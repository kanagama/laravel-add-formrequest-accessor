<?php

namespace Kanagama\FormRequestAccessor\Tests\Feature;

use Kanagama\FormRequestAccessor\Tests\TestCase;
use Kanagama\FormRequestAccessor\Tests\TestRequest\TestEmptyDisabledRequest;

/**
 * @author k-nagama <k.nagama0632@gmail.com>
 */
class TestEmptyDisabledRequestFeatureTest extends TestCase
{
    /**
     * @var TestEmptyDisabledRequest
     */
    private TestEmptyDisabledRequest $testEmptyDisabledRequest;

    /**
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        // empty_disabled
        $this->testEmptyDisabledRequest = new TestEmptyDisabledRequest([
            'null'         => null,
            'int'          => 1,
            'int_zero'     => 0,
            'string_empty' => '',
            'string'       => 'a',
        ]);
        $this->testEmptyDisabledRequest->passedValidation();
    }

    /**
     * @test
     * @group empty_disabled
     */
    public function empty_disabledがtrueの場合、emptyのプロパティにアクセスできない()
    {
        $all = $this->testEmptyDisabledRequest->all();

        $this->assertFalse(isset($all['accessor_null']));
        $this->assertFalse(isset($all['null']));
        $this->assertFalse(isset($all['accessor_string_empty']));
        $this->assertFalse(isset($all['string_empty']));
        $this->assertFalse(isset($all['accessor_int_zero']));
        $this->assertFalse(isset($all['int_zero']));

        $this->assertFalse(property_exists($this->testEmptyDisabledRequest, 'accessor_null'));
        $this->assertFalse(property_exists($this->testEmptyDisabledRequest, 'null'));
        $this->assertFalse(property_exists($this->testEmptyDisabledRequest, 'accessor_string_empty'));
        $this->assertFalse(property_exists($this->testEmptyDisabledRequest, 'string_empty'));
        $this->assertFalse(property_exists($this->testEmptyDisabledRequest, 'accessor_int_zero'));
        $this->assertFalse(property_exists($this->testEmptyDisabledRequest, 'int_zero'));
    }

    /**
     * @test
     * @group empty_disabled
     */
    public function empty_disabledがtrueの場合、emptyでなければアクセスできる()
    {
        $all = $this->testEmptyDisabledRequest->all();

        $this->assertTrue(isset($all['accessor_int']));
        $this->assertTrue(isset($all['accessor_string']));
        $this->assertTrue(isset($all['int']));
        $this->assertTrue(isset($all['string']));

        $this->assertFalse(property_exists($this->testEmptyDisabledRequest, 'accessor_int'));
        $this->assertFalse(property_exists($this->testEmptyDisabledRequest, 'accessor_string'));
        $this->assertFalse(property_exists($this->testEmptyDisabledRequest, 'int'));
        $this->assertFalse(property_exists($this->testEmptyDisabledRequest, 'string'));
    }
}
