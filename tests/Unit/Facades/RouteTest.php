<?php

namespace Kanagama\FormRequestAccessor\Tests\Unit\Facades;

use Illuminate\Support\Facades\Route as RouteFacade;
use Kanagama\FormRequestAccessor\Facades\Route;
use Kanagama\FormRequestAccessor\Tests\TestCase;

/**
 * @author k-nagama <k.nagama0632@gmail.com>
 */
class RouteTest extends TestCase
{
    /**
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        RouteFacade::shouldReceive('currentRouteAction')
            ->andReturn('App\Controller\TestController@index');
    }

    /**
     * @test
     */
    public function getController()
    {
        $this->assertEquals(
            'TestController',
            Route::getController()
        );
    }

    /**
     * @test
     */
    public function getAction()
    {
        $this->assertEquals(
            'index',
            Route::getAction()
        );
    }
}
