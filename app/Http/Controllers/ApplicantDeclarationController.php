<?php

namespace App\Http\Controllers;

use App\Models\ApplicantDeclaration;
use Illuminate\Http\Request;

class ApplicantDeclarationController extends Controller
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
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(ApplicantDeclaration $applicantDeclaration)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ApplicantDeclaration $applicantDeclaration)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ApplicantDeclaration $applicantDeclaration)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ApplicantDeclaration $applicantDeclaration)
    {
        //
    }

    /**
     * Save remarks for an applicant's declaration.
     */
    public function saveRemarks(Request $request, \App\Models\Applicant $applicant)
    {
        $validated = $request->validate([
            'remarks' => 'nullable|string',
        ]);

        // Get or create the declaration record
        $declaration = $applicant->declaration;

        if (!$declaration) {
            // Create a new declaration record if it doesn't exist
            $declaration = ApplicantDeclaration::create([
                'applicant_id' => $applicant->applicant_id,
                'remarks' => $validated['remarks'] ?? null,
            ]);
        } else {
            // Update only the remarks field
            $declaration->update([
                'remarks' => $validated['remarks'] ?? null,
            ]);
        }

        return redirect()
            ->route('admission.applicants.declaration', $applicant)
            ->with('success', 'Remarks saved successfully!');
    }
}
