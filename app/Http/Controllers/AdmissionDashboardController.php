<?php

namespace App\Http\Controllers;

use App\Models\Applicant;
use App\Models\Exam;
use Illuminate\Http\Request;

class AdmissionDashboardController extends Controller
{
    public function index()
    {
        $totalApplicants = Applicant::count();
        $pendingApplicants = Applicant::where('status', 'Pending')->count();
        $qualifiedApplicants = Applicant::where('status', 'Qualified')->count();
        $notQualifiedApplicants = Applicant::where('status', 'NotQualified')->count();
        $totalExams = Exam::count();

        return view('admission.dashboard', compact(
            'totalApplicants',
            'pendingApplicants',
            'qualifiedApplicants',
            'notQualifiedApplicants',
            'totalExams'
        ));
    }
}

