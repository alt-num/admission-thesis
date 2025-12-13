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
            
            // Use existing plain_password from database
            $password = $applicant->applicantUser->plain_password;
            
            // If plain_password is not set (legacy accounts), generate a new one
            if (!$password) {
                $password = generateRandomPassword();
                $applicant->applicantUser->update([
                    'password' => Hash::make($password),
                    'plain_password' => $password,
                ]);
            }
            
            $campusName = $applicant->campus->campus_name ?? 'N/A';

            Mail::to($applicant->email)->queue(
                new ApplicantAccountCreatedMail(
                    $applicant,
                    $username,
                    $password,
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
            Mail::to($applicant->email)->queue(
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

