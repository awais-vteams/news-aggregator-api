<?php

namespace App\Domains\News\Controllers;

use App\Domains\News\Actions\GetUserPreferenceArticleAction;
use App\Domains\News\Models\UserPreference;
use App\Domains\News\Requests\UserPreferenceRequest;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Request;

class UserPreferenceController extends Controller
{
    /**
     * Get the user's preferences.
     */
    public function getPreferences(Request $request): JsonResponse
    {
        $preferences = UserPreference::where('user_id', $request->user()->id)->first();

        return response()->json($preferences);
    }

    /**
     * Set the user's preferences.
     */
    public function setPreferences(UserPreferenceRequest $request): JsonResponse
    {
        $preferences = UserPreference::updateOrCreate(
            ['user_id' => $request->user()->id],
            $request->validated()
        );

        return response()->json($preferences, 201);
    }

    public function getPersonalizedArticles(Request $request, GetUserPreferenceArticleAction $getUserPreferenceArticleAction)
    {
        $preferences = UserPreference::where('user_id', $request->user()->id)->first();

        if (! $preferences) {
            return response()->json(['message' => 'No preferences set.'], 404);
        }

        return $getUserPreferenceArticleAction->run($preferences);
    }
}
