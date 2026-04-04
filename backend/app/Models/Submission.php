<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Submission extends Model
{
    use HasUuids;

    protected $fillable = [
        'author_id',
        'title',
        'abstract',
        'keywords',
        'research_field',
        'funding_source',
        'competing_interests',
        'data_availability',
        'manuscript_url',
        'status',
        'version',
        'doi',
    ];

    protected $casts = [
        'keywords' => 'json',
        'received_at' => 'datetime',
        'published_at' => 'datetime',
        'desk_rejected_at' => 'datetime',
    ];

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function reviewAssignments(): HasMany
    {
        return $this->hasMany(ReviewAssignment::class);
    }

    public function editorialDecision()
    {
        return $this->hasOne(EditorialDecision::class);
    }

    public function responses(): HasMany
    {
        return $this->hasMany(AuthorResponse::class);
    }
}
