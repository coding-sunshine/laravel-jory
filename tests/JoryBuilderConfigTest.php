<?php

namespace JosKolenberg\LaravelJory\Tests;

class JoryBuilderConfigTest extends TestCase
{
    protected function getEnvironmentSetUp($app)
    {
        parent::getEnvironmentSetUp($app);
        $app['config']->set('jory.response.data-key', null);
        $app['config']->set('jory.response.errors-key', null);
    }

    /** @test */
    public function it_can_return_data_in_the_root_when_data_key_is_configured_null()
    {
        $response = $this->json('GET', 'jory/band/3', [
            'jory' => '{"fld":["name"]}',
        ]);

        $expected = [
            'name' => 'Beatles',
        ];
        $response->assertStatus(200)->assertExactJson($expected)->assertJson($expected);
    }

    /** @test */
    public function it_can_return_data_in_the_root_when_data_key_is_configured_null_2()
    {
        $response = $this->json('GET', 'jory', [
            'band:3_as_beatles' => '{"fld":["name"]}',
        ]);

        $expected = [
            'beatles' => [
                'name' => 'Beatles',
            ],
        ];
        $response->assertStatus(200)->assertExactJson($expected)->assertJson($expected);
    }

    /** @test */
    public function it_can_return_errors_in_the_root_when_data_key_is_configured_null()
    {
        $response = $this->json('GET', 'jory/band/3', [
            'jory' => '{"fld":["naame"]}',
        ]);

        $expected = [
            'Field "naame" not available. Did you mean "name"? (Location: fields.naame)',
        ];
        $response->assertStatus(422)->assertExactJson($expected)->assertJson($expected);
    }

    /** @test */
    public function it_can_return_errors_in_the_root_when_data_key_is_configured_null_2()
    {
        $response = $this->json('GET', 'jory', [
            'band:3_as_beatles' => '{"fld":["naame"]}',
        ]);

        $expected = [
            'band:3_as_beatles: Field "naame" not available. Did you mean "name"? (Location: fields.naame)',
        ];
        $response->assertStatus(422)->assertExactJson($expected)->assertJson($expected);
    }
}
