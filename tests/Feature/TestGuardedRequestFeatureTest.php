<?php

namespace Kanagama\FormRequestAccessor\Tests\Feature;

use Kanagama\FormRequestAccessor\Tests\TestCase;
use Kanagama\FormRequestAccessor\Tests\TestRequest\TestGuardedRequest;

/**
 * @author k-nagama <k.nagama0632@gmail.com>
 */
class TestGuardedRequestFeatureTest extends TestCase
{
    /**
     * @var TestGuardedRequest
     */
    private TestGuardedRequest $testGuardedRequest;

    /**
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->app->resolving(TestGuardedRequest::class, function ($resolved) {
            $resolved->merge([
                'test_guarded'  => 1,
                'test'          => 1,
            ]);
        });
        /** @var TestGuardedRequest */
        $this->testGuardedRequest = app(TestGuardedRequest::class);
    }

    /**
     * @test
     * @group guarded
     */
    public function guardedプロパティに指定されていないプロパティはallで出力される()
    {
        $all = $this->testGuardedRequest->all();

        $this->assertTrue(isset($all['test']));
        $this->assertTrue(isset($all['accessor_int']));
        $this->assertTrue(isset($all['accessor_string']));
    }

    /**
     * @test
     * @group guarded
     */
    public function guardedプロパティに指定されていないプロパティは直接アクセスできる()
    {
        $this->assertEquals($this->testGuardedRequest->test_guarded, 1);
        $this->assertEquals($this->testGuardedRequest->test, 1);
        $this->assertEquals($this->testGuardedRequest->accessor_guarded, 'a');
        $this->assertEquals($this->testGuardedRequest->accessor_int, 1);
        $this->assertEquals($this->testGuardedRequest->accessor_string, '1');
    }

    /**
     * @test
     * @group guarded
     */
    public function guardedプロパティに指定されているプロパティはallで出力されない()
    {
        $all = $this->testGuardedRequest->all();

        $this->assertFalse(isset($all['test_guarded']));
        $this->assertFalse(isset($all['accessor_guarded']));
    }

    /**
     * @test
     * @group guarded
     */
    public function guardedプロパティに指定されているプロパティでもプロパティとしてはアクセスできる()
    {
        $this->assertEquals($this->testGuardedRequest->test_guarded, 1);
        $this->assertEquals($this->testGuardedRequest->accessor_guarded, 'a');
    }
}
