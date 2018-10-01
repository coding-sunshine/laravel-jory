<?php

namespace JosKolenberg\LaravelJory\Tests\Models;

use JosKolenberg\LaravelJory\Tests\JoryBuilders\AlbumJoryBuilder;
use JosKolenberg\LaravelJory\Traits\JoryTrait;

class Album extends Model
{
    use JoryTrait;

    protected $table = 'albums';

    public function songs()
    {
        return $this->hasMany(Song::class);
    }

    public static function getJoryBuilder()
    {
        return new AlbumJoryBuilder();
    }

}
