<?php

namespace App\Http\Controllers\Admission;

use App\Http\Controllers\Controller;
use App\Models\Applicant;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class ApplicantPdfController extends Controller
{
    /**
     * Generate the application form PDF for an applicant.
     */
    public function generate($id)
    {
        // Load applicant with all required relationships
        $applicant = Applicant::with([
            'campus',
            'preferredCourse1',
            'preferredCourse2',
            'preferredCourse3',
            'declaration',
            'applicantUser',
            'examSchedules.examSchedule.exam'
        ])->findOrFail($id);

        // Prepare photo path
        $photo_path = null;
        if ($applicant->photo_path && Storage::disk('public')->exists($applicant->photo_path)) {
            $photo_path = storage_path('app/public/' . $applicant->photo_path);
        }

        // Prepare declaration date
        $declaration = $applicant->declaration;
        $declarationDate = '';
        if ($declaration && $declaration->certified_date) {
            $declarationDate = \Carbon\Carbon::parse($declaration->certified_date)->format('d/m/Y');
        }

        // Prepare logo paths
        $essu_logo = public_path('imgs/essu.png');
        $bagong_logo = public_path('imgs/bagong_pilipinas.png');

        // Build data array
        $data = [
            'applicant' => $applicant,
            'photo_path' => $photo_path,
            'essu_logo' => $essu_logo,
            'bagong_logo' => $bagong_logo,
            'declaration_date' => $declarationDate,
        ];

        // Generate PDF
        $pdf = Pdf::loadView('pdf.application_form', $data)
            ->setPaper('a4', 'portrait');

        return $pdf->stream('application_form.pdf');
    }
}

