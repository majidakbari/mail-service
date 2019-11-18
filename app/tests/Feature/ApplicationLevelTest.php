<?php

namespace Tests\Feature;

use Faker\Generator;
use Illuminate\Foundation\Testing\WithFaker;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

/**
 * Class ApplicationLevelTest
 * @package Tests\Feature
 */
class ApplicationLevelTest extends TestCase
{
    use WithFaker;

    const HTTP_METHODS = ['get', 'post', 'patch', 'put', 'head', 'options', 'delete'];

    /**
     * @test
     * @group FeatureApplicationLevelTests
     */
    public function routeNotFoundTest(): void
    {
        $response = $this->json($this->faker->randomElement(self::HTTP_METHODS), $this->faker->randomAscii);

        $response->assertStatus(Response::HTTP_NOT_FOUND)->assertJson([
            "error" => 'NotFoundHttpException',
            "message" => trans('app.NotFoundHttpException')
        ]);
    }

    /**
     * @test
     * @group FeatureApplicationLevelTests
     * @dataProvider  wrongUriAndMethodDataProvider
     * @param string $method
     * @param string $uri
     */
    public function methodNotAllowedTest($method, $uri): void
    {
        $response = $this->json($method, $uri);

        $response->assertStatus(Response::HTTP_METHOD_NOT_ALLOWED)->assertJson([
            "error" => 'MethodNotAllowedHttpException',
            "message" => trans('app.MethodNotAllowedHttpException')
        ]);
    }


    /**
     * @return array
     */
    public function wrongUriAndMethodDataProvider(): array
    {
        //we do not have proper access to `$this` context in the providers
        //so we have to resolve dependencies directly from the service container
        $this->refreshApplication();
        /** @var Generator $faker */
        $faker = resolve(Generator::class);

        $emailRoutesIllegalMethods = ['get', 'put', 'patch', 'head', 'delete'];
        $logRouteIllegalMethods = ['post', 'put', 'patch', 'delete'];

        return [
            [
                $faker->randomElement($logRouteIllegalMethods),
                route('log.index', [], false)
            ],
            [
                $faker->randomElement($emailRoutesIllegalMethods),
                route('email.send.multiple', [], false)
            ],
            [
                $faker->randomElement($emailRoutesIllegalMethods),
                route('email.send.single', [], false)
            ]
        ];
    }
}
