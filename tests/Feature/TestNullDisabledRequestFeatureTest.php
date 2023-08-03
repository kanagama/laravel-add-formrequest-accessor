<?php

namespace Kanagama\FormRequestAccessor\Tests\Feature;

use Kanagama\FormRequestAccessor\Tests\TestCase;
use Kanagama\FormRequestAccessor\Tests\TestRequest\TestNullDisabledRequest;

/**
 * @author k-nagama <k.nagama0632@gmail.com>
 */
class TestNullDisabledRequestFeatureTest extends TestCase
{
    /**
     * @var TestNullDisabledRequest
     */
    private TestNullDisabledRequest $testNullDisabledRequest;

    /**
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->app->resolving(TestNullDisabledRequest::class, function ($resolved) {
            $resolved->merge([
                'null'         => null,
                'int'          => 1,
                'int_zero'     => 0,
                'string_empty' => '',
            ]);
        });
        /** @var TestNullDisabledRequest */
        $this->testNullDisabledRequest = app(TestNullDisabledRequest::class);
    }

    /**
     * @test
     * @group null_disabled
     */
    public function null_disabledがtrueの場合、nullのプロパティにアクセスできない()
    {
        $all = $this->testNullDisabledRequest->all();

        $this->assertFalse(isset($all['accessor_null']));
        $this->assertFalse(isset($all['null']));
    }

    /**
     * @test
     * @group nullDisabled
     */
    public function nullDisabledがtruの場合、nullのプロパティにアクセスできない()
    {
        $this->assertFalse(property_exists($this->testNullDisabledRequest, 'accessor_null'));
        $this->assertFalse(property_exists($this->testNullDisabledRequest, 'null'));
    }

    /**
     * @test
     * @group nullDisabled
     */
    public function null_disabledがtrueの場合、nullでなければアクセスできる()
    {
        $this->assertIsInt($this->testNullDisabledRequest->int);
        $this->assertIsInt($this->testNullDisabledRequest->int_zero);
        $this->assertIsInt($this->testNullDisabledRequest->accessor_int);
        $this->assertIsString($this->testNullDisabledRequest->string_empty);
        $this->assertIsString($this->testNullDisabledRequest->accessor_string_empty);
    }
}
