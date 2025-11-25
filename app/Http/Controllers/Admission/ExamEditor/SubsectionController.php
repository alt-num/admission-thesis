<?php

namespace App\Http\Controllers\Admission\ExamEditor;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admission\ExamEditor\StoreSubsectionRequest;
use App\Http\Requests\Admission\ExamEditor\UpdateSubsectionRequest;
use App\Models\Exam;
use App\Models\ExamSection;
use App\Models\ExamSubsection;
use Illuminate\Http\Request;

class SubsectionController extends Controller
{
    /**
     * Store a new subsection.
     */
    public function store(StoreSubsectionRequest $request, Exam $exam, ExamSection $section)
    {
        // Validate section belongs to exam
        if ($section->exam_id !== $exam->exam_id) {
            return response()->json([
                'success' => false,
                'message' => 'Section does not belong to this exam.',
            ], 403);
        }

        // Get the next order number if not provided
        $orderNo = $request->input('order_no');
        if (!$orderNo) {
            $maxOrder = ExamSubsection::where('section_id', $section->section_id)->max('order_no') ?? 0;
            $orderNo = $maxOrder + 1;
        } else {
            // Shift existing subsections with order_no >= new order_no
            ExamSubsection::where('section_id', $section->section_id)
                ->where('order_no', '>=', $orderNo)
                ->increment('order_no');
        }

        $subsection = ExamSubsection::create([
            'section_id' => $section->section_id,
            'name' => $request->input('name'),
            'order_no' => $orderNo,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Subsection created successfully.',
            'data' => $subsection->fresh(),
        ]);
    }

    /**
     * Update a subsection.
     */
    public function update(Exam $exam, ExamSubsection $subsection, UpdateSubsectionRequest $request)
    {
        // Validate subsection belongs to exam
        if ($subsection->section->exam_id !== $exam->exam_id) {
            return response()->json([
                'success' => false,
                'message' => 'Subsection does not belong to this exam.',
            ], 403);
        }

        $oldOrderNo = $subsection->order_no;
        $newOrderNo = $request->input('order_no', $oldOrderNo);

        // Update order if changed
        if ($newOrderNo != $oldOrderNo) {
            if ($newOrderNo > $oldOrderNo) {
                // Moving down: decrement subsections between old and new
                ExamSubsection::where('section_id', $subsection->section_id)
                    ->where('subsection_id', '!=', $subsection->subsection_id)
                    ->where('order_no', '>', $oldOrderNo)
                    ->where('order_no', '<=', $newOrderNo)
                    ->decrement('order_no');
            } else {
                // Moving up: increment subsections between new and old
                ExamSubsection::where('section_id', $subsection->section_id)
                    ->where('subsection_id', '!=', $subsection->subsection_id)
                    ->where('order_no', '>=', $newOrderNo)
                    ->where('order_no', '<', $oldOrderNo)
                    ->increment('order_no');
            }
        }

        $subsection->update([
            'name' => $request->input('name'),
            'order_no' => $newOrderNo,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Subsection updated successfully.',
            'data' => $subsection->fresh(),
        ]);
    }

    /**
     * Delete a subsection.
     */
    public function destroy(Exam $exam, ExamSubsection $subsection)
    {
        // Validate subsection belongs to exam
        if ($subsection->section->exam_id !== $exam->exam_id) {
            return response()->json([
                'success' => false,
                'message' => 'Subsection does not belong to this exam.',
            ], 403);
        }

        $orderNo = $subsection->order_no;
        $sectionId = $subsection->section_id;

        // Delete the subsection
        $subsection->delete();

        // Decrement order_no for subsections after the deleted one
        ExamSubsection::where('section_id', $sectionId)
            ->where('order_no', '>', $orderNo)
            ->decrement('order_no');

        return response()->json([
            'success' => true,
            'message' => 'Subsection deleted successfully.',
            'data' => null,
        ]);
    }

    /**
     * Reorder subsections.
     */
    public function reorder(Request $request)
    {
        $request->validate([
            'subsections' => 'required|array',
            'subsections.*.subsection_id' => 'required|exists:exam_subsections,subsection_id',
            'subsections.*.order_no' => 'required|integer|min:1',
        ]);

        // Update order numbers
        foreach ($request->input('subsections') as $item) {
            ExamSubsection::where('subsection_id', $item['subsection_id'])
                ->update(['order_no' => $item['order_no']]);
        }

        // Get first subsection to determine section_id for reload
        $firstSubsection = ExamSubsection::find($request->input('subsections.0.subsection_id'));
        if (!$firstSubsection) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid subsection data.',
            ], 400);
        }

        // Reload subsections in new order
        $updatedSubsections = ExamSubsection::where('section_id', $firstSubsection->section_id)
            ->orderBy('order_no')
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Subsections reordered successfully.',
            'data' => $updatedSubsections,
        ]);
    }
}

