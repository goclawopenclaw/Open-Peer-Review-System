<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class InlineComment extends Model
{
    use HasUuids;

    protected $fillable = [
        'review_id',
        'submission_id',
        'reviewer_id',
        'paragraph_number',
        'text_excerpt',
        'comment_text',
        'is_public',
    ];

    protected $casts = [
        'is_public' => 'boolean',
    ];
}
