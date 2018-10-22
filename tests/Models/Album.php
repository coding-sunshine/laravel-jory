<?php

namespace JosKolenberg\LaravelJory\Tests\Models;

use JosKolenberg\LaravelJory\Traits\JoryTrait;
use JosKolenberg\LaravelJory\Tests\JoryBuilders\AlbumJoryBuilder;

class Album extends Model
{
    use JoryTrait;

    protected $table = 'albums';

    protected $casts = [
        'id' => 'integer',
        'band_id' => 'integer',
    ];

    public function songs()
    {
        return $this->hasMany(Song::class);
    }

    public function band()
    {
        return $this->belongsTo(Band::class);
    }

    public static function getJoryBuilder()
    {
        return new AlbumJoryBuilder();
    }

    public function cover()
    {
        return $this->hasOne(AlbumCover::class);
    }
}
