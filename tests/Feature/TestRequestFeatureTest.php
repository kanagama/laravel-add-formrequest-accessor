<?php

namespace Kanagama\FormRequestAccessor\Tests\Feature;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ValidatedInput;
use Kanagama\FormRequestAccessor\Tests\TestCase;
use Kanagama\FormRequestAccessor\Tests\TestRequest\TestRequest;

/**
 * @author k-nagama <k.nagama0632@gmail.com>
 */
class TestRequestFeatureTest extends TestCase
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

        Route::shouldReceive('currentRouteAction')
            ->andReturn('App\Controller\TestController@index');
    }

    /**
     * @test
     * @group immutable1
     */
    public function immutableではないRequestクラスでmergeできる()
    {
        $this->assertNull($this->testRequest->merge);

        $this->testRequest->merge([
            'merge' => 1,
        ]);
        $this->assertEquals($this->testRequest->merge, 1);
        $this->assertEquals($this->testRequest->test_offset_unset, 1);
        $this->assertEquals($this->testRequest->test_offset_set, 2);
    }

    /**
     * @test
     * @group immutable
     */
    public function immutableではないRequestクラスでoffsetUnsetできる()
    {
        $this->assertEquals($this->testRequest->test_offset_unset, 1);

        $this->testRequest->offsetUnset('test_offset_unset');
        $this->assertNull($this->testRequest->test_offset_unset);
        $this->assertEquals($this->testRequest->test_offset_set, 2);
    }

    /**
     * @test
     * @group immutable
     */
    public function immutableではないRequestクラスでoffsetSetできる()
    {
        $this->assertEquals($this->testRequest->test_offset_set, 2);

        $this->testRequest->offsetSet('test_offset_set', 3);
        $this->assertEquals($this->testRequest->test_offset_set, 3);
    }

    /**
     * @test
     * @group immutable
     */
    public function immutableではないRequestクラスでreplaceできる()
    {
        $this->assertNull($this->testRequest->merge);

        $this->testRequest->replace([
            'merge' => 1,
        ]);
        $this->assertEquals($this->testRequest->merge, 1);

        // 上書きされて消えている
        $this->assertNull($this->testRequest->test_offset_unset);
        $this->assertNull($this->testRequest->test_offset_set);
    }

    /**
     * @test
     */
    public function getControllerで正常にコントローラー名が取得できる()
    {
        $this->assertNotEmpty($this->testRequest->getController());
    }

    /**
     * @test
     */
    public function getActionで正常にアクション名が取得できる()
    {
        $this->assertNotEmpty($this->testRequest->getAction());
    }

    /**
     * @test
     */
    public function validatedが動作する()
    {
        $this->assertIsArray($this->testRequest->validated());
    }

    /**
     * @test
     * @group validated
     */
    public function validatedは入力パラメータのみ()
    {
        $this->assertSame(
            $this->testRequest->validated(),
            [
                'test_offset_unset' => 1,
            ]
        );
    }

    /**
     * @test
     */
    public function safeが動作する()
    {
        $this->assertInstanceOf(
            ValidatedInput::class,
            $this->testRequest->safe()
        );
    }

    /**
     * @test
     */
    public function queryが動作する()
    {
        $this->assertIsArray(
            $this->testRequest->query()
        );
    }

    /**
     * @test
     */
    public function onlyが動作する()
    {
        $this->assertIsArray(
            $this->testRequest->only('test_offset_unset')
        );
    }

    /**
     * @test
     */
    public function exceptが動作する()
    {
        $this->assertIsArray(
            $this->testRequest->except('test_offset_unset')
        );
    }

    /**
     * @test
     */
    public function beforeAllが動作する()
    {
        $this->assertIsArray(
            $this->testRequest->beforeAll()
        );
    }

    /**
     * @test
     */
    public function beforeが動作する()
    {
        $this->assertInstanceOf(
            FormRequest::class,
            $this->testRequest->before()
        );
    }
}
