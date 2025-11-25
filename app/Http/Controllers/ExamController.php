<?php

namespace App\Http\Controllers;

use App\Models\Exam;
use Illuminate\Http\Request;

class ExamController extends Controller
{
    /**
     * Display a listing of exams.
     */
    public function index()
    {
        $exams = Exam::latest()->paginate(15);

        return view('admission.exams.index', compact('exams'));
    }

    /**
     * Show the form for creating a new exam.
     */
    public function create()
    {
        return view('admission.exams.create');
    }

    /**
     * Store a newly created exam.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        // Automatically set the year to current year
        $year = now()->year;

        // Create exam as inactive by default
        Exam::create([
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'is_active' => false,
            'year' => $year,
        ]);

        return redirect()
            ->route('admission.exams.index')
            ->with('success', 'Exam created successfully!');
    }

    /**
     * Display the specified exam.
     */
    public function show(Exam $exam)
    {
        return view('admission.exams.show', compact('exam'));
    }

    /**
     * Activate an exam (deactivates all others).
     */
    public function activate(Exam $exam)
    {
        // Deactivate all other exams
        Exam::where('is_active', true)->update(['is_active' => false]);

        // Activate this exam
        $exam->update(['is_active' => true]);

        return back()->with('success', 'Exam activated successfully!');
    }

    /**
     * Deactivate an exam.
     */
    public function deactivate(Exam $exam)
    {
        $exam->update(['is_active' => false]);

        return back()->with('success', 'Exam deactivated successfully!');
    }
}

