<?php

namespace JosKolenberg\LaravelJory\Tests\JoryResources;

use JosKolenberg\Jory\Jory;
use JosKolenberg\LaravelJory\Config\Config;
use JosKolenberg\LaravelJory\JoryBuilder;
use Illuminate\Database\Eloquent\Collection;
use JosKolenberg\LaravelJory\JoryResource;
use JosKolenberg\LaravelJory\Tests\Models\Song;

class SongJoryResourceWithAfterFetchHook extends JoryResource
{
    protected $modelClass = Song::class;

    protected $uri = 'song-with-after-fetch';

    /**
     * Configure the JoryBuilder.
     *
     * @param  \JosKolenberg\LaravelJory\Config\Config $config
     */
    protected function configure(): void
    {
        // Fields
        $this->field('id')->filterable()->sortable();
        $this->field('title')->filterable()->sortable();
        $this->field('album_id')->filterable()->sortable();
        $this->field('custom_field')->hideByDefault();

        $this->filter('custom_filter_field');
        $this->sort('custom_sort_field');
    }

    public function afterFetch(Collection $collection): Collection
    {
        if($this->hasField('custom_field')){
            $collection->each(function ($song){
                $song->title = 'altered';
                $song->custom_field = 'custom_value';
            });
        }

        if($this->hasFilter('custom_filter_field')){
            $collection->each(function ($song){
                $song->title = 'altered by filter';
            });
        }

        if($this->hasSort('custom_sort_field')){
            $collection->each(function ($song){
                $song->title = 'altered by sort';
            });
        }

        return $collection->filter(function ($model) {
            return $model->id > 100;
        });
    }

    public function scopeCustomFilterFieldFilter($query, $operator, $data)
    {
        // Do nothing
    }

    public function scopeCustomSortFieldSort($query, $order)
    {
        // Do nothing
    }
}