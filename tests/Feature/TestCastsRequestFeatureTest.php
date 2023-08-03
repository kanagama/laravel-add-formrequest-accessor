<?php

namespace Kanagama\FormRequestAccessor\Tests\Feature;

use Carbon\Carbon;
use Kanagama\FormRequestAccessor\Tests\TestCase;
use Kanagama\FormRequestAccessor\Tests\TestRequest\TestCastsRequest;

/**
 * @author k-nagama <k.nagama0632@gmail.com>
 */
class TestCastsRequestFeatureTest extends TestCase
{
    /**
     * @var TestCastsRequest
     */
    private TestCastsRequest $testCastRequest;

    /**
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->app->resolving(TestCastsRequest::class, function ($resolved) {
            $resolved->merge([
                'int'    => '1',
                'string' => 1,
                'bool'   => 1,
                'carbon' => date('Y-m-d'),
            ]);
        });
        /** @var TestCastsRequest */
        $this->testCastRequest = app(TestCastsRequest::class);
    }

    /**
     * @test
     * @group casts
     */
    public function 文字列型プロパティが数値型にキャストされている()
    {
        $this->assertIsInt($this->testCastRequest->int);
    }

    /**
     * @test
     * @group casts1
     */
    public function 文字列型アクセサが数値型にキャストされている()
    {
        $this->assertIsInt($this->testCastRequest->cast_int);
    }

    /**
     * @test
     * @group casts
     */
    public function 数値型プロパティが文字列型にキャストされている()
    {
        $this->assertIsString($this->testCastRequest->string);
    }

    /**
     * @test
     * @group casts
     */
    public function 数値型アクセサが文字列型にキャストされている()
    {
        $this->assertIsString($this->testCastRequest->cast_string);
    }

    /**
     * @test
     * @group casts
     */
    public function 数値型プロパティがbool型にキャストされている()
    {
        $this->assertIsBool($this->testCastRequest->bool);
    }

    /**
     * @test
     * @group casts
     */
    public function 数値型アクセサがbool型にキャストされている()
    {
        $this->assertIsBool($this->testCastRequest->cast_bool);
    }

    /**
     * @test
     * @group casts
     */
    public function 文字列型プロパティがCarbon型にキャストされている()
    {
        $this->assertInstanceOf(
            Carbon::class,
            $this->testCastRequest->carbon
        );
    }

    /**
     * @test
     * @group casts
     */
    public function 文字列型アクセサがCarbon型にキャストされている()
    {
        $this->assertInstanceOf(
            Carbon::class,
            $this->testCastRequest->cast_carbon
        );
    }

    /**
     * @test
     * @gruop input
     */
    public function inputの第2引数が正常に設定される()
    {
        $this->assertEquals($this->testCastRequest->input('not_accessor_test', true), true);
        $this->assertEquals($this->testCastRequest->input('not_accessor_test', []), []);
        $this->assertEquals($this->testCastRequest->input('not_accessor_test', ''), '');
    }

    /**
     * @test
     * @group get
     */
    public function getの第2引数が正常に設定される()
    {
        $this->assertEquals($this->testCastRequest->get('not_accessor_test', true), true);
        $this->assertEquals($this->testCastRequest->get('not_accessor_test', []), []);
        $this->assertEquals($this->testCastRequest->get('not_accessor_test', ''), '');
    }
}
