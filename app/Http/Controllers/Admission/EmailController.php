<?php

namespace App\Http\Controllers\Admission;

use App\Http\Controllers\Controller;
use App\Mail\ApplicantAccountCreatedMail;
use App\Mail\ExamScheduleAssignedMail;
use App\Models\Applicant;
use App\Services\EmailAuditService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class EmailController extends Controller
{
    public function __construct()
    {
        // Apply rate limiting to email resend endpoints
        $this->middleware('rate-limit-email-resends', [
            'only' => ['sendCredentials', 'sendSchedule']
        ]);
    }

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

            $mailable = new ApplicantAccountCreatedMail(
                $applicant,
                $username,
                $password,
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

            return back()->with('success', 'Login credentials email sent successfully!');
        } catch (\Exception $e) {
            // Log the failure
            EmailAuditService::logFailed(
                ApplicantAccountCreatedMail::class,
                $applicant->email,
                'Login Credentials',
                $e->getMessage(),
                $applicant->app_ref_no
            );
            
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
            $mailable = new ExamScheduleAssignedMail(
                $applicant,
                $schedule,
                $exam->title,
                $campusName
            );

            Mail::to($applicant->email)->queue($mailable);

            // Log the email send for audit
            EmailAuditService::logQueued(
                ExamScheduleAssignedMail::class,
                $applicant->email,
                $mailable->envelope()->subject,
                $applicant->app_ref_no
            );

            return back()->with('success', 'Exam schedule email sent successfully!');
        } catch (\Exception $e) {
            // Log the failure
            EmailAuditService::logFailed(
                ExamScheduleAssignedMail::class,
                $applicant->email,
                'Exam Schedule',
                $e->getMessage(),
                $applicant->app_ref_no
            );
            
            return back()->with('error', 'Failed to send email: ' . $e->getMessage());
        }
    }
}

