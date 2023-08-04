<?php

namespace Kanagama\FormRequestAccessor\Tests\Feature;

use Kanagama\FormRequestAccessor\Tests\TestCase;
use Kanagama\FormRequestAccessor\Tests\TestRequest\TestDisabledRequest;

/**
 * @author k-nagama <k.nagama0632@gmail.com>
 */
class TestDisabledRequestFeatureTest extends TestCase
{
    /**
     * @var TestDisabledRequest
     */
    private TestDisabledRequest $testDisabledRequest;

    /**
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->app->resolving(TestDisabledRequest::class, function ($resolved) {
            $resolved->merge([
                'test_enabled'  => 1,
                'test_disabled' => 1,
            ]);
        });
        /** @var TestDisabledRequest */
        $this->testDisabledRequest = app(TestDisabledRequest::class);
    }

    /**
     * @test
     * @group disabled
     */
    public function disabledプロパティに指定されていないプロパティはallで出力される()
    {
        $all = $this->testDisabledRequest->all();

        $this->assertTrue(isset($all['accessor_enabled']));
        $this->assertTrue(isset($all['test_enabled']));

        $this->assertEquals($this->testDisabledRequest->accessor_enabled, 1);
        $this->assertEquals($this->testDisabledRequest->test_enabled, 1);
    }

    /**
     * @test
     * @group disabled
     */
    public function disabledプロパティに指定されてないプロパティに直接アクセスできる()
    {
        $this->assertEquals($this->testDisabledRequest->accessor_enabled, 1);
        $this->assertEquals($this->testDisabledRequest->test_enabled, 1);
    }

    /**
     * @test
     * @group disabled1
     */
    public function disabledプロパティで指定されているプロパティはallで出力されない()
    {
        $all = $this->testDisabledRequest->all();

        $this->assertFalse(isset($all['accessor_disabled']));
        $this->assertFalse(isset($all['test_disabled']));
    }

    /**
     * @test
     * @group disabled
     */
    public function disabledプロパティに指定されているプロパティには直接アクセスできない()
    {
        $this->assertFalse(isset($this->testDisabledRequest->accessor_disabled));
        $this->assertFalse(isset($this->testDisabledRequest->test_disabled));
    }
}
