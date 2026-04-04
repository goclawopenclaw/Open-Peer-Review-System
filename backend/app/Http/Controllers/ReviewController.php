<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\ReviewAssignment;
use App\Models\InlineComment;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function pendingAssignments(Request $request)
    {
        $assignments = ReviewAssignment::where('reviewer_id', $request->user()->id)
            ->where('status', '!=', 'submitted')
            ->with('submission')
            ->paginate(20);

        return response()->json($assignments);
    }

    public function acceptAssignment(Request $request, ReviewAssignment $assignment)
    {
        if ($assignment->reviewer_id !== $request->user()->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $assignment->update([
            'status' => 'accepted',
            'accepted_at' => now(),
        ]);

        return response()->json([
            'message' => 'Review assignment accepted',
            'assignment' => $assignment,
        ]);
    }

    public function declineAssignment(Request $request, ReviewAssignment $assignment)
    {
        if ($assignment->reviewer_id !== $request->user()->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'reason' => 'nullable|string',
            'alternative_reviewer_name' => 'nullable|string',
        ]);

        $assignment->update([
            'status' => 'declined',
            'declined_at' => now(),
            'decline_reason' => $validated['reason'] ?? null,
            'alternative_reviewer_name' => $validated['alternative_reviewer_name'] ?? null,
        ]);

        return response()->json([
            'message' => 'Review assignment declined',
            'assignment' => $assignment,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'assignment_id' => 'required|uuid|exists:review_assignments,id',
            'summary' => 'required|string|min:100',
            'strengths' => 'required|string',
            'weaknesses' => 'required|string',
            'detailed_comments' => 'required|string',
            'recommendation' => 'required|in:accept,minor_revisions,major_revisions,reject',
            'confidence' => 'required|in:high,medium,low',
            'is_signed' => 'boolean',
            'inline_comments' => 'array',
        ]);

        $assignment = ReviewAssignment::find($validated['assignment_id']);

        if ($assignment->reviewer_id !== $request->user()->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
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
            'submitted_at' => now(),
        ]);

        // Save inline comments
        if (isset($validated['inline_comments'])) {
            foreach ($validated['inline_comments'] as $comment) {
                InlineComment::create([
                    'review_id' => $review->id,
                    'submission_id' => $assignment->submission_id,
                    'reviewer_id' => $request->user()->id,
                    'paragraph_number' => $comment['paragraph_number'] ?? null,
                    'text_excerpt' => $comment['text_excerpt'] ?? null,
                    'comment_text' => $comment['comment_text'],
                    'is_public' => $comment['is_public'] ?? true,
                ]);
            }
        }

        $assignment->update(['status' => 'submitted']);

        return response()->json([
            'message' => 'Review submitted',
            'review' => $review,
        ], 201);
    }

    public function show(Review $review)
    {
        return response()->json([
            'review' => $review->load('reviewer', 'inlineComments'),
        ]);
    }

    public function submissionReviews($submissionId)
    {
        $reviews = Review::where('submission_id', $submissionId)
            ->where('is_public', true)
            ->with('reviewer', 'inlineComments')
            ->get();

        return response()->json(['reviews' => $reviews]);
    }
}
