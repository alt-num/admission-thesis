<?php

namespace App\Http\Controllers\Admission;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use Illuminate\Http\Request;

class ExamEditorController extends Controller
{
    /**
     * Display the exam editor.
     */
    public function index(Exam $exam, Request $request)
    {
        // Load exam with related sections, subsections, questions, and choices in correct order
        $exam->load([
            'sections' => function ($query) {
                $query->orderBy('order_no');
            },
            'sections.subsections' => function ($query) {
                $query->orderBy('order_no');
            },
            'sections.subsections.questions' => function ($query) {
                $query->orderBy('order_no');
            },
            'sections.subsections.questions.choices' => function ($query) {
                $query->orderBy('choice_id');
            }
        ]);

        // Get selected section from query parameter
        $selectedSectionId = $request->query('section_id');
        $selectedSection = null;
        
        if ($selectedSectionId) {
            $selectedSection = $exam->sections->firstWhere('section_id', $selectedSectionId);
        }

        return view('admission.exams.editor', compact('exam', 'selectedSection'));
    }
}

