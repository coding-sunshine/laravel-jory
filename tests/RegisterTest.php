<?php

namespace JosKolenberg\LaravelJory\Tests;

use JosKolenberg\LaravelJory\Facades\Jory;
use JosKolenberg\LaravelJory\Register\JoryResourcesRegister;
use JosKolenberg\LaravelJory\Tests\JoryResources\AutoRegistered\SongJoryResource;
use JosKolenberg\LaravelJory\Tests\JoryResources\Unregistered\CustomSongJoryResource;
use JosKolenberg\LaravelJory\Tests\JoryResources\Unregistered\CustomSongJoryResource2;
use JosKolenberg\LaravelJory\Tests\JoryResources\AutoRegistered\TagJoryResource;
use JosKolenberg\LaravelJory\Tests\JoryResources\Unregistered\SongJoryResourceWithConfig;
use JosKolenberg\LaravelJory\Tests\JoryResources\Unregistered\SongJoryResourceWithConfigThree;
use JosKolenberg\LaravelJory\Tests\JoryResources\Unregistered\SongJoryResourceWithConfigTwo;
use JosKolenberg\LaravelJory\Tests\JoryResources\Unregistered\TagJoryResourceWithExplicitSelect;

class RegisterTest extends TestCase
{
    /** @test */
    public function it_gives_manually_added_jory_resources_precedence_over_autoregistered_jory_resources_with_the_same_uri()
    {
        $register = app(JoryResourcesRegister::class);
        $this->assertInstanceOf(TagJoryResource::class, $register->getByUri('tag'));

        Jory::register(TagJoryResourceWithExplicitSelect::class);

        $this->assertInstanceOf(TagJoryResourceWithExplicitSelect::class, $register->getByUri('tag'));
    }

    /** @test */
    public function it_gives_any_newly_added_jory_resources_precedence_over_earlier_registered_jory_resources_with_the_same_uri()
    {
        $register = app(JoryResourcesRegister::class);

        $this->assertInstanceOf(SongJoryResource::class, $register->getByUri('song'));

        Jory::register(CustomSongJoryResource::class);
        $this->assertInstanceOf(CustomSongJoryResource::class, $register->getByUri('song'));

        Jory::register(CustomSongJoryResource2::class);
        $this->assertInstanceOf(CustomSongJoryResource2::class, $register->getByUri('song'));
    }

    /** @test */
    public function it_can_give_all_the_available_resources()
    {
        $register = app(JoryResourcesRegister::class);

        $actual = [];
        foreach ($register->getAllJoryResources()->sortBy(function ($joryResource) {
            return $joryResource->getUri();
        }) as $joryResource) {
            $actual[] = $joryResource->getUri();
        }

        $expected = [
            'album',
            'album-cover',
            'band',
            'image',
            'instrument',
            'person',
            'song',
            'tag',
        ];

        $this->assertEquals(json_encode($expected), json_encode($actual));
    }

    /** @test */
    public function it_can_give_the_options_for_the_resource()
    {
        $register = app(JoryResourcesRegister::class);
        $register->add(new SongJoryResourceWithConfig());
        $registration = $register->getByUri('song');
        $actual = $registration->getConfig()->toArray();

        $expected = [
            'fields' => [
                [
                    'field' => 'id',
                    'description' => null,
                    'default' => true,
                ],
                [
                    'field' => 'title',
                    'description' => 'The songs title.',
                    'default' => true,
                ],
                [
                    'field' => 'album_id',
                    'description' => null,
                    'default' => false,
                ],
            ],
            'filters' => [
                [
                    'name' => 'title',
                    'description' => 'Filter on the title.',
                    'operators' => [
                        '=',
                        '!=',
                        '<>',
                        '>',
                        '>=',
                        '<',
                        '<=',
                        '<=>',
                        'like',
                        'not_like',
                        'is_null',
                        'not_null',
                        'in',
                        'not_in',
                    ],
                ],
                [
                    'name' => 'album_id',
                    'description' => 'Filter on the album id.',
                    'operators' => [
                        '=',
                    ],
                ],
            ],
            'sorts' => [
                [
                    'name' => 'id',
                    'description' => null,
                    'default' => false,
                ],
                [
                    'name' => 'title',
                    'description' => 'Order by the title.',
                    'default' => false,
                ],
            ],
            'limit' => [
                'default' => 50,
                'max' => 250,
            ],
            'relations' => [
                [
                    'relation' => 'album',
                    'description' => null,
                    'type' => 'album',
                ],
            ],
        ];

        $this->assertEquals(json_encode($expected), json_encode($actual));
    }

