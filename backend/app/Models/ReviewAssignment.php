<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ReviewAssignment extends Model
{
    use HasUuids;

    protected $fillable = [
        'submission_id',
        'reviewer_id',
        'assigned_by_editor_id',
        'deadline_at',
        'status',
        'invitation_sent_at',
        'accepted_at',
        'declined_at',
        'decline_reason',
        'alternative_reviewer_name',
    ];

    protected $casts = [
        'deadline_at' => 'datetime',
        'invitation_sent_at' => 'datetime',
        'accepted_at' => 'datetime',
        'declined_at' => 'datetime',
    ];

    public function submission(): BelongsTo
    {
        return $this->belongsTo(Submission::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }

    public function editor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_by_editor_id');
    }

    public function review(): HasOne
    {
        return $this->hasOne(Review::class, 'assignment_id');
    }
}
