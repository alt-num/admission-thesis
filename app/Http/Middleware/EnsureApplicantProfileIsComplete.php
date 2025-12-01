<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureApplicantProfileIsComplete
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $applicantUser = auth()->guard('applicant')->user();
        
        if (!$applicantUser) {
            return redirect()->route('login');
        }

        $applicant = $applicantUser->applicant;

        // Check if required profile fields are missing
        $requiredFields = [
            'first_name',
            'last_name',
            'birth_date',
            'place_of_birth',
            'sex',
            'civil_status',
            'email',
            'contact_number',
            'barangay',
            'municipality',
            'province',
            'last_school_attended',
            'school_address',
            'year_graduated',
            'gen_average',
            'preferred_course_1',
            'preferred_course_2',
            'preferred_course_3',
        ];

        foreach ($requiredFields as $field) {
            if (empty($applicant->$field)) {
                return redirect()->route('applicant.profile.show')
                    ->with('warning', 'Please complete your profile before proceeding.');
            }
        }

        // Check if photo is uploaded (required before exam)
        if (empty($applicant->photo_path)) {
            return redirect()->route('applicant.profile.show')
                ->with('warning', 'Please upload your ID photo to continue.');
        }

        // Check if application needs revision
        $hasExamAttempt = $applicant->examAttempts()->exists();
        if ($applicant->needs_revision) {
            if (!$hasExamAttempt) {
                // Before exam: must fix form before taking exam
                return redirect()->route('applicant.profile.show')
                    ->with('warning', 'Please correct your application form before taking the exam.');
            }
            // After exam: allow dashboard access (only minor fields editable)
        }

        // Check if declaration is missing or incomplete
        $declaration = $applicant->declaration;
        
        if (!$declaration) {
            return redirect()->route('applicant.declaration.edit')
                ->with('warning', 'Please complete your declaration before proceeding.');
        }

        $requiredDeclarationFields = [
            'physical_condition_flag',
            'disciplinary_action_flag',
            'certified_signature_name',
            'certified_date',
        ];

        foreach ($requiredDeclarationFields as $field) {
            if (!isset($declaration->$field)) {
                return redirect()->route('applicant.declaration.edit')
                    ->with('warning', 'Please complete your declaration before proceeding.');
            }
        }

        // Check conditional fields
        if ($declaration->physical_condition_flag && empty($declaration->physical_condition_desc)) {
            return redirect()->route('applicant.declaration.edit')
                ->with('warning', 'Please provide details about your physical condition.');
        }

        if ($declaration->disciplinary_action_flag && empty($declaration->disciplinary_action_desc)) {
            return redirect()->route('applicant.declaration.edit')
                ->with('warning', 'Please provide details about the disciplinary action.');
        }

        return $next($request);
    }
}

