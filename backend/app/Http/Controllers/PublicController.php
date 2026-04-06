<?php

namespace App\Http\Controllers;

use App\Models\Submission;
use App\Models\Review;
use App\Models\AuthorResponse;
use Illuminate\Http\Request;

class PublicController extends Controller
{
    public function articles(Request $request)
    {
        $articles = Submission::where('status', 'published')
            ->with('author', 'reviews.reviewer')
            ->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $articles->items(),
            'pagination' => [
                'total' => $articles->total(),
                'per_page' => $articles->perPage(),
                'current_page' => $articles->currentPage(),
            ],
        ], 200);
    }

    public function article($id)
    {
        $article = Submission::where('status', 'published')
            ->with('author', 'reviews.reviewer', 'authorResponse')
            ->findOrFail($id);

        $reviews = Review::where('submission_id', $article->id)
            ->where('is_public', true)
            ->with('reviewer')
            ->get();

        return response()->json([
            'success' => true,
            'article' => [
                'id' => $article->id,
                'title' => $article->title,
                'abstract' => $article->abstract,
                'keywords' => $article->keywords,
                'research_field' => $article->research_field,
                'funding_source' => $article->funding_source,
                'competing_interests' => $article->competing_interests,
                'data_availability' => $article->data_availability,
                'doi' => $article->doi,
                'published_at' => $article->published_at,
                'version' => $article->version,
                'author' => [
                    'id' => $article->author->id,
                    'name' => $article->author->name,
                    'affiliation' => $article->author->affiliation,
                ],
            ],
            'reviews' => $reviews->map(function ($review) {
                return [
                    'id' => $review->id,
                    'summary' => $review->summary,
                    'strengths' => $review->strengths,
                    'weaknesses' => $review->weaknesses,
                    'detailed_comments' => $review->detailed_comments,
                    'recommendation' => $review->recommendation,
                    'confidence' => $review->confidence,
                    'is_signed' => $review->is_signed,
                    'reviewer' => $review->is_signed ? [
                        'id' => $review->reviewer->id,
                        'name' => $review->reviewer->name,
                        'affiliation' => $review->reviewer->affiliation,
                    ] : null,
                    'submitted_at' => $review->submitted_at,
                ];
            }),
            'author_response' => $article->authorResponse ? [
                'id' => $article->authorResponse->id,
                'response_text' => $article->authorResponse->response_text,
                'response_document_url' => $article->authorResponse->response_document_url,
                'submitted_at' => $article->authorResponse->submitted_at,
            ] : null,
        ], 200);
    }
}
