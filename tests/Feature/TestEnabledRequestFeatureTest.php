<?php

namespace Kanagama\FormRequestAccessor\Tests\Feature;

use Kanagama\FormRequestAccessor\Tests\TestCase;
use Kanagama\FormRequestAccessor\Tests\TestRequest\TestEnabledRequest;

/**
 * @author k-nagama <k.nagama0632@gmail.com>
 */
class TestEnabledRequestFeatureTest extends TestCase
{
    /**
     * @var TestEnabledRequest
     */
    private TestEnabledRequest $testEnabledRequest;

    /**
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->app->resolving(TestEnabledRequest::class, function ($resolved) {
            $resolved->merge([
                'test_enabled'  => 1,
                'test_disabled' => 1,
            ]);
        });
        /** @var TestEnabledRequest */
        $this->testEnabledRequest = app(TestEnabledRequest::class);
    }

    /**
     * @test
     * group enabled
     */
    public function enabledプロパティに指定されているプロパティはallで出力される()
    {
        $all = $this->testEnabledRequest->all();

        $this->assertTrue(isset($all['accessor_enabled']));
        $this->assertTrue(isset($all['test_enabled']));
    }

    /**
     * @test
     * @group enabled
     */
    public function enabledプロパティに指定されているプロパティにアクセスできる()
    {
        $this->assertEquals($this->testEnabledRequest->accessor_enabled, 1);
        $this->assertEquals($this->testEnabledRequest->test_enabled, 1);
    }

    /**
     * @test
     * @group enabled
     */
    public function enabledプロパティで指定されていないプロパティはallで出力されない()
    {
        $all = $this->testEnabledRequest->all();

        $this->assertFalse(isset($all['accessor_disabled']));
        $this->assertFalse(isset($all['test_disabled']));
    }

    /**
     * @test
     * @group enabled
     */
    public function enabledプロパティで指定されていないプロパティに直接アクセスできない()
    {
        $this->assertFalse(property_exists($this->testEnabledRequest, 'accessor_disabled'));
        $this->assertFalse(property_exists($this->testEnabledRequest, 'test_disabled'));
    }
}
