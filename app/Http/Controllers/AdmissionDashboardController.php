<?php

namespace App\Http\Controllers;

use App\Models\Applicant;
use App\Models\Exam;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdmissionDashboardController extends Controller
{
    public function index()
    {
        // Check if employee is active
        $admissionUser = Auth::guard('admission')->user();
        if ($admissionUser && $admissionUser->employee) {
            if (strtolower($admissionUser->employee->status) !== 'active') {
                Auth::guard('admission')->logout();
                return redirect()->route('login')->with('error', 'Your account is now inactive.');
            }
        }

        $totalApplicants = Applicant::count();
        $pendingApplicants = Applicant::where('status', 'Pending')->count();
        $qualifiedApplicants = Applicant::where('status', 'Qualified')->count();
        $notQualifiedApplicants = Applicant::where('status', 'NotQualified')->count();
        $flaggedApplicants = Applicant::where('status', 'Flagged')->count();
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

