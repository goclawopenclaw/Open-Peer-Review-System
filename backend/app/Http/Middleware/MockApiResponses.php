<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class MockApiResponses
{
    public function handle(Request $request, Closure $next): Response
    {
        // Mock auth endpoints
        if ($request->path() === 'api/auth/register' && $request->isMethod('post')) {
            return response()->json([
                'user' => [
                    'id' => 'uuid-' . uniqid(),
                    'name' => $request->input('name'),
                    'email' => $request->input('email'),
                    'affiliation' => $request->input('affiliation'),
                ],
                'token' => 'mock-token-' . uniqid(),
                'message' => 'User registered successfully'
            ], 201);
        }

        if ($request->path() === 'api/auth/login' && $request->isMethod('post')) {
            return response()->json([
                'user' => [
                    'id' => 'uuid-user-1',
                    'name' => 'Test User',
                    'email' => $request->input('email'),
                    'is_editor' => false,
                ],
                'token' => 'mock-token-login-' . uniqid(),
                'message' => 'Login successful'
            ], 200);
        }

        if ($request->path() === 'api/auth/me' && $request->isMethod('get')) {
            return response()->json([
                'user' => [
                    'id' => 'uuid-user-1',
                    'name' => 'Test User',
                    'email' => 'test@example.com',
                    'affiliation' => 'Test University',
                    'is_editor' => false,
                    'is_admin' => false,
                ]
            ], 200);
        }

        if ($request->path() === 'api/auth/logout' && $request->isMethod('post')) {
            return response()->json([
                'message' => 'Logged out successfully'
            ], 200);
        }

        // Mock submissions endpoints - GET single submission
        if (preg_match('/api\/submissions\/(.+)$/', $request->path()) && $request->isMethod('get')) {
            preg_match('/api\/submissions\/(.+)$/', $request->path(), $matches);
            return response()->json([
                'submission' => [
                    'id' => $matches[1],
                    'title' => 'Sample Article Title',
                    'abstract' => 'This is a sample article abstract for testing purposes.',
                    'keywords' => ['keyword1', 'keyword2', 'keyword3'],
                    'research_field' => 'Computer Science',
                    'funding_source' => 'National Science Foundation',
                    'competing_interests' => 'None',
                    'data_availability' => 'Data available upon request',
                    'status' => 'submitted',
                    'created_at' => now(),
                ]
            ], 200);
        }

        // Mock submissions endpoints - POST create
        if ($request->path() === 'api/submissions' && $request->isMethod('post')) {
            return response()->json([
                'success' => true,
                'submission' => [
                    'id' => 'uuid-sub-' . uniqid(),
                    'title' => $request->input('title'),
                    'abstract' => $request->input('abstract'),
                    'keywords' => $request->input('keywords', []),
                    'research_field' => $request->input('research_field'),
                    'status' => 'draft',
                    'created_at' => now(),
                ]
            ], 201);
        }

        // Mock submit endpoint (POST /submissions/{id}/submit)
        if (preg_match('/api\/submissions\/(.+)\/submit/', $request->path()) && $request->isMethod('post')) {
            return response()->json([
                'success' => true,
                'submission' => [
                    'id' => 'uuid-sub-' . uniqid(),
                    'title' => $request->input('title'),
                    'abstract' => $request->input('abstract'),
                    'status' => 'submitted',
                    'created_at' => now(),
                ]
            ], 200);
        }

        if ($request->path() === 'api/public/articles' && $request->isMethod('get')) {
            return response()->json([
                'data' => [
                    [
                        'id' => 'uuid-article-1',
                        'title' => 'Sample Article 1',
                        'abstract' => 'This is a sample article abstract.',
                        'author' => ['name' => 'John Doe', 'affiliation' => 'University A'],
                        'keywords' => ['keyword1', 'keyword2'],
                        'research_field' => 'Computer Science',
                        'published_at' => now(),
                        'reviews' => []
                    ],
                    [
                        'id' => 'uuid-article-2',
                        'title' => 'Sample Article 2',
                        'abstract' => 'Another sample article.',
                        'author' => ['name' => 'Jane Smith', 'affiliation' => 'University B'],
                        'keywords' => ['keyword3'],
                        'research_field' => 'Biology',
                        'published_at' => now(),
                        'reviews' => []
                    ]
                ]
            ], 200);
        }

        if (preg_match('/api\/public\/articles\/(.+)/', $request->path())) {
            return response()->json([
                'article' => [
                    'id' => 'uuid-article-1',
                    'title' => 'Sample Article',
                    'abstract' => 'This is a sample article.',
                    'author' => ['name' => 'John Doe', 'affiliation' => 'University A'],
                    'keywords' => ['keyword1', 'keyword2'],
                    'research_field' => 'Computer Science',
                    'published_at' => now(),
                ],
                'reviews' => [
                    [
                        'id' => 'uuid-review-1',
                        'reviewer' => ['name' => 'Reviewer 1'],
                        'summary' => 'Good paper',
                        'strengths' => 'Novel approach',
                        'weaknesses' => 'Limited evaluation',
                        'detailed_comments' => 'More experiments needed',
                        'recommendation' => 'minor_revisions',
                        'confidence' => 'high',
                        'is_signed' => true,
                        'submitted_at' => now(),
                    ]
                ]
            ], 200);
        }

        return $next($request);
    }
}
