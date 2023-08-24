<?php

namespace Kanagama\FormRequestAccessor\Tests\Feature;

use Kanagama\FormRequestAccessor\Tests\TestCase;
use Kanagama\FormRequestAccessor\Tests\TestRequest\TestRequest;

/**
 * @author k-nagama <k.nagama0632@gmail.com>
 */
class BeforeMethodFeatureTest extends TestCase
{
    /**
     * @var TestRequest
     */
    private TestRequest $testRequest;

    /**
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->app->resolving(TestRequest::class, function ($resolved) {
            $resolved->merge([
                'test_offset_unset' => 1,
                'test_offset_set'   => 2,
            ]);
        });
        /** @var TestRequest */
        $this->testRequest = app(TestRequest::class);
    }

    /**
     * before で自クラスオブジェクトが返却される
     *
     * @test
     */
    public function before()
    {
        $this->assertInstanceOf(TestRequest::class, $this->testRequest->before());
    }
}
