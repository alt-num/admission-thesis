<?php

namespace App\Http\Controllers\Admission\ExamEditor;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admission\ExamEditor\StoreSectionRequest;
use App\Http\Requests\Admission\ExamEditor\UpdateSectionRequest;
use App\Models\Exam;
use App\Models\ExamSection;
use Illuminate\Http\Request;

class SectionController extends Controller
{
    /**
     * Store a new section.
     */
    public function store(StoreSectionRequest $request, Exam $exam)
    {
        // Get the next order number if not provided
        $orderNo = $request->input('order_no');
        if (!$orderNo) {
            $maxOrder = ExamSection::where('exam_id', $exam->exam_id)->max('order_no') ?? 0;
            $orderNo = $maxOrder + 1;
        } else {
            // Shift existing sections with order_no >= new order_no
            ExamSection::where('exam_id', $exam->exam_id)
                ->where('order_no', '>=', $orderNo)
                ->increment('order_no');
        }

        $section = ExamSection::create([
            'exam_id' => $exam->exam_id,
            'name' => $request->input('name'),
            'order_no' => $orderNo,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Section created successfully.',
            'data' => $section->fresh(),
        ]);
    }

    /**
     * Update a section.
     */
    public function update(UpdateSectionRequest $request, Exam $exam, ExamSection $section)
    {
        // Validate ownership
        if ($section->exam_id !== $exam->exam_id) {
            return response()->json([
                'success' => false,
                'message' => 'Section does not belong to this exam.',
            ], 403);
        }

        $oldOrderNo = $section->order_no;
        $newOrderNo = $request->input('order_no', $oldOrderNo);

        // Update order if changed
        if ($newOrderNo != $oldOrderNo) {
            if ($newOrderNo > $oldOrderNo) {
                // Moving down: decrement sections between old and new
                ExamSection::where('exam_id', $exam->exam_id)
                    ->where('section_id', '!=', $section->section_id)
                    ->where('order_no', '>', $oldOrderNo)
                    ->where('order_no', '<=', $newOrderNo)
                    ->decrement('order_no');
            } else {
                // Moving up: increment sections between new and old
                ExamSection::where('exam_id', $exam->exam_id)
                    ->where('section_id', '!=', $section->section_id)
                    ->where('order_no', '>=', $newOrderNo)
                    ->where('order_no', '<', $oldOrderNo)
                    ->increment('order_no');
            }
        }

        $section->update([
            'name' => $request->input('name'),
            'order_no' => $newOrderNo,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Section updated successfully.',
            'data' => $section->fresh(),
        ]);
    }

    /**
     * Delete a section.
     */
    public function destroy(Exam $exam, ExamSection $section)
    {
        // Validate ownership
        if ($section->exam_id !== $exam->exam_id) {
            return response()->json([
                'success' => false,
                'message' => 'Section does not belong to this exam.',
            ], 403);
        }

        $orderNo = $section->order_no;

        // Delete the section
        $section->delete();

        // Decrement order_no for sections after the deleted one
        ExamSection::where('exam_id', $exam->exam_id)
            ->where('order_no', '>', $orderNo)
            ->decrement('order_no');

        return response()->json([
            'success' => true,
            'message' => 'Section deleted successfully.',
            'data' => null,
        ]);
    }

    /**
     * Reorder sections.
     */
    public function reorder(Request $request, Exam $exam)
    {
        $request->validate([
            'sections' => 'required|array',
            'sections.*.section_id' => 'required|exists:exam_sections,section_id',
            'sections.*.order_no' => 'required|integer|min:1',
        ]);

        // Validate all sections belong to this exam
        $sectionIds = collect($request->input('sections'))->pluck('section_id');
        $sections = ExamSection::where('exam_id', $exam->exam_id)
            ->whereIn('section_id', $sectionIds)
            ->get();

        if ($sections->count() !== $sectionIds->count()) {
            return response()->json([
                'success' => false,
                'message' => 'One or more sections do not belong to this exam.',
            ], 403);
        }

        // Update order numbers
        foreach ($request->input('sections') as $item) {
            ExamSection::where('section_id', $item['section_id'])
                ->update(['order_no' => $item['order_no']]);
        }

        // Reload sections in new order
        $updatedSections = ExamSection::where('exam_id', $exam->exam_id)
            ->orderBy('order_no')
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Sections reordered successfully.',
            'data' => $updatedSections,
        ]);
    }
}

