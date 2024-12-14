<?php

namespace App\Domains\News\Actions;

use App\Domains\News\Models\UserPreference;

class GetUserPreferenceAction
{
    public function run(int $userId): ?UserPreference
    {
        return UserPreference::where('user_id', $userId)->first();
    }
}
