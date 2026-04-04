<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EditorialDecision extends Model
{
    use HasUuids;

    protected $fillable = [
        'submission_id',
        'editor_id',
        'decision',
        'decision_letter',
        'revision_deadline_at',
        'sent_at',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
        'revision_deadline_at' => 'datetime',
    ];

    public function submission(): BelongsTo
    {
        return $this->belongsTo(Submission::class);
    }

    public function editor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'editor_id');
    }
}
