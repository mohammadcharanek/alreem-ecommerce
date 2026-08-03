<?php

namespace Tests\Feature\Security;

use App\Http\Middleware\AdminMiddleware;
use Illuminate\Routing\Route;
use Tests\TestCase;

class ImportAuthorizationTest extends TestCase
{
    public function test_every_import_route_requires_auth_admin_and_throttling(): void
    {
        foreach ([
            'products.import.form',
            'products.import',
            'products.import.images',
        ] as $routeName) {
            /** @var Route $route */
            $route = app('router')->getRoutes()->getByName($routeName);

            $this->assertNotNull($route);

            $middleware = $route->gatherMiddleware();

            $this->assertContains('auth', $middleware);
            $this->assertContains(AdminMiddleware::class, $middleware);
            $this->assertContains('throttle:5,1', $middleware);
        }
    }
}
