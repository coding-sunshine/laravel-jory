<?php

namespace JosKolenberg\LaravelJory\Tests;

use JosKolenberg\LaravelJory\Exceptions\RegistrationNotFoundException;
use JosKolenberg\LaravelJory\Register\JoryResourcesRegister;
use JosKolenberg\LaravelJory\Tests\Models\Groupie;

class JoryRegisterTest extends TestCase
{
    /** @test */
    public function it_throws_an_exception_when_no_associated_jory_resource_is_found()
    {
        $response = $this->json('GET', 'jory/error-person/1', []);

        $response->assertStatus(500);

        $this->expectException(RegistrationNotFoundException::class);
        $this->expectExceptionMessage('No joryResource found for model JosKolenberg\LaravelJory\Tests\Models\Groupie. Does JosKolenberg\LaravelJory\Tests\Models\Groupie have an associated JoryResource?');

        $register = app(JoryResourcesRegister::class);

        $register->getByModelClass(Groupie::class);
    }

}
