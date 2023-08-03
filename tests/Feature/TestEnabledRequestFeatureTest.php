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

        // enabled
        $this->testEnabledRequest = new TestEnabledRequest([
            'test_enabled'  => 1,
            'test_disabled' => 1,
        ]);
        $this->testEnabledRequest->passedValidation();
    }

    /**
     * @test
     * group enabled
     */
    public function enabledプロパティに指定されているプロパティにはアクセスできる()
    {
        $all = $this->testEnabledRequest->all();

        $this->assertTrue(isset($all['accessor_enabled']));
        $this->assertTrue(isset($all['test_enabled']));

        $this->assertEquals($this->testEnabledRequest->accessor_enabled, 1);
        $this->assertEquals($this->testEnabledRequest->test_enabled, 1);
    }

    /**
     * @test
     * @group enabled
     */
    public function enabledプロパティで指定されていないプロパティにはアクセスできない()
    {
        $all = $this->testEnabledRequest->all();

        $this->assertFalse(isset($all['accessor_disabled']));
        $this->assertFalse(isset($all['test_disabled']));

        $this->assertFalse(property_exists($this->testEnabledRequest, 'accessor_disabled'));
        $this->assertFalse(property_exists($this->testEnabledRequest, 'test_disabled'));
    }
}
