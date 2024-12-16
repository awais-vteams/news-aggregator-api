<?php

namespace App\Domains\News\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property string $ulid
 * @property int $user_id
 * @property array $sources
 * @property array $categories
 * @property array $authors
 */
class UserPreference extends Model
{
    use HasUlids, HasFactory;

    protected $primaryKey = 'ulid';

    protected $fillable = [
        'user_id',
        'sources',
        'categories',
        'authors',
    ];

    protected $casts = [
        'sources' => 'array',
        'categories' => 'array',
        'authors' => 'array',
    ];
}
