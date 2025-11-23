<?php

namespace App\Http\Controllers;

use App\Models\Applicant;
use App\Models\ApplicantUser;
use App\Models\Campus;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class ApplicantController extends Controller
{
    /**
     * Display a listing of applicants (for admission users).
     */
    public function index()
    {
        $applicants = Applicant::with(['campus', 'preferredCourse1', 'preferredCourse2', 'preferredCourse3'])
            ->latest()
            ->paginate(15);

        return view('admission.applicants.index', compact('applicants'));
    }

    /**
     * Show the form for creating a new applicant.
     */
    public function create()
    {
        $campuses = Campus::orderBy('campus_name')->get();
        $courses = Course::with('department')->orderBy('course_name')->get();

        return view('admission.applicants.create', compact('campuses', 'courses'));
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

        return redirect()
            ->route('admission.applicants.index')
            ->with('success', "Applicant registered successfully! Username: {$username}, Password: {$defaultPassword}");
    }
}
