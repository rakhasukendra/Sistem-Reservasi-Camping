<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Article extends Model
{
    protected $fillable = ['title', 'slug', 'excerpt', 'image', 'content', 'is_published'];

    protected function casts(): array
    {
        return ['is_published' => 'boolean'];
    }

    /**
     * Resolve the optional guide image, which is either an external URL or a public disk path.
     */
    public function imageUrl(): ?string
    {
        if (! $this->image) {
            return null;
        }

        return Str::startsWith($this->image, ['http://', 'https://'])
            ? $this->image
            : Storage::disk('public')->url($this->image);
    }
}
