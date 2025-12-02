<?php

namespace App\Http\Controllers;

use App\Models\ApplicantUser;
use Illuminate\Http\Request;

class ApplicantUserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'applicant_id' => 'required|exists:applicants,applicant_id',
            'username' => 'required|string|max:255|unique:applicant_users,username',
            'password' => 'required|string|min:8',
            'account_status' => 'sometimes|string|in:active,disabled',
        ]);

        ApplicantUser::create($validated);

        return redirect()->route('applicant-users.index')
            ->with('success', 'Applicant user created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(ApplicantUser $applicantUser)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ApplicantUser $applicantUser)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ApplicantUser $applicantUser)
    {
        $validated = $request->validate([
            'applicant_id' => 'sometimes|exists:applicants,applicant_id',
            'username' => 'sometimes|string|max:255|unique:applicant_users,username,' . $applicantUser->user_id . ',user_id',
            'password' => 'sometimes|string|min:8',
            'account_status' => 'sometimes|string|in:active,disabled',
        ]);

        $applicantUser->update($validated);

        return redirect()->route('applicant-users.index')
            ->with('success', 'Applicant user updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ApplicantUser $applicantUser)
    {
        //
    }
}