    /** @test */
    public function it_can_show_the_options_for_the_resource_2()
    {
        $register = app(JoryResourcesRegister::class);
        $register->add(new SongJoryResourceWithConfigTwo());
        $registration = $register->getByUri('song');
        $actual = $registration->getConfig()->toArray();

        $expected = [
            'fields' => [
                [
                    'field' => 'id',
                    'description' => null,
                    'default' => true,
                ],
                [
                    'field' => 'title',
                    'description' => null,
                    'default' => true,
                ],
                [
                    'field' => 'album_id',
                    'description' => null,
                    'default' => true,
                ],
            ],
            'filters' => [
                [
                    'name' => 'title',
                    'description' => null,
                    'operators' => [
                        '=',
                        '!=',
                        '<>',
                        '>',
                        '>=',
                        '<',
                        '<=',
                        '<=>',
                        'like',
                        'not_like',
                        'is_null',
                        'not_null',
                        'in',
                        'not_in',
                    ],
                ],
            ],
            'sorts' => [
                [
                    'name' => 'title',
                    'description' => null,
                    'default' => false,
                ],
            ],
            'limit' => [
                'default' => null,
                'max' => null,
            ],
            'relations' => [],
        ];

        $this->assertEquals(json_encode($expected), json_encode($actual));
    }

    /** @test */
    public function it_can_show_the_options_for_the_resource_3()
    {
        $register = app(JoryResourcesRegister::class);
        $register->add(new SongJoryResourceWithConfigThree());
        $registration = $register->getByUri('song');
        $actual = $registration->getConfig()->toArray();

        $expected = [
            'fields' => [
                [
                    'field' => 'id',
                    'description' => null,
                    'default' => true,
                ],
                [
                    'field' => 'title',
                    'description' => null,
                    'default' => true,
                ],
                [
                    'field' => 'album_id',
                    'description' => null,
                    'default' => true,
                ],
            ],
            'filters' => [],
            'sorts' => [
                [
                    'name' => 'title',
                    'description' => null,
                    'default' => [
                        'index' => 2,
                        'order' => 'desc'
                    ],
                ],
                [
                    'name' => 'album_name',
                    'description' => null,
                    'default' => [
                        'index' => 1,
                        'order' => 'asc'
                    ],
                ],
            ],
            'limit' => [
                'default' => 10,
                'max' => 10,
            ],
            'relations' => [],
        ];

        $this->assertEquals(json_encode($expected), json_encode($actual));
    }

