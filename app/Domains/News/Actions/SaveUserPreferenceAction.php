<?php

namespace App\Domains\News\Actions;

use App\Domains\News\Models\UserPreference;

class SaveUserPreferenceAction
{
    public function run(int $userId, array $data): UserPreference
    {
        return UserPreference::updateOrCreate(
            ['user_id' => $userId],
            $data
        );
    }
}
