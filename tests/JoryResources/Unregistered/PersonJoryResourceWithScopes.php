<?php

namespace JosKolenberg\LaravelJory\Tests\JoryResources\Unregistered;

use JosKolenberg\LaravelJory\Config\Sort;
use JosKolenberg\LaravelJory\JoryResource;
use JosKolenberg\LaravelJory\Tests\JoryResources\Scopes\FirstNameSort;
use JosKolenberg\LaravelJory\Tests\JoryResources\Scopes\SpecialFirstNameFilter;
use JosKolenberg\LaravelJory\Tests\Models\Person;

class PersonJoryResourceWithScopes extends JoryResource
{
    protected $modelClass = Person::class;

    protected function configure(): void
    {
        // Fields
        $this->field('id')->filterable()->sortable();
        $this->field('first_name')->filterable(function ($filter){
            $filter->scope(new SpecialFirstNameFilter());
        })->sortable();
        $this->field('last_name')->filterable()->sortable();
        $this->field('date_of_birth')->filterable()->sortable(function(Sort $sort){
            $sort->scope(new FirstNameSort);
        });
        $this->field('full_name')->filterable();

        // Custom attributes
        $this->field('instruments_string')->load('instruments')->hideByDefault();
        $this->field('first_image_url')->load('first_image')->hideByDefault();

        $this->filter('band.albums.songs.title');
        $this->filter('instruments.name');

        // Relations
        $this->relation('instruments');
        $this->relation('first_image');
    }

    public function scopeFullNameFilter($query, $operator, $data)
    {
        $query->where('first_name', 'like', '%'.$data.'%');
        $query->orWhere('last_name', 'like', '%'.$data.'%');
    }
}