    /** @test */
    public function it_can_show_the_options_for_the_resource_4()
    {
        $register = app(JoryResourcesRegister::class);
        $registration = $register->getByUri('band');
        $actual = $registration->getConfig()->toArray();

        $expected = [
            'fields' => [
                [
                    'field' => 'id',
                    'description' => null,
                    'default' => true,
                ],
                [
                    'field' => 'name',
                    'description' => null,
                    'default' => true,
                ],
                [
                    'field' => 'year_start',
                    'description' => 'The year in which the band started.',
                    'default' => true,
                ],
                [
                    'field' => 'year_end',
                    'description' => 'The year in which the band quitted, could be null if band still exists.',
                    'default' => true,
                ],
                [
                    'field' => 'all_albums_string',
                    'description' => null,
                    'default' => false,
                ],
                [
                    'field' => 'titles_string',
                    'description' => null,
                    'default' => false,
                ],
                [
                    'field' => 'first_title_string',
                    'description' => null,
                    'default' => false,
                ],
                [
                    'field' => 'image_urls_string',
                    'description' => null,
                    'default' => false,
                ],
            ],
            'filters' => [
                [
                    'name' => 'has_album_with_name',
                    'description' => 'Filter bands that have an album with a given name.',
                    'operators' => [
                        '=',
                        '!=',
                        '<>',
                        '>',
                        '>=',
                        '<',
                        '<=',
                        '<=>',
                        'like',
                        'not_like',
                        'is_null',
                        'not_null',
                        'in',
                        'not_in',
                    ],
                ],
                [
                    'name' => 'number_of_albums_in_year',
                    'description' => 'Filter the bands that released a given number of albums in a year, pass value and year parameter.',
                    'operators' => ['=', '>', '<', '<=', '>=', '<>', '!='],
                ],
                [
                    'name' => 'id',
                    'description' => 'Try this filter by id!',
                    'operators' => [
                        '=',
                        '>',
                        '<',
                        '<=',
                        '>=',
                        '<>',
                        '!=',
                        'in',
                        'not_in',
                    ],
                ],
                [
                    'name' => 'name',
                    'description' => null,
                    'operators' => [
                        '=',
                        '!=',
                        '<>',
                        '>',
                        '>=',
                        '<',
                        '<=',
                        '<=>',
                        'like',
                        'not_like',
                        'is_null',
                        'not_null',
                        'in',
                        'not_in',
                    ],
                ],
                [
                    'name' => 'year_start',
                    'description' => null,
                    'operators' => [
                        '=',
                        '!=',
                        '<>',
                        '>',
                        '>=',
                        '<',
                        '<=',
                        '<=>',
                        'like',
                        'not_like',
                        'is_null',
                        'not_null',
                        'in',
                        'not_in',
                    ],
                ],
                [
                    'name' => 'year_end',
                    'description' => null,
                    'operators' => [
                        '=',
                        '!=',
                        '<>',
                        '>',
                        '>=',
                        '<',
                        '<=',
                        '<=>',
                        'like',
                        'not_like',
                        'is_null',
                        'not_null',
                        'in',
                        'not_in',
                    ],
                ],
            ],
            'sorts' => [
                [
                    'name' => 'id',
                    'description' => null,
                    'default' => false,
                ],
                [
                    'name' => 'name',
                    'description' => null,
                    'default' => false,
                ],
                [
                    'name' => 'year_start',
                    'description' => null,
                    'default' => false,
                ],
                [
                    'name' => 'year_end',
                    'description' => null,
                    'default' => false,
                ],
            ],
            'limit' => [
                'default' => 30,
                'max' => 120,
            ],
            'relations' => [
                [
                    'relation' => 'albums',
                    'description' => 'Get the related albums for the band.',
                    'type' => 'album',
                ],
                [
                    'relation' => 'people',
                    'description' => null,
                    'type' => 'person',
                ],
                [
                    'relation' => 'songs',
                    'description' => null,
                    'type' => 'song',
                ],
                [
                    'relation' => 'first_song',
                    'description' => null,
                    'type' => 'song',
                ],
                [
                    'relation' => 'images',
                    'description' => null,
                    'type' => 'image',
                ],
            ],
        ];

        $this->assertEquals(json_encode($expected), json_encode($actual));
    }

