<?php

namespace App\Http\Controllers;

use App\Models\Submission;
use App\Models\ReviewerSuggestion;
use Illuminate\Http\Request;

class SubmissionController extends Controller
{
    public function index(Request $request)
    {
        $query = Submission::with('author');

        if ($request->user()->is_editor || $request->user()->is_admin) {
            // Editors see all submissions
        } else {
            // Authors see only their own
            $query->where('author_id', $request->user()->id);
        }

        $submissions = $query->paginate(20);

        return response()->json($submissions);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:500',
            'abstract' => 'required|string|max:5000',
            'keywords' => 'array|max:10',
            'research_field' => 'nullable|string',
            'funding_source' => 'nullable|string',
            'competing_interests' => 'nullable|string',
            'data_availability' => 'nullable|string',
            'reviewer_suggestions' => 'array|max:5',
        ]);

        $submission = Submission::create([
            'author_id' => $request->user()->id,
            'title' => $validated['title'],
            'abstract' => $validated['abstract'],
            'keywords' => $validated['keywords'] ?? [],
            'research_field' => $validated['research_field'],
            'funding_source' => $validated['funding_source'],
            'competing_interests' => $validated['competing_interests'],
            'data_availability' => $validated['data_availability'],
            'status' => 'draft',
        ]);

        // Save reviewer suggestions
        if (isset($validated['reviewer_suggestions'])) {
            foreach ($validated['reviewer_suggestions'] as $suggestion) {
                ReviewerSuggestion::create([
                    'submission_id' => $submission->id,
                    'reviewer_name' => $suggestion['name'],
                    'reviewer_email' => $suggestion['email'],
                    'institution' => $suggestion['institution'] ?? null,
                    'rationale' => $suggestion['rationale'] ?? null,
                ]);
            }
        }

        return response()->json([
            'message' => 'Submission created',
            'submission' => $submission,
        ], 201);
    }

    public function show(Request $request, Submission $submission)
    {
        $this->authorize('view', $submission);

        return response()->json([
            'submission' => $submission->load('author', 'reviews', 'editorialDecision'),
        ]);
    }

    public function update(Request $request, Submission $submission)
    {
        $this->authorize('update', $submission);

        $validated = $request->validate([
            'title' => 'string|max:500',
            'abstract' => 'string|max:5000',
            'keywords' => 'array',
            'research_field' => 'nullable|string',
        ]);

        $submission->update($validated);

        return response()->json([
            'message' => 'Submission updated',
            'submission' => $submission,
        ]);
    }

    public function submit(Request $request, Submission $submission)
    {
        $this->authorize('update', $submission);

        if ($submission->status !== 'draft') {
            return response()->json(['error' => 'Only draft submissions can be submitted'], 422);
        }

        $submission->update([
            'status' => 'submitted',
            'received_at' => now(),
        ]);

        return response()->json([
            'message' => 'Submission submitted for review',
            'submission' => $submission,
        ]);
    }

    public function destroy(Request $request, Submission $submission)
    {
        $this->authorize('delete', $submission);

        $submission->delete();

        return response()->json(['message' => 'Submission deleted'], 204);
    }
}
