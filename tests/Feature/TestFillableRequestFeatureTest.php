<?php

namespace Kanagama\FormRequestAccessor\Tests\Feature;

use Kanagama\FormRequestAccessor\Tests\TestCase;
use Kanagama\FormRequestAccessor\Tests\TestRequest\TestFillableRequest;

/**
 * @author k-nagama <k.nagama0632@gmail.com>
 */
class TestFillableRequestFeatureTest extends TestCase
{
    /**
     * @var TestFillableRequest
     */
    private TestFillableRequest $testFillableRequest;

    /**
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->app->resolving(TestFillableRequest::class, function ($resolved) {
            $resolved->merge([
                'test_fillable' => 1,
                'test_guarded'  => 1,
            ]);
        });
        /** @var TestFillableRequest */
        $this->testFillableRequest = app(TestFillableRequest::class);
    }

    /**
     * @test
     * @group fillable
     */
    public function fillableプロパティに指定されていないプロパティにはallでアクセスできない()
    {
        $all = $this->testFillableRequest->all();

        $this->assertFalse(isset($all['accessor_guarded']));
        $this->assertFalse(isset($all['test_guarded']));
    }

    /**
     * @test
     * @group fillable
     */
    public function fillableプロパティに指定されていないプロパティにもプロパティとしてはアクセスできる()
    {
        $this->assertEquals($this->testFillableRequest->accessor_guarded, 1);
        $this->assertEquals($this->testFillableRequest->test_guarded, 1);
    }

    /**
     * @test
     * @group fillable
     */
    public function fillableプロパティに指定されているプロパティのみallでアクセスできる()
    {
        $all = $this->testFillableRequest->all();

        $this->assertTrue(isset($all['accessor_fillable']));
        $this->assertTrue(isset($all['test_fillable']));
    }

    /**
     * @test
     * @group fillable
     */
    public function fillableプロパティに指定されているプロパティにはプロパティとしてもアクセスできる()
    {
        $this->assertEquals($this->testFillableRequest->accessor_fillable, 1);
        $this->assertEquals($this->testFillableRequest->test_fillable, 1);
    }

    /**
     * @test
     * @group fillable
     */
    public function fillableプロパティに指定されているプロパティはallで出力される()
    {
        $all = $this->testFillableRequest->all();

        $this->assertTrue(isset($all['accessor_fillable']));
        $this->assertTrue(isset($all['test_fillable']));
    }

    /**
     * @test
     * @group fillable
     */
    public function fillableプロパティに指定されているプロパティは直接アクセスできる()
    {
        $this->assertEquals($this->testFillableRequest->accessor_fillable, 1);
        $this->assertEquals($this->testFillableRequest->test_fillable, 1);
    }
}
