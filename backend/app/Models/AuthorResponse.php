<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class AuthorResponse extends Model
{
    use HasUuids;

    protected $fillable = [
        'submission_id',
        'revision_version',
        'response_document_url',
        'response_text',
        'new_manuscript_url',
        'submitted_at',
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
    ];
}
