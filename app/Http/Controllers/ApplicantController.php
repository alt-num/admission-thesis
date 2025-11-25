<?php

namespace App\Http\Controllers;

use App\Mail\ApplicantAccountCreatedMail;
use App\Mail\ExamScheduleAssignedMail;
use App\Models\Applicant;
use App\Models\ApplicantExamSchedule;
use App\Models\ApplicantUser;
use App\Models\Campus;
use App\Models\Course;
use App\Models\Exam;
use App\Models\ExamSchedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class ApplicantController extends Controller
{
    /**
     * Display a listing of applicants (for admission users).
     */
    public function index(Request $request)
    {
        $search = $request->query('search');

        $applicants = Applicant::with(['campus', 'preferredCourse1', 'preferredCourse2', 'preferredCourse3', 'applicantUser'])
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('app_ref_no', 'ilike', "%{$search}%")
                      ->orWhere('first_name', 'ilike', "%{$search}%")
                      ->orWhere('last_name', 'ilike', "%{$search}%")
                      ->orWhereHas('campus', function ($campusQuery) use ($search) {
                          $campusQuery->where('campus_name', 'ilike', "%{$search}%");
                      })
                      ->orWhereRaw("DATE(created_at)::text ILIKE ?", ["%{$search}%"]);
                });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admission.applicants.index', compact('applicants'));
    }

    /**
     * Show the form for creating a new applicant.
     */
    public function create()
    {
        $campuses = Campus::orderBy('campus_name')->get();
        $courses = Course::with('department')->orderBy('course_name')->get();

        // Get MAIN campus ID for default selection
        $mainCampus = Campus::where('campus_code', 'MAIN')->first();
        $defaultCampusId = $mainCampus ? $mainCampus->campus_id : null;

        // Calculate current academic year (e.g., 2025-2026)
        $currentYear = (int) date('Y');
        $nextYear = $currentYear + 1;
        $defaultSchoolYear = "{$currentYear}-{$nextYear}";

        // Get active exam and its schedules
        $activeExam = Exam::where('is_active', true)->first();
        $schedules = collect();
        
        if ($activeExam) {
            $schedules = ExamSchedule::where('exam_id', $activeExam->exam_id)
                ->orderBy('schedule_date')
                ->orderBy('start_time')
                ->get();
        }

        return view('admission.applicants.create', compact('campuses', 'courses', 'defaultCampusId', 'defaultSchoolYear', 'schedules', 'activeExam'));
    }

    /**
     * Store a newly created applicant.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:applicants,email',
            'campus_id' => 'required|exists:campuses,campus_id',
            'school_year' => 'required|string',
            'preferred_course_1' => 'nullable|exists:courses,course_id',
            'preferred_course_2' => 'nullable|exists:courses,course_id',
            'preferred_course_3' => 'nullable|exists:courses,course_id',
            'schedule_id' => 'nullable|exists:exam_schedules,schedule_id',
        ]);

        // Get the campus to generate app_ref_no
        $campus = Campus::findOrFail($validated['campus_id']);

        // Generate unique app_ref_no using the new format
        $appRefNo = Applicant::generateRefNumber($campus);

        // Create applicant
        $applicant = Applicant::create([
            'app_ref_no' => $appRefNo,
            'first_name' => $validated['first_name'],
            'middle_name' => $validated['middle_name'] ?? null,
            'last_name' => $validated['last_name'],
            'email' => $validated['email'],
            'campus_id' => $validated['campus_id'],
            'school_year' => $validated['school_year'],
            'preferred_course_1' => $validated['preferred_course_1'] ?? null,
            'preferred_course_2' => $validated['preferred_course_2'] ?? null,
            'preferred_course_3' => $validated['preferred_course_3'] ?? null,
            'status' => 'Pending',
            'registered_by' => Auth::guard('admission')->id(),
        ]);

        // Generate username (using app_ref_no)
        $username = strtolower($appRefNo);
        $defaultPassword = strtolower($appRefNo);

        // Create ApplicantUser
        ApplicantUser::create([
            'applicant_id' => $applicant->applicant_id,
            'username' => $username,
            'password' => Hash::make($defaultPassword),
            'account_status' => 'Active',
        ]);

        // Send account creation email
        Mail::to($applicant->email)->send(
            new ApplicantAccountCreatedMail(
                $applicant,
                $username,
                $defaultPassword,
                $campus->campus_name
            )
        );

        // Assign to schedule if selected
        $scheduleId = $validated['schedule_id'] ?? null;
        if ($scheduleId) {
            $schedule = ExamSchedule::withCount('applicantExamSchedules')
                ->with('exam')
                ->find($scheduleId);
            
            // Check capacity
            if ($schedule->capacity !== null && $schedule->applicant_exam_schedules_count >= $schedule->capacity) {
                return back()
                    ->withErrors(['schedule_id' => 'This schedule is already full.'])
                    ->withInput();
            }

            // Create assignment
            ApplicantExamSchedule::create([
                'applicant_id' => $applicant->applicant_id,
                'schedule_id' => $scheduleId,
                'assigned_at' => now(),
            ]);

            // Send exam schedule assignment email
            Mail::to($applicant->email)->send(
                new ExamScheduleAssignedMail(
                    $applicant,
                    $schedule,
                    $schedule->exam->title,
                    $campus->campus_name
                )
            );
        }

        return redirect()
            ->route('admission.applicants.index')
            ->with('success', "Applicant registered successfully! Username: {$username}, Password: {$defaultPassword}");
    }

    /**
     * Display the specified applicant.
     */
    public function show(Applicant $applicant)
    {
        $applicant->load([
            'campus',
            'preferredCourse1',
            'preferredCourse2',
            'preferredCourse3',
            'declaration',
            'examAttempts.exam',
            'examAttempts.subsectionScores.subsection.section',
            'courseResults.course',
            'applicantUser',
            'examSchedules.examSchedule.exam'
        ]);

        $eligibility = $this->computeCourseEligibility($applicant);

        return view('admission.applicants.show', compact('applicant', 'eligibility'));
    }

    /**
     * Show the form for editing the specified applicant.
     */
    public function edit(Applicant $applicant)
    {
        $applicant->load(['campus', 'preferredCourse1', 'preferredCourse2', 'preferredCourse3']);
        $courses = Course::with('department')->orderBy('course_name')->get();

        return view('admission.applicants.edit', compact('applicant', 'courses'));
    }

    /**
     * Update the specified applicant in storage.
     */
    public function update(Request $request, Applicant $applicant)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:applicants,email,' . $applicant->applicant_id . ',applicant_id',
            'contact_number' => 'nullable|string|max:32',
            'preferred_course_1' => 'nullable|exists:courses,course_id',
            'preferred_course_2' => 'nullable|exists:courses,course_id',
            'preferred_course_3' => 'nullable|exists:courses,course_id',
        ]);

        $applicant->update([
            'first_name' => $validated['first_name'],
            'middle_name' => $validated['middle_name'] ?? null,
            'last_name' => $validated['last_name'],
            'email' => $validated['email'],
            'contact_number' => $validated['contact_number'] ?? null,
            'preferred_course_1' => $validated['preferred_course_1'] ?? null,
            'preferred_course_2' => $validated['preferred_course_2'] ?? null,
            'preferred_course_3' => $validated['preferred_course_3'] ?? null,
        ]);

        return redirect()
            ->route('admission.applicants.show', $applicant)
            ->with('success', 'Applicant updated successfully!');
    }

    /**
     * Display the declaration for the specified applicant.
     */
    public function declarationViewing(Applicant $applicant)
    {
        $applicant->load('declaration');

        return view('admission.applicants.declaration', compact('applicant'));
    }

    /**
     * Compute course eligibility based on exam scores.
     */
    private function computeCourseEligibility(Applicant $applicant): array
    {
        // Get the most recent exam attempt
        $examAttempt = $applicant->examAttempts()
            ->with(['exam', 'subsectionScores.subsection.section'])
            ->latest('started_at')
            ->first();

        if (!$examAttempt) {
            return [
                'total_score' => null,
                'sections' => [],
                'subsections' => [],
                'courses' => [],
                'final_recommendation' => null,
            ];
        }

        // Get section scores from exam_attempts
        $sections = [];
        if ($examAttempt->score_verbal !== null) {
            $sections[] = [
                'name' => 'Verbal',
                'score' => (float) $examAttempt->score_verbal,
            ];
        }
        if ($examAttempt->score_nonverbal !== null) {
            $sections[] = [
                'name' => 'Nonverbal',
                'score' => (float) $examAttempt->score_nonverbal,
            ];
        }

        // Get subsection scores grouped by section
        $subsections = [];
        $subsectionScores = $examAttempt->subsectionScores()
            ->with('subsection.section')
            ->get();

        foreach ($subsectionScores as $subsectionScore) {
            $subsection = $subsectionScore->subsection;
            $section = $subsection->section;
            
            $sectionName = $section->name ?? 'Unknown';
            if (!isset($subsections[$sectionName])) {
                $subsections[$sectionName] = [];
            }
            
            $subsections[$sectionName][] = [
                'name' => $subsection->name,
                'score' => (float) $subsectionScore->score,
            ];
        }

        // Get preferred courses and check eligibility
        $courses = [];
        $preferredCourses = [
            1 => $applicant->preferredCourse1,
            2 => $applicant->preferredCourse2,
            3 => $applicant->preferredCourse3,
        ];

        foreach ($preferredCourses as $priority => $course) {
            if (!$course) {
                continue;
            }

            $totalScore = (float) $examAttempt->score_total;
            $passingScore = $course->passing_score;
            
            // If passing_score is null, automatically FAIL
            $passed = false;
            if ($passingScore !== null) {
                $passed = $totalScore >= $passingScore;
            }

            $courses[] = [
                'course' => $course,
                'passed' => $passed,
                'required' => $passingScore,
                'priority' => $priority,
            ];
        }

        // Determine final recommendation (highest priority passed course)
        $finalRecommendation = null;
        foreach ($courses as $courseData) {
            if ($courseData['passed']) {
                $finalRecommendation = $courseData['course']->course_code;
                break;
            }
        }

        return [
            'total_score' => (float) $examAttempt->score_total,
            'sections' => $sections,
            'subsections' => $subsections,
            'courses' => $courses,
            'final_recommendation' => $finalRecommendation,
        ];
    }
}
