<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Submission;

class SubmissionPolicy
{
    public function view(User $user, Submission $submission): bool
    {
        return $user->is_editor || $user->is_admin || $user->id === $submission->author_id;
    }

    public function update(User $user, Submission $submission): bool
    {
        return $user->id === $submission->author_id && $submission->status === 'draft';
    }

    public function delete(User $user, Submission $submission): bool
    {
        return $user->id === $submission->author_id && $submission->status === 'draft';
    }
}
