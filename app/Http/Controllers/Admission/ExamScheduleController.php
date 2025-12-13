<?php

namespace App\Http\Controllers\Admission;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admission\Exam\Scheduling\StoreExamScheduleRequest;
use App\Http\Requests\Admission\Exam\Scheduling\UpdateExamScheduleRequest;
use App\Mail\ExamScheduleAssignedMail;
use App\Models\Applicant;
use App\Models\ApplicantExamSchedule;
use App\Models\Exam;
use App\Models\ExamSchedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ExamScheduleController extends Controller
{
    /**
     * Display a listing of schedules for an exam.
     */
    public function index(Exam $exam)
    {
        $schedules = $exam->schedules()
            ->orderBy('schedule_date')
            ->orderBy('start_time')
            ->get()
            ->map(function ($schedule) {
                $assignedCount = $schedule->applicantExamSchedules()->count();
                $schedule->assigned_count = $assignedCount;
                $schedule->remaining_slots = $schedule->capacity ? $schedule->capacity - $assignedCount : null;
                return $schedule;
            });

        return view('admission.exams.schedules.index', compact('exam', 'schedules'));
    }

    /**
     * Store a newly created schedule.
     */
    public function store(Exam $exam, StoreExamScheduleRequest $request)
    {
        $exam->schedules()->create([
            'schedule_date' => $request->schedule_date,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'capacity' => $request->capacity,
            'location' => $request->location,
            'exam_code' => $this->generateExamCode(),
            'anti_cheat_enabled' => $request->has('anti_cheat_enabled') ? (bool)$request->anti_cheat_enabled : true,
        ]);

        return redirect()
            ->route('admission.exams.schedules.index', $exam)
            ->with('success', 'Schedule created successfully!');
    }

    /**
     * Display the specified schedule with assignment interface.
     */
    public function show(Exam $exam, ExamSchedule $schedule, Request $request)
    {
        // Ensure schedule belongs to exam
        if ($schedule->exam_id !== $exam->exam_id) {
            abort(404);
        }

        // Load assigned applicants with their relationships
        $assignedApplicants = $schedule->applicantExamSchedules()
            ->with('applicant.campus')
            ->get()
            ->map(fn($assignment) => $assignment->applicant);

        // Get IDs of applicants already assigned to ANY schedule of this exam
        $assignedApplicantIds = ApplicantExamSchedule::whereHas('examSchedule', function ($query) use ($exam) {
            $query->where('exam_id', $exam->exam_id);
        })->pluck('applicant_id')->toArray();

        // Get search term
        $search = $request->query('search');

        // Get eligible applicants (Pending and not assigned to any schedule of this exam)
        $eligibleApplicants = Applicant::where('status', 'Pending')
            ->whereNotIn('applicant_id', $assignedApplicantIds)
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('app_ref_no', 'ilike', "%{$search}%")
                      ->orWhere('first_name', 'ilike', "%{$search}%")
                      ->orWhere('last_name', 'ilike', "%{$search}%")
                      ->orWhereRaw("CONCAT(first_name, ' ', last_name) ILIKE ?", ["%{$search}%"]);
                });
            })
            ->with('campus')
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->paginate(25)
            ->withQueryString();

        return view('admission.exams.schedules.show', compact('exam', 'schedule', 'assignedApplicants', 'eligibleApplicants', 'search'));
    }

    /**
     * Update the specified schedule.
     */
    public function update(Exam $exam, ExamSchedule $schedule, UpdateExamScheduleRequest $request)
    {
        // Ensure schedule belongs to exam
        if ($schedule->exam_id !== $exam->exam_id) {
            abort(404);
        }

        $schedule->update([
            'schedule_date' => $request->schedule_date,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'capacity' => $request->capacity,
            'location' => $request->location,
            'anti_cheat_enabled' => $request->has('anti_cheat_enabled') ? (bool)$request->anti_cheat_enabled : true,
        ]);

        return redirect()
            ->route('admission.exams.schedules.show', [$exam, $schedule])
            ->with('success', 'Schedule updated successfully!');
    }

    /**
     * Remove the specified schedule.
     */
    public function destroy(Exam $exam, ExamSchedule $schedule)
    {
        // Ensure schedule belongs to exam
        if ($schedule->exam_id !== $exam->exam_id) {
            abort(404);
        }

        // Delete related ApplicantExamSchedule records
        $schedule->applicantExamSchedules()->delete();

        // Delete the schedule
        $schedule->delete();

        return redirect()
            ->route('admission.exams.schedules.index', $exam)
            ->with('success', 'Schedule deleted successfully!');
    }

    /**
     * Assign applicants to a schedule.
     */
    public function assignApplicants(Exam $exam, ExamSchedule $schedule, Request $request)
    {
        // Ensure schedule belongs to exam
        if ($schedule->exam_id !== $exam->exam_id) {
            abort(404);
        }

        $request->validate([
            'applicants' => ['required', 'array', 'min:1'],
            'applicants.*' => ['exists:applicants,applicant_id'],
        ]);

        $applicantIds = $request->applicants;

        // Get IDs of applicants already assigned to ANY schedule of this exam
        $assignedApplicantIds = ApplicantExamSchedule::whereHas('examSchedule', function ($query) use ($exam) {
            $query->where('exam_id', $exam->exam_id);
        })->pluck('applicant_id')->toArray();

        // Filter out applicants who are already assigned
        $applicantIds = array_diff($applicantIds, $assignedApplicantIds);

        // Verify all applicants have status 'Pending'
        $applicantIds = Applicant::whereIn('applicant_id', $applicantIds)
            ->where('status', 'Pending')
            ->pluck('applicant_id')
            ->toArray();

        if (empty($applicantIds)) {
            return back()->with('error', 'No eligible applicants to assign.');
        }

        // Check capacity if set
        if ($schedule->capacity !== null) {
            $currentAssigned = $schedule->applicantExamSchedules()->count();
            $availableSlots = $schedule->capacity - $currentAssigned;

            if ($availableSlots <= 0) {
                return back()->with('error', 'This schedule is at full capacity.');
            }

            if (count($applicantIds) > $availableSlots) {
                // Only assign as many as fit
                $applicantIds = array_slice($applicantIds, 0, $availableSlots);
            }
        }

        // Load schedule with exam relationship
        $schedule->load('exam');

        // Create ApplicantExamSchedule records and send emails
        foreach ($applicantIds as $applicantId) {
            ApplicantExamSchedule::create([
                'applicant_id' => $applicantId,
                'schedule_id' => $schedule->schedule_id,
                'assigned_at' => now(),
            ]);

            // Load applicant with campus relationship
            $applicant = Applicant::with('campus')->find($applicantId);

            // Send exam schedule assignment email
            Mail::to($applicant->email)->queue(
                new ExamScheduleAssignedMail(
                    $applicant,
                    $schedule,
                    $schedule->exam->title,
                    $applicant->campus->campus_name
                )
            );
        }

        $count = count($applicantIds);
        return redirect()
            ->route('admission.exams.schedules.show', [$exam, $schedule])
            ->with('success', "{$count} applicant(s) assigned successfully!");
    }

    /**
     * Unassign an applicant from a schedule.
     */
    public function unassignApplicant(Exam $exam, ExamSchedule $schedule, Applicant $applicant)
    {
        // Ensure schedule belongs to exam
        if ($schedule->exam_id !== $exam->exam_id) {
            abort(404);
        }

        ApplicantExamSchedule::where('applicant_id', $applicant->applicant_id)
            ->where('schedule_id', $schedule->schedule_id)
            ->delete();

        return redirect()
            ->route('admission.exams.schedules.show', [$exam, $schedule])
            ->with('success', 'Applicant unassigned successfully!');
    }

    /**
     * Generate a new exam code for a schedule.
     */
    public function generateCode(Exam $exam, ExamSchedule $schedule)
    {
        // Ensure schedule belongs to exam
        if ($schedule->exam_id !== $exam->exam_id) {
            abort(404);
        }

        $schedule->update([
            'exam_code' => $this->generateExamCode(),
        ]);

        return redirect()
            ->route('admission.exams.schedules.show', [$exam, $schedule])
            ->with('success', 'New exam code generated successfully!');
    }

    /**
     * Generate a random 4-5 character alphanumeric exam code.
     */
    private function generateExamCode(): string
    {
        $length = rand(4, 5); // Random length between 4 and 5
        $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $code = '';
        
        for ($i = 0; $i < $length; $i++) {
            $code .= $characters[rand(0, strlen($characters) - 1)];
        }
        
        return $code;
    }
}
