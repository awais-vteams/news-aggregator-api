<?php

namespace App\Domains\News\Models;

use App\Domains\News\DTO\ArticleFilterDto;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    use HasUlids, HasFactory;

    protected $perPage = 20;

    protected $fillable = ['title', 'description', 'url', 'author', 'content', 'category', 'source_name', 'source_url', 'published_at'];

    protected $casts = [
        'published_at' => 'datetime',
    ];

    /**
     * Scope a query to filter articles based on search parameters.
     */
    public function scopeFilter(Builder $query, ArticleFilterDto $filter): Builder
    {
        $query->when($filter->keyword, function ($query, $filter) {
            $query->where('title', 'like', '%'.$filter->keyword.'%')
                ->orWhere('description', 'like', '%'.$filter->keyword.'%');
        })->when($filter->date, function ($query, $filter) {
            $query->whereDate('published_at', $filter->date);
        })->when($filter->category, function ($query, $filter) {
            $query->where('category', $filter->category);
        })->when($filter->source, function ($query, $filter) {
            $query->where('source', $filter->source);
        });

        return $query;
    }
}
