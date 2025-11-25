<?php

namespace App\Http\Controllers\Admission;

use App\Http\Controllers\Controller;
use App\Mail\ApplicantAccountCreatedMail;
use App\Mail\ExamScheduleAssignedMail;
use App\Models\Applicant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class EmailController extends Controller
{
    /**
     * Send login credentials to applicant.
     */
    public function sendCredentials(Applicant $applicant)
    {
        // Validate that applicant has email
        if (!$applicant->email) {
            return back()->with('error', 'Applicant does not have an email address.');
        }

        // Validate that applicant has user account
        if (!$applicant->applicantUser) {
            return back()->with('error', 'Applicant does not have a user account.');
        }

        try {
            $username = $applicant->applicantUser->username;
            // Temporary password is the same as username (or app_ref_no)
            $temporaryPassword = $username;
            $campusName = $applicant->campus->campus_name ?? 'N/A';

            Mail::to($applicant->email)->send(
                new ApplicantAccountCreatedMail(
                    $applicant,
                    $username,
                    $temporaryPassword,
                    $campusName
                )
            );

            return back()->with('success', 'Login credentials email sent successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to send email: ' . $e->getMessage());
        }
    }

    /**
     * Send exam schedule notification to applicant.
     */
    public function sendSchedule(Applicant $applicant)
    {
        // Validate that applicant has email
        if (!$applicant->email) {
            return back()->with('error', 'Applicant does not have an email address.');
        }

        // Get the most recent exam schedule assignment
        $applicantExamSchedule = $applicant->examSchedules()
            ->with('examSchedule.exam')
            ->latest('assigned_at')
            ->first();

        if (!$applicantExamSchedule) {
            return back()->with('error', 'Applicant is not assigned to any exam schedule.');
        }

        $schedule = $applicantExamSchedule->examSchedule;
        $exam = $schedule->exam;
        $campusName = $applicant->campus->campus_name ?? 'N/A';

        try {
            Mail::to($applicant->email)->send(
                new ExamScheduleAssignedMail(
                    $applicant,
                    $schedule,
                    $exam->title,
                    $campusName
                )
            );

            return back()->with('success', 'Exam schedule email sent successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to send email: ' . $e->getMessage());
        }
    }
}

