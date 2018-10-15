<?php

namespace JosKolenberg\LaravelJory\Tests\Models;

use JosKolenberg\LaravelJory\Traits\JoryTrait;

class Song extends Model
{
    use JoryTrait;

    protected $table = 'songs';

    public function album()
    {
        return $this->belongsTo(Album::class);
    }
}
