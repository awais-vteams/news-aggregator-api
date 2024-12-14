<?php

namespace App\Domains\News\Controllers;

use App\Domains\News\Actions\GetAllArticleAction;
use App\Domains\News\Actions\GetSingleArticleAction;
use App\Domains\News\Requests\AllArticleRequest;
use App\Http\Controllers\Controller;

class ArticleController extends Controller
{
    /**
     * Fetch paginated articles with search and filtering.
     */
    public function index(AllArticleRequest $request, GetAllArticleAction $getAllArticleAction)
    {
        return $getAllArticleAction->run($request);
    }

    /**
     * Retrieve a single article's details.
     */
    public function show($id, GetSingleArticleAction $getSingleArticleAction)
    {
        return $getSingleArticleAction->run($id);
    }
}
