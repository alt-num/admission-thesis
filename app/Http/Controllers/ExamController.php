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
            'is_active' => 'nullable|boolean',
        ]);

        // If this exam is being set as active, deactivate all other exams
        if ($request->boolean('is_active')) {
            Exam::where('is_active', true)->update(['is_active' => false]);
        }

        Exam::create([
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'is_active' => $request->boolean('is_active', false),
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
     * Show the exam editor (placeholder).
     */
    public function editor(Exam $exam)
    {
        return view('admission.exams.editor', compact('exam'));
    }
}

