<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Review extends Model
{
    use HasUuids;

    protected $fillable = [
        'assignment_id',
        'submission_id',
        'reviewer_id',
        'summary',
        'strengths',
        'weaknesses',
        'detailed_comments',
        'recommendation',
        'confidence',
        'is_signed',
        'is_public',
        'submitted_at',
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
    ];

    public function assignment(): BelongsTo
    {
        return $this->belongsTo(ReviewAssignment::class);
    }

    public function submission(): BelongsTo
    {
        return $this->belongsTo(Submission::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }

    public function inlineComments(): HasMany
    {
        return $this->hasMany(InlineComment::class);
    }
}
