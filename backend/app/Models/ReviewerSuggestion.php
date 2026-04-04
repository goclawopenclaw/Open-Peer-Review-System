<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class ReviewerSuggestion extends Model
{
    use HasUuids;

    protected $fillable = [
        'submission_id',
        'reviewer_name',
        'reviewer_email',
        'institution',
        'rationale',
        'conflict_of_interest',
    ];
}
