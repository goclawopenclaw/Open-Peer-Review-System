<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\ReviewAssignment;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function pendingAssignments(Request $request)
    {
        $assignments = $request->user()
            ->reviewerAssignments()
            ->with('submission.author', 'assignedByEditor')
            ->where('status', 'pending')
            ->paginate(10);

        return response()->json([
            'success' => true,
            'data' => $assignments->items(),
            'pagination' => [
                'total' => $assignments->total(),
                'per_page' => $assignments->perPage(),
                'current_page' => $assignments->currentPage(),
            ],
        ], 200);
    }

    public function acceptAssignment(Request $request, ReviewAssignment $assignment)
    {
        if ($assignment->reviewer_id !== $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 403);
        }

        $assignment->update([
            'status' => 'accepted',
            'accepted_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'assignment' => $assignment,
            'message' => 'Review assignment accepted',
        ], 200);
    }

    public function declineAssignment(Request $request, ReviewAssignment $assignment)
    {
        if ($assignment->reviewer_id !== $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 403);
        }

        $reason = $request->input('reason');

        $assignment->update([
            'status' => 'declined',
            'declined_at' => now(),
            'decline_reason' => $reason,
        ]);

        return response()->json([
            'success' => true,
            'assignment' => $assignment,
            'message' => 'Review assignment declined',
        ], 200);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'assignment_id' => 'required|uuid|exists:review_assignments,id',
            'summary' => 'required|string',
            'strengths' => 'required|string',
            'weaknesses' => 'required|string',
            'detailed_comments' => 'required|string',
            'recommendation' => 'required|in:accept,minor_revisions,major_revisions,reject',
            'confidence' => 'required|in:high,medium,low',
            'is_signed' => 'boolean',
        ]);

        $assignment = ReviewAssignment::findOrFail($validated['assignment_id']);

        if ($assignment->reviewer_id !== $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 403);
        }

        $review = Review::create([
            'assignment_id' => $assignment->id,
            'submission_id' => $assignment->submission_id,
            'reviewer_id' => $request->user()->id,
            'summary' => $validated['summary'],
            'strengths' => $validated['strengths'],
            'weaknesses' => $validated['weaknesses'],
            'detailed_comments' => $validated['detailed_comments'],
            'recommendation' => $validated['recommendation'],
            'confidence' => $validated['confidence'],
            'is_signed' => $validated['is_signed'] ?? true,
            'is_public' => true,
            'submitted_at' => now(),
        ]);

        $assignment->update(['status' => 'submitted']);

        return response()->json([
            'success' => true,
            'review' => $review,
            'message' => 'Review submitted successfully',
        ], 201);
    }

    public function show(Request $request, Review $review)
    {
        return response()->json([
            'success' => true,
            'review' => [
                'id' => $review->id,
                'summary' => $review->summary,
                'strengths' => $review->strengths,
                'weaknesses' => $review->weaknesses,
                'detailed_comments' => $review->detailed_comments,
                'recommendation' => $review->recommendation,
                'confidence' => $review->confidence,
                'is_signed' => $review->is_signed,
                'reviewer' => $review->is_signed ? $review->reviewer : null,
                'submitted_at' => $review->submitted_at,
            ],
        ], 200);
    }

    public function submissionReviews(Request $request, $submissionId)
    {
        $reviews = Review::where('submission_id', $submissionId)
            ->with('reviewer')
            ->where('is_public', true)
            ->get();

        return response()->json([
            'success' => true,
            'reviews' => $reviews,
        ], 200);
    }
}
