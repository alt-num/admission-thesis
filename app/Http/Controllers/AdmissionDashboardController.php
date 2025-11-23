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
        $examTakenApplicants = Applicant::where('status', 'ExamTaken')->count();
        $passedApplicants = Applicant::where('status', 'Passed')->count();
        $failedApplicants = Applicant::where('status', 'Failed')->count();
        $totalExams = Exam::count();

        return view('admission.dashboard', compact(
            'totalApplicants',
            'pendingApplicants',
            'examTakenApplicants',
            'passedApplicants',
            'failedApplicants',
            'totalExams'
        ));
    }
}

