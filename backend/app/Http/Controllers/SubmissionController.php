<?php

namespace App\Http\Controllers;

use App\Models\Submission;
use App\Models\ReviewerSuggestion;
use Illuminate\Http\Request;

class SubmissionController extends Controller
{
    public function index(Request $request)
    {
        $submissions = $request->user()->submissions()->with('author')->paginate(10);

        return response()->json([
            'success' => true,
            'data' => $submissions->items(),
            'pagination' => [
                'total' => $submissions->total(),
                'per_page' => $submissions->perPage(),
                'current_page' => $submissions->currentPage(),
            ],
        ], 200);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:500',
            'abstract' => 'required|string|max:5000',
            'keywords' => 'nullable|array',
            'research_field' => 'nullable|string|max:255',
            'funding_source' => 'nullable|string|max:255',
            'competing_interests' => 'nullable|string',
            'data_availability' => 'nullable|string',
            'reviewer_suggestions' => 'nullable|array',
        ]);

        $submission = $request->user()->submissions()->create([
            'title' => $validated['title'],
            'abstract' => $validated['abstract'],
            'keywords' => $validated['keywords'] ?? [],
            'research_field' => $validated['research_field'],
            'funding_source' => $validated['funding_source'],
            'competing_interests' => $validated['competing_interests'],
            'data_availability' => $validated['data_availability'],
            'status' => 'draft',
        ]);

        // Add reviewer suggestions
        if (!empty($validated['reviewer_suggestions'])) {
            foreach ($validated['reviewer_suggestions'] as $reviewer) {
                ReviewerSuggestion::create([
                    'submission_id' => $submission->id,
                    'reviewer_name' => $reviewer['name'],
                    'reviewer_email' => $reviewer['email'],
                    'institution' => $reviewer['institution'] ?? null,
                    'rationale' => $reviewer['rationale'] ?? null,
                ]);
            }
        }

        return response()->json([
            'success' => true,
            'submission' => [
                'id' => $submission->id,
                'title' => $submission->title,
                'abstract' => $submission->abstract,
                'status' => $submission->status,
                'created_at' => $submission->created_at,
            ],
            'message' => 'Submission created successfully',
        ], 201);
    }

    public function show(Request $request, Submission $submission)
    {
        $this->authorize('view', $submission);

        return response()->json([
            'success' => true,
            'submission' => [
                'id' => $submission->id,
                'title' => $submission->title,
                'abstract' => $submission->abstract,
                'keywords' => $submission->keywords,
                'research_field' => $submission->research_field,
                'funding_source' => $submission->funding_source,
                'competing_interests' => $submission->competing_interests,
                'data_availability' => $submission->data_availability,
                'status' => $submission->status,
                'version' => $submission->version,
                'doi' => $submission->doi,
                'created_at' => $submission->created_at,
                'updated_at' => $submission->updated_at,
            ],
        ], 200);
    }

    public function update(Request $request, Submission $submission)
    {
        $this->authorize('update', $submission);

        $validated = $request->validate([
            'title' => 'sometimes|string|max:500',
            'abstract' => 'sometimes|string|max:5000',
            'keywords' => 'sometimes|array',
            'research_field' => 'sometimes|string|max:255',
            'funding_source' => 'sometimes|string|max:255',
            'competing_interests' => 'sometimes|string',
            'data_availability' => 'sometimes|string',
        ]);

        $submission->update($validated);

        return response()->json([
            'success' => true,
            'submission' => $submission,
            'message' => 'Submission updated successfully',
        ], 200);
    }

    public function submit(Request $request, Submission $submission)
    {
        $this->authorize('update', $submission);

        if ($submission->status !== 'draft') {
            return response()->json([
                'success' => false,
                'message' => 'Only draft submissions can be submitted',
            ], 422);
        }

        $submission->update([
            'status' => 'submitted',
            'received_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'submission' => $submission,
            'message' => 'Submission submitted successfully',
        ], 200);
    }

    public function destroy(Request $request, Submission $submission)
    {
        $this->authorize('delete', $submission);

        $submission->delete();

        return response()->json([
            'success' => true,
            'message' => 'Submission deleted successfully',
        ], 200);
    }
}
