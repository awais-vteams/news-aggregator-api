<?php

namespace App\Domains\News\Controllers;

use App\Domains\News\Actions\GetUserPreferenceAction;
use App\Domains\News\Actions\GetUserPreferenceArticleAction;
use App\Domains\News\Actions\SaveUserPreferenceAction;
use App\Domains\News\Requests\UserPreferenceRequest;
use App\Domains\News\Resources\UserPreferenceResource;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserPreferenceController extends Controller
{
    /**
     * Get the user's preferences.
     */
    public function getPreferences(Request $request, GetUserPreferenceAction $userPreferenceAction): ?UserPreferenceResource
    {
        return new UserPreferenceResource($userPreferenceAction->run($request->user()->id));
    }

    /**
     * Set the user's preferences.
     */
    public function setPreferences(UserPreferenceRequest $request, SaveUserPreferenceAction $userPreferenceAction): JsonResponse
    {
        $preferences = $userPreferenceAction->run($request->user()->id, $request->validated());

        return (new UserPreferenceResource($preferences))
            ->response()
            ->setStatusCode(201);
    }

    public function getPersonalizedArticles(
        Request $request,
        GetUserPreferenceAction $userPreferenceAction,
        GetUserPreferenceArticleAction $getUserPreferenceArticleAction
    ) {
        $preferences = $userPreferenceAction->run($request->user()->id);

        if (! $preferences) {
            return response()->json(['message' => 'No preferences set.'], 404);
        }

        return $getUserPreferenceArticleAction->run($preferences);
    }
}
