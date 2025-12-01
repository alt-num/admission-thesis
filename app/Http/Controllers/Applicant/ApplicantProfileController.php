<?php

namespace App\Http\Controllers\Applicant;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\ApplicantUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ApplicantProfileController extends Controller
{
    /**
     * Show the profile completion page (official "Complete Your Applicant Information" screen).
     */
    public function show()
    {
        $applicantUser = auth()->guard('applicant')->user();
        $applicant = $applicantUser->applicant;

        // Load all courses for the preferred course dropdowns
        $courses = Course::orderBy('course_name')->get();

        return view('applicant.profile', compact('applicant', 'courses'));
    }

    /**
     * Show the form for completing the applicant's profile (initial setup).
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
     * Update the applicant's profile (initial completion).
     */
    public function update(Request $request)
    {
        $applicantUser = auth()->guard('applicant')->user();
        $applicant = $applicantUser->applicant;

        // Check if applicant has taken exam - if yes, prevent photo changes
        $hasExamAttempt = $applicant->examAttempts()->exists();
        $needsRevision = $applicant->needs_revision ?? false;

        // Photo validation: required unless applicant already has a photo, or if exam is taken, photo cannot be changed
        $photoRules = [];
        if ($hasExamAttempt) {
            // After exam, photo cannot be changed
            $photoRules['photo'] = 'nullable|prohibited';
        } else {
            // Before exam, photo is required if not already uploaded
            if (empty($applicant->photo_path)) {
                $photoRules['photo'] = 'required|image|mimes:png,jpg,jpeg|max:2048';
            } else {
                $photoRules['photo'] = 'nullable|image|mimes:png,jpg,jpeg|max:2048';
            }
        }

        // Validation rules - different based on whether exam is taken and needs revision
        $baseRules = [];
        
        if ($needsRevision && $hasExamAttempt) {
            // After exam with revision: only allow editing specific fields
            $baseRules = [
                'email' => 'required|email|max:255',
                'contact_number' => 'required|string|max:20',
                'barangay' => 'required|string|max:255',
                'municipality' => 'required|string|max:255',
                'province' => 'required|string|max:255',
                'last_school_attended' => 'required|string|max:255',
                'school_address' => 'required|string|max:255',
            ];
        } else {
            // Before exam or no revision needed: all fields editable
            $baseRules = [
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
            ];
        }

        $validated = $request->validate(array_merge($baseRules, $photoRules));

        // Remove 'photo' from validated array (it's not a database column, only photo_path is)
        unset($validated['photo']);

        // Handle photo upload
        if ($request->hasFile('photo') && !$hasExamAttempt) {
            // Delete old photo if exists
            if ($applicant->photo_path && Storage::disk('public')->exists($applicant->photo_path)) {
                Storage::disk('public')->delete($applicant->photo_path);
            }

            // Store new photo
            $photo = $request->file('photo');
            $photoName = 'applicant_' . $applicant->applicant_id . '_' . time() . '.' . $photo->getClientOriginalExtension();
            $photoPath = $photo->storeAs('applicant_photos', $photoName, 'public');
            
            $validated['photo_path'] = $photoPath;
        }

        // Update the applicant record
        $applicant->update($validated);

        // Clear needs_revision flag after successful update
        if ($needsRevision) {
            $applicant->needs_revision = false;
            $applicant->save();
        }

        return redirect()->route('applicant.declaration.edit')->with('success', 'Profile updated successfully. Please complete your declaration.');
    }

    /**
     * Show the form for editing limited profile fields (username, password, email, mobile).
     */
    public function editProfile()
    {
        $applicantUser = auth()->guard('applicant')->user();
        $applicant = $applicantUser->applicant->load([
            'preferredCourse1',
            'preferredCourse2',
            'preferredCourse3'
        ]);

        return view('applicant.profile_edit', compact('applicantUser', 'applicant'));
    }

    /**
     * Update limited profile fields (username, password, email, mobile).
     */
    public function updateProfile(Request $request)
    {
        $applicantUser = auth()->guard('applicant')->user();
        $applicant = $applicantUser->applicant;

        // Validation rules
        $validated = $request->validate([
            'username' => [
                'required',
                'string',
                'min:3',
                Rule::unique('applicant_users', 'username')->ignore($applicantUser->user_id, 'user_id'),
            ],
            'email' => [
                'required',
                'email',
                Rule::unique('applicants', 'email')->ignore($applicant->applicant_id, 'applicant_id'),
            ],
            'contact_number' => 'required|string|max:32',
            'password' => 'nullable|min:6|confirmed',
        ]);

        // Update applicant_user (username and password)
        $applicantUser->username = $validated['username'];
        if (!empty($validated['password'])) {
            $applicantUser->password = Hash::make($validated['password']);
        }
        $applicantUser->save();

        // Update applicant (email and contact_number)
        $applicant->email = $validated['email'];
        $applicant->contact_number = $validated['contact_number'];
        $applicant->save();

        return redirect()
            ->route('applicant.profile.edit')
            ->with('success', 'Profile updated successfully!');
    }
}

