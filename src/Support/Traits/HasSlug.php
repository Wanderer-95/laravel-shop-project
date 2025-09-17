<?php

namespace Support\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

trait HasSlug
{
    protected static function bootHasSlug(): void
    {
        static::creating(function (Model $model) {
            $slug = $model->slug ?? Str::slug($model->{static::slugFrom()});
            $model->slug = self::slugUnique($model, $slug);
        });
    }

    protected static function slugFrom(): string
    {
        return 'title';
    }

    protected static function slugUnique(Model $model, string $baseSlug): string
    {
        $slug = $baseSlug;
        $count = 0;

        while ($model->newModelQuery()->where('slug', $slug)->exists()) {
            $count++;
            $slug = $baseSlug.'-'.$count;
        }

        return $slug;
    }
}
