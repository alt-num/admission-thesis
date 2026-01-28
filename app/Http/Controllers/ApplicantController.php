<?php

namespace App\Http\Controllers;

use App\Mail\ApplicantAccountCreatedMail;
use App\Mail\ApplicationNeedsRevisionMail;
use App\Mail\ExamScheduleAssignedMail;
use App\Mail\PhotoRejectedMail;
use App\Models\Applicant;
use App\Models\ApplicantExamSchedule;
use App\Models\ApplicantUser;
use App\Models\AntiCheatLog;
use App\Models\Campus;
use App\Models\Course;
use App\Models\Exam;
use App\Models\ExamSchedule;
use App\Services\EmailAuditService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ApplicantController extends Controller
{
    /**
     * Display a listing of applicants (for admission users).
     */
    public function index(Request $request)
    {
        $search = $request->query('search');

        $applicants = Applicant::with(['campus', 'preferredCourse1', 'preferredCourse2', 'preferredCourse3', 'applicantUser', 'courseResults', 'examAttempts', 'examSchedules.examSchedule'])
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

        // Auto-evaluate results for each applicant
        foreach ($applicants as $applicant) {
            $applicant->evaluateResultsIfNeeded();
        }

        return view('admission.applicants.index', compact('applicants'));
    }

    /**
     * Show the form for creating a new applicant.
     */
    public function create()
    {
        $campuses = Campus::orderBy('campus_name')->get();

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

        return view('admission.applicants.create', compact('campuses', 'defaultCampusId', 'defaultSchoolYear', 'schedules', 'activeExam'));
    }

    /**
     * Store a newly created applicant.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email|unique:applicants,email',
            'campus_id' => 'required|exists:campuses,campus_id',
            'school_year' => 'required|string',
            'schedule_id' => 'nullable|exists:exam_schedules,schedule_id',
        ]);

        // Generate app_ref_no automatically
        $appRefNo = $this->generateAppRefNo($validated['campus_id'], $validated['school_year']);

        // Get the campus for later use
        $campus = Campus::findOrFail($validated['campus_id']);

        // Auto-generate placeholder names
        $firstName = 'Applicant';
        $lastName = 'Ref-' . $appRefNo;

        // Create applicant with placeholder names
        $applicant = Applicant::create([
            'app_ref_no' => $appRefNo,
            'first_name' => $firstName,
            'middle_name' => null,
            'last_name' => $lastName,
            'email' => $validated['email'],
            'campus_id' => $validated['campus_id'],
            'school_year' => $validated['school_year'],
            'preferred_course_1' => null,
            'preferred_course_2' => null,
            'preferred_course_3' => null,
            'status' => 'Pending',
            'registered_by' => Auth::guard('admission')->id(),
        ]);

        // Generate username (using app_ref_no)
        $username = strtolower($appRefNo);
        
        // Generate deterministic password based on applicant reference number
        // Format: bor_YY_NNNNN (e.g., "bor_25_00002" from "BOR-2500002")
        $password = generateApplicantPassword($appRefNo);

        // Create ApplicantUser
        ApplicantUser::create([
            'applicant_id' => $applicant->applicant_id,
            'username' => $username,
            'password' => Hash::make($password),
            'plain_password' => $password,
            'account_status' => 'active',
        ]);

        // Send account creation email
        Mail::to($applicant->email)->queue(
            new ApplicantAccountCreatedMail(
                $applicant,
                $username,
                $password,
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
            Mail::to($applicant->email)->queue(
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
            ->with('success', "Applicant registered successfully! Login credentials have been sent to the their email.");
    }

    /**
     * Display the specified applicant.
     */ 
    public function show(Applicant $applicant)
    {
        // Auto-evaluate results before loading
        $applicant->evaluateResultsIfNeeded();

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

        // Load anti-cheat logs for this applicant, sorted by timestamp descending
        $antiCheatLogs = AntiCheatLog::where('applicant_id', $applicant->applicant_id)
            ->with('examAttempt.exam')
            ->orderBy('event_timestamp', 'desc')
            ->get();

        $eligibility = $this->computeCourseEligibility($applicant);

        return view('admission.applicants.show', compact('applicant', 'eligibility', 'antiCheatLogs'));
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
     * Reset applicant username and password.
     */
    public function resetCredentials(Applicant $applicant)
    {
        // Check if user is Admin or Staff
        $user = Auth::guard('admission')->user();
        if (!$user || !in_array($user->role, ['Admin', 'Staff'])) {
            abort(403, 'Unauthorized access.');
        }

        // Validate that applicant has email
        if (!$applicant->email) {
            return back()->with('error', 'Applicant does not have an email address.');
        }

        // Validate that applicant has user account
        if (!$applicant->applicantUser) {
            return back()->with('error', 'Applicant does not have a user account.');
        }

        // Load campus for city code
        $applicant->load('campus');
        $campus = $applicant->campus;
        if (!$campus) {
            return back()->with('error', 'Applicant does not have an associated campus.');
        }

        // Regenerate username (same format: lowercase app_ref_no)
        $username = strtolower($applicant->app_ref_no);

        // Check for username uniqueness (excluding current applicant user)
        $existingUser = ApplicantUser::where('username', $username)
            ->where('user_id', '!=', $applicant->applicantUser->user_id)
            ->first();
        
        if ($existingUser) {
            return back()->with('error', 'Username already exists. Please contact system administrator.');
        }

        // Generate new deterministic password based on applicant reference number
        // Format: bor_YY_NNNNN (e.g., "bor_25_00002" from "BOR-2500002")
        $newPassword = generateApplicantPassword($applicant->app_ref_no);

        // Hash password before saving
        $hashedPassword = Hash::make($newPassword);

        // Update database
        $applicant->applicantUser->update([
            'username' => $username,
            'password' => $hashedPassword,
            'plain_password' => $newPassword,
        ]);

        // Re-send email notification with new credentials
        try {
            $campusName = $campus->campus_name ?? 'N/A';
            $mailable = new ApplicantAccountCreatedMail(
                $applicant,
                $username,
                $newPassword,
                $campusName
            );

            Mail::to($applicant->email)->queue($mailable);

            // Log the email send for audit
            EmailAuditService::logQueued(
                ApplicantAccountCreatedMail::class,
                $applicant->email,
                $mailable->envelope()->subject,
                $applicant->app_ref_no
            );
        } catch (\Exception $e) {
            // Log the failure
            EmailAuditService::logFailed(
                ApplicantAccountCreatedMail::class,
                $applicant->email,
                'Reset Credentials',
                $e->getMessage(),
                $applicant->app_ref_no
            );
            return back()->with('warning', 'Credentials reset successfully, but email notification failed to send.');
        }

        return back()->with('success', "Credentials reset successfully. New credentials have been sent to {$applicant->email}");
    }

    /**
     * Request applicant to submit a new photo.
     */
    public function requestNewPhoto(Applicant $applicant)
    {
        // Check if applicant has taken exam
        if ($applicant->examAttempts()->exists()) {
            return back()->with('error', 'Cannot request new photo. Applicant has already taken the exam.');
        }

        // Delete old photo file if it exists
        if ($applicant->photo_path && Storage::disk('public')->exists($applicant->photo_path)) {
            Storage::disk('public')->delete($applicant->photo_path);
        }

        // Set photo_path to null
        $applicant->photo_path = null;
        $applicant->save();

        // Send email notification
        try {
            Mail::to($applicant->email)->queue(
                new PhotoRejectedMail($applicant)
            );
        } catch (\Exception $e) {
            \Log::error('Failed to send photo rejection email: ' . $e->getMessage());
            return back()->with('warning', 'Photo reset successfully, but email notification failed to send.');
        }

        return back()->with('success', 'Photo reset request sent. Applicant must upload a new photo.');
    }

    /**
     * Update applicant email (only if profile is not complete).
     */
    public function updateEmail(Request $request, Applicant $applicant)
    {
        // Check if user is Admin or Staff
        $user = Auth::guard('admission')->user();
        if (!$user || !in_array($user->role, ['Admin', 'Staff'])) {
            abort(403, 'Unauthorized access.');
        }

        $request->validate([
            'email' => 'required|email|max:255|unique:applicants,email,' . $applicant->applicant_id . ',applicant_id'
        ]);

        // Check if profile is complete - if so, email cannot be changed
        if ($applicant->isProfileComplete()) {
            return back()->with('error', 'Email can no longer be changed after the applicant has logged in.');
        }

        // Update email in applicants table
        $applicant->email = $request->email;
        $applicant->save();

        // Resend login credentials to the new email
        if ($applicant->applicantUser) {
            try {
                $campusName = $applicant->campus->campus_name ?? 'N/A';
                $username = $applicant->applicantUser->username;
                $password = $applicant->applicantUser->plain_password;

                // If plain_password is not set (legacy accounts), generate a new one
                if (!$password) {
                    $password = generateApplicantPassword($applicant->app_ref_no);
                    $applicant->applicantUser->update([
                        'password' => Hash::make($password),
                        'plain_password' => $password,
                    ]);
                }

                Mail::to($applicant->email)->queue(
                    new ApplicantAccountCreatedMail(
                        $applicant,
                        $username,
                        $password,
                        $campusName
                    )
                );
            } catch (\Exception $e) {
                \Log::error('Failed to send email update notification: ' . $e->getMessage());
                return back()->with('warning', 'Email updated, but failed to send login credentials. Please resend manually.');
            }
        }

        return back()->with('success', 'Email updated and login credentials resent.');
    }

    /**
     * Return applicant's form for revision.
     */
    public function returnForRevision(Applicant $applicant)
    {
        // Check if applicant exists
        if (!$applicant) {
            abort(404, 'Applicant not found.');
        }

        // Reset password when returning for revision
        if ($applicant->applicantUser) {
            $newPassword = generateApplicantPassword($applicant->app_ref_no);
            $applicant->applicantUser->update([
                'password' => Hash::make($newPassword),
                'plain_password' => $newPassword,
            ]);
        }

        // Set needs_revision flag
        $applicant->needs_revision = true;
        $applicant->save();

        // Send email notification
        try {
            Mail::to($applicant->email)->queue(
                new ApplicationNeedsRevisionMail($applicant)
            );
        } catch (\Exception $e) {
            \Log::error('Failed to send revision email: ' . $e->getMessage());
            return back()->with('warning', 'Application returned for revision, but email notification failed to send.');
        }

        return back()->with('success', 'Application returned for revision. Applicant has been notified.');
    }

    /**
     * Compute course eligibility based on exam scores.
     */
    private function computeCourseEligibility(Applicant $applicant): array
    {
        // Get the most recent exam attempt
        $examAttempt = $applicant->examAttempts()
            ->with(['exam.sections' => function($query) {
                $query->orderBy('order_no');
            }, 'subsectionScores.subsection.section'])
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

        // Get subsection scores grouped by section
        $subsections = [];
        $subsectionScores = $examAttempt->subsectionScores()
            ->with('subsection.section')
            ->get();

        // Group subsection scores by section for calculation
        $subsectionScoresBySection = [];
        foreach ($subsectionScores as $subsectionScore) {
            $subsection = $subsectionScore->subsection;
            $section = $subsection->section;
            
            if (!$section) {
                continue;
            }
            
            $sectionId = $section->section_id;
            if (!isset($subsectionScoresBySection[$sectionId])) {
                $subsectionScoresBySection[$sectionId] = [
                    'section' => $section,
                    'scores' => [],
                ];
            }
            
            $subsectionScoresBySection[$sectionId]['scores'][] = (float) $subsectionScore->score;
            
            // Also build subsections array for display
            $sectionName = $section->name;
            if (!isset($subsections[$sectionName])) {
                $subsections[$sectionName] = [];
            }
            
            $subsections[$sectionName][] = [
                'name' => $subsection->name,
                'score' => (float) $subsectionScore->score,
            ];
        }

        // Calculate section scores from actual exam sections
        $sections = [];
        if ($examAttempt->exam && $examAttempt->exam->sections) {
            foreach ($examAttempt->exam->sections as $section) {
                $sectionId = $section->section_id;
                
                // Calculate section score as average of its subsection scores
                $sectionScore = null;
                if (isset($subsectionScoresBySection[$sectionId]) && !empty($subsectionScoresBySection[$sectionId]['scores'])) {
                    $scores = $subsectionScoresBySection[$sectionId]['scores'];
                    $sectionScore = count($scores) > 0 ? array_sum($scores) / count($scores) : 0;
                }
                
                // Only include sections that have subsection scores
                if ($sectionScore !== null) {
                    $sections[] = [
                        'name' => $section->name,
                        'score' => round($sectionScore, 2),
                    ];
                }
            }
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

    /**
     * Toggle flagged status for an applicant.
     */
    public function toggleFlagged(Applicant $applicant)
    {
        $applicant->status = ($applicant->status === 'Flagged')
            ? 'Pending'
            : 'Flagged';
        $applicant->save();

        return back()->with('success', 'Applicant flagged status updated.');
    }

    /**
     * Toggle account status for an applicant user.
     */
    public function toggleAccountStatus(Applicant $applicant)
    {
        if (!$applicant->applicantUser) {
            return back()->with('error', 'Applicant does not have a user account.');
        }

        $user = $applicant->applicantUser;
        $user->account_status = ($user->account_status === 'active')
            ? 'disabled'
            : 'active';
        $user->save();

        return back()->with('success', 'Applicant account status updated.');
    }

    /**
     * Generate application reference number automatically.
     * Format: <CITY_CODE>-<YY><#####>
     * Example: BOR-2500001
     */
    private function generateAppRefNo($campusId, $schoolYear)
    {
        // A) Get city code from campus
        $cityCode = Campus::findOrFail($campusId)->city_code;

        // B) Extract school year short (e.g., "2025-2026" → "25")
        $yearShort = substr($schoolYear, 2, 2);

        // C) Find the last applicant for the same campus + same school year
        $last = Applicant::where('campus_id', $campusId)
                         ->where('school_year', $schoolYear)
                         ->orderBy('applicant_id', 'desc')
                         ->first();

        // D) Determine next sequence
        if ($last) {
            // Extract last 5 digits from $last->app_ref_no and increment
            $lastSequence = (int) substr($last->app_ref_no, -5);
            $next = $lastSequence + 1;
        } else {
            // Start at 1
            $next = 1;
        }

        // E) Zero-pad to 5 digits
        $numPadded = str_pad($next, 5, '0', STR_PAD_LEFT);

        // F) Return formatted reference number
        return "{$cityCode}-{$yearShort}{$numPadded}";
    }
}
