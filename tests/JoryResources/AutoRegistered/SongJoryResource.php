<?php

namespace JosKolenberg\LaravelJory\Tests\JoryResources\AutoRegistered;

use JosKolenberg\LaravelJory\JoryResource;
use JosKolenberg\LaravelJory\Tests\Models\Song;

class SongJoryResource extends JoryResource
{
    protected $modelClass = Song::class;

    protected function configure(): void
    {
        // Fields
        $this->field('id')->filterable()->sortable();
        $this->field('title')->filterable()->sortable();
        $this->field('album_id')->filterable()->sortable();

        // Custom attributes
        $this->field('album_name')->load('album')->hideByDefault();

        // Custom filters
        $this->filter('album_name');

        // Relations
        $this->relation('album');
        $this->relation('test_relation_without_jory_resource');
    }
}
