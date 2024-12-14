<?php

namespace App\Domains\News\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    use HasUlids, HasFactory;

    protected $perPage = 20;

    protected $fillable = ['title', 'description', 'url', 'author', 'content', 'category', 'source_name', 'source_url', 'published_at'];

    /**
     * Scope a query to filter articles based on search parameters.
     */
    public function scopeFilter($query, array $filters)
    {
        if (filled($filters['keyword'])) {
            $query->where(function ($query) use ($filters) {
                $query->where('title', 'like', '%'.$filters['keyword'].'%')
                    ->orWhere('description', 'like', '%'.$filters['keyword'].'%');
            });
        }

        if (filled($filters['date'])) {
            $query->whereDate('published_at', $filters['date']);
        }

        if (filled($filters['category'])) {
            $query->where('category', $filters['category']);
        }

        if (filled($filters['source'])) {
            $query->where('source_name', $filters['source']);
        }

        return $query;
    }
}
