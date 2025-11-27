<?php

namespace App\Http\Controllers\Applicant;

use App\Http\Controllers\Controller;
use App\Models\Course;
use Illuminate\Http\Request;

class ApplicantProfileController extends Controller
{
    /**
     * Show the form for editing the applicant's profile.
     */
    public function edit()
    {
        $applicantUser = auth()->guard('applicant')->user();
        $applicant = $applicantUser->applicant;

        // Load all courses for the preferred course dropdowns
        $courses = Course::orderBy('course_name')->get();

        return view('applicant.profile', compact('applicant', 'courses'));
    }

    /**
     * Update the applicant's profile.
     */
    public function update(Request $request)
    {
        $applicantUser = auth()->guard('applicant')->user();
        $applicant = $applicantUser->applicant;

        // Validation rules
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'required|string|max:255',
            'birth_date' => 'required|date',
            'place_of_birth' => 'required|string|max:255',
            'sex' => 'required|in:Male,Female',
            'civil_status' => 'required|in:Single,Married,Widowed,Separated',
            'email' => 'required|email|max:255',
            'contact_number' => 'required|string|max:20',
            'barangay' => 'required|string|max:255',
            'municipality' => 'required|string|max:255',
            'province' => 'required|string|max:255',
            'last_school_attended' => 'required|string|max:255',
            'school_address' => 'required|string|max:255',
            'year_graduated' => 'required|integer|min:1950|max:' . (date('Y') + 1),
            'gen_average' => 'required|numeric|min:65|max:100',
            'preferred_course_1' => 'required|exists:courses,course_id',
            'preferred_course_2' => 'required|exists:courses,course_id|different:preferred_course_1',
            'preferred_course_3' => 'required|exists:courses,course_id|different:preferred_course_1,preferred_course_2',
        ]);

        // Update the applicant record
        $applicant->update($validated);

        return redirect()->route('applicant.declaration.edit')->with('success', 'Profile updated successfully. Please complete your declaration.');
    }
}

