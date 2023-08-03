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

        // disabled
        $this->testDisabledRequest = new TestDisabledRequest([
            'test_enabled'  => 1,
            'test_disabled' => 1,
        ]);
        $this->testDisabledRequest->passedValidation();
    }

    /**
     * @test
     * @group disabled
     */
    public function disabledプロパティに指定されていないプロパティにはアクセスできる()
    {
        $all = $this->testDisabledRequest->all();

        $this->assertTrue(isset($all['accessor_enabled']));
        $this->assertTrue(isset($all['test_enabled']));

        $this->assertEquals($this->testDisabledRequest->accessor_enabled, 1);
        $this->assertEquals($this->testDisabledRequest->test_enabled, 1);
    }

    /**
     * @test
     * @group disabled1
     */
    public function disabledプロパティで指定されているプロパティにはアクセスできない()
    {
        $all = $this->testDisabledRequest->all();

        $this->assertFalse(isset($all['accessor_disabled']));
        $this->assertFalse(isset($all['test_disabled']));

        $this->assertFalse(isset($this->testDisabledRequest->accessor_disabled));
        $this->assertFalse(isset($this->testDisabledRequest->test_disabled));
    }
}
