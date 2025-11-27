<?php

namespace App\Http\Controllers\Applicant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ApplicantDeclarationController extends Controller
{
    /**
     * Show the form for editing the applicant's declaration.
     */
    public function edit()
    {
        $applicantUser = auth()->guard('applicant')->user();
        $applicant = $applicantUser->applicant;

        // Load or create the declaration record
        $declaration = $applicant->declaration;

        return view('applicant.declaration', compact('applicant', 'declaration'));
    }

    /**
     * Update the applicant's declaration.
     */
    public function update(Request $request)
    {
        $applicantUser = auth()->guard('applicant')->user();
        $applicant = $applicantUser->applicant;

        // Validation rules
        $validated = $request->validate([
            'physical_condition_flag' => 'required|boolean',
            'physical_condition_desc' => 'nullable|string|required_if:physical_condition_flag,1',
            'disciplinary_action_flag' => 'required|boolean',
            'disciplinary_action_desc' => 'nullable|string|required_if:disciplinary_action_flag,1',
            'certified_signature_name' => 'required|string|max:255',
            'certified_date' => 'required|date',
        ]);

        // Get or create the declaration
        $declaration = $applicant->declaration;

        if (!$declaration) {
            // Create new declaration
            $applicant->declaration()->create(array_merge($validated, [
                'applicant_id' => $applicant->applicant_id,
            ]));
        } else {
            // Update existing declaration
            $declaration->update($validated);
        }

        return redirect()->route('applicant.dashboard')->with('success', 'Declaration submitted successfully.');
    }
}

