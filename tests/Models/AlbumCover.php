<?php

namespace JosKolenberg\LaravelJory\Tests\Models;

use JosKolenberg\LaravelJory\Traits\JoryTrait;

class AlbumCover extends Model
{
    use JoryTrait;

    protected $table = 'album_covers';

    protected $casts = [
        'id' => 'integer',
        'album_id' => 'integer',
    ];

    public function album()
    {
        return $this->belongsTo(Album::class);
    }
}