    /** @test */
    public function it_can_show_the_options_for_the_resource_5()
    {
        $register = app(JoryResourcesRegister::class);
        $registration = $register->getByUri('album');
        $actual = $registration->getConfig()->toArray();

        $expected = [
            'fields' => [
                [
                    'field' => 'id',
                    'description' => null,
                    'default' => true,
                ],
                [
                    'field' => 'name',
                    'description' => null,
                    'default' => true,
                ],
                [
                    'field' => 'band_id',
                    'description' => null,
                    'default' => true,
                ],
                [
                    'field' => 'release_date',
                    'description' => null,
                    'default' => true,
                ],
                [
                    'field' => 'custom_field',
                    'description' => null,
                    'default' => false,
                ],
                [
                    'field' => 'cover_image',
                    'description' => null,
                    'default' => false,
                ],
                [
                    'field' => 'titles_string',
                    'description' => null,
                    'default' => false,
                ],
                [
                    'field' => 'tag_names_string',
                    'description' => null,
                    'default' => false,
                ],
            ],
            'filters' => [
                [
                    'name' => 'number_of_songs',
                    'description' => null,
                    'operators' => [
                        '=',
                        '!=',
                        '<>',
                        '>',
                        '>=',
                        '<',
                        '<=',
                        '<=>',
                        'like',
                        'not_like',
                        'is_null',
                        'not_null',
                        'in',
                        'not_in',
                    ],
                ],
                [
                    'name' => 'has_song_with_title',
                    'description' => null,
                    'operators' => [
                        '=',
                        '!=',
                        '<>',
                        '>',
                        '>=',
                        '<',
                        '<=',
                        '<=>',
                        'like',
                        'not_like',
                        'is_null',
                        'not_null',
                        'in',
                        'not_in',
                    ],
                ],
                [
                    'name' => 'album_cover.album_id',
                    'description' => null,
                    'operators' => [
                        '=',
                        '!=',
                        '<>',
                        '>',
                        '>=',
                        '<',
                        '<=',
                        '<=>',
                        'like',
                        'not_like',
                        'is_null',
                        'not_null',
                        'in',
                        'not_in',
                    ],
                ],
                [
                    'name' => 'has_small_id',
                    'description' => null,
                    'operators' => [
                        '=',
                        '!=',
                        '<>',
                        '>',
                        '>=',
                        '<',
                        '<=',
                        '<=>',
                        'like',
                        'not_like',
                        'is_null',
                        'not_null',
                        'in',
                        'not_in',
                    ],
                ],
                [
                    'name' => 'id',
                    'description' => null,
                    'operators' => [
                        '=',
                        '!=',
                        '<>',
                        '>',
                        '>=',
                        '<',
                        '<=',
                        '<=>',
                        'like',
                        'not_like',
                        'is_null',
                        'not_null',
                        'in',
                        'not_in',
                    ],
                ],
                [
                    'name' => 'name',
                    'description' => null,
                    'operators' => [
                        '=',
                        '!=',
                        '<>',
                        '>',
                        '>=',
                        '<',
                        '<=',
                        '<=>',
                        'like',
                        'not_like',
                        'is_null',
                        'not_null',
                        'in',
                        'not_in',
                    ],
                ],
                [
                    'name' => 'band_id',
                    'description' => null,
                    'operators' => [
                        '=',
                        '!=',
                        '<>',
                        '>',
                        '>=',
                        '<',
                        '<=',
                        '<=>',
                        'like',
                        'not_like',
                        'is_null',
                        'not_null',
                        'in',
                        'not_in',
                    ],
                ],
                [
                    'name' => 'release_date',
                    'description' => null,
                    'operators' => [
                        '=',
                        '!=',
                        '<>',
                        '>',
                        '>=',
                        '<',
                        '<=',
                        '<=>',
                        'like',
                        'not_like',
                        'is_null',
                        'not_null',
                        'in',
                        'not_in',
                    ],
                ],
            ],
            'sorts' => [
                [
                    'name' => 'number_of_songs',
                    'description' => null,
                    'default' => false,
                ],
                [
                    'name' => 'band_name',
                    'description' => null,
                    'default' => false,
                ],
                [
                    'name' => 'alphabetic_name',
                    'description' => null,
                    'default' => false,
                ],
                [
                    'name' => 'id',
                    'description' => null,
                    'default' => false,
                ],
                [
                    'name' => 'name',
                    'description' => null,
                    'default' => false,
                ],
                [
                    'name' => 'band_id',
                    'description' => null,
                    'default' => false,
                ],
                [
                    'name' => 'release_date',
                    'description' => null,
                    'default' => false,
                ],
            ],
            'limit' => [
                'default' => null,
                'max' => null,
            ],
            'relations' => [
                [
                    'relation' => 'songs',
                    'description' => null,
                    'type' => 'song',
                ],
                [
                    'relation' => 'band',
                    'description' => null,
                    'type' => 'band',
                ],
                [
                    'relation' => 'cover',
                    'description' => null,
                    'type' => 'album-cover',
                ],
                [
                    'relation' => 'album_cover',
                    'description' => null,
                    'type' => 'album-cover',
                ],
                [
                    'relation' => 'snake_case_album_cover',
                    'description' => null,
                    'type' => 'album-cover',
                ],
                [
                    'relation' => 'custom_songs_2',
                    'description' => null,
                    'type' => 'song',
                ],
                [
                    'relation' => 'custom_songs_3',
                    'description' => null,
                    'type' => 'song',
                ],
                [
                    'relation' => 'tags',
                    'description' => null,
                    'type' => 'tag',
                ],
            ],
        ];

        $this->assertEquals(json_encode($expected), json_encode($actual));
    }}
