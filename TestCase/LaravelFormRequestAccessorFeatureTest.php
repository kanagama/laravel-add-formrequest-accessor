<?php

namespace Tests\Feature;

use Carbon\Carbon;
use Kanagama\FormRequestAccessor\Exceptions\ImmutableException;
use Kanagama\FormRequestAccessor\TestRequest\TestCastRequest;
use Kanagama\FormRequestAccessor\TestRequest\TestDisabledRequest;
use Kanagama\FormRequestAccessor\TestRequest\TestEnabledRequest;
use Kanagama\FormRequestAccessor\TestRequest\TestImmutableRequest;
use Kanagama\FormRequestAccessor\TestRequest\TestNullDisabledRequest;
use Kanagama\FormRequestAccessor\TestRequest\TestEmptyDisabledRequest;
use Tests\TestCase;

/**
 * @author k-nagama <k.nagama0632@gmail.com>
 */
class LaravelFormRequestAccessorFeatureTest extends TestCase
{
    private TestCastRequest $testCastRequest;
    private TestDisabledRequest $testDisabledRequest;
    private TestEnabledRequest $testEnabledRequest;
    private TestImmutableRequest $testImmutableRequest;
    private TestNullDisabledRequest $testNullDisabledRequest;
    private TestEmptyDisabledRequest $testEmptyDisabledRequest;

    /**
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        // $casts
        $this->testCastRequest = new TestCastRequest([
            'int'    => '1',
            'string' => 1,
            'bool'   => 1,
            'carbon' => date('Y-m-d'),
        ]);
        $this->testCastRequest->passedValidation();

        // null_disabled
        $this->testNullDisabledRequest = new TestNullDisabledRequest([
            'null'         => null,
            'int'          => 1,
            'int_zero'     => 0,
            'string_empty' => '',
        ]);
        $this->testNullDisabledRequest->passedValidation();

        // empty_disabled
        $this->testEmptyDisabledRequest = new TestEmptyDisabledRequest([
            'null'         => null,
            'int'          => 1,
            'int_zero'     => 0,
            'string_empty' => '',
            'string'       => 'a',
        ]);
        $this->testEmptyDisabledRequest->passedValidation();

        // immutable
        $this->testImmutableRequest = new TestImmutableRequest();
        $this->testImmutableRequest->passedValidation();

        // disabled
        $this->testDisabledRequest = new TestDisabledRequest([
            'test_enabled'  => 1,
            'test_disabled' => 1,
        ]);
        $this->testDisabledRequest->passedValidation();

        // enabled
        $this->testEnabledRequest = new TestEnabledRequest([
            'test_enabled'  => 1,
            'test_disabled' => 1,
        ]);
        $this->testEnabledRequest->passedValidation();
    }

    /**
     * @test
     */
    public function 文字列型プロパティが数値型にキャストされている()
    {
        $this->assertIsInt($this->testCastRequest->int);
    }

    /**
     * @test
     */
    public function 文字列型アクセサが数値型にキャストされている()
    {
        $this->assertIsInt($this->testCastRequest->cast_int);
    }

    /**
     * @test
     */
    public function 数値型プロパティが文字列型にキャストされている()
    {
        $this->assertIsString($this->testCastRequest->string);
    }

    /**
     * @test
     */
    public function 数値型アクセサが文字列型にキャストされている()
    {
        $this->assertIsString($this->testCastRequest->cast_string);
    }

    /**
     * @test
     */
    public function 数値型プロパティがbool型にキャストされている()
    {
        $this->assertIsBool($this->testCastRequest->bool);
    }

    /**
     * @test
     */
    public function 数値型アクセサがbool型にキャストされている()
    {
        $this->assertIsBool($this->testCastRequest->cast_bool);
    }

    /**
     * @test
     */
    public function 文字列型プロパティがCarbon型にキャストされている()
    {
        $this->assertInstanceOf(Carbon::class, $this->testCastRequest->carbon);
    }

    /**
     * @test
     */
    public function 文字列型アクセサがCarbon型にキャストされている()
    {
        $this->assertInstanceOf(Carbon::class, $this->testCastRequest->cast_carbon);
    }

    /**
     * @test
     */
    public function inputの第2引数が正常に設定される()
    {
        $this->assertEquals($this->testCastRequest->input('not_accessor_test', true), true);
        $this->assertEquals($this->testCastRequest->input('not_accessor_test', []), []);
        $this->assertEquals($this->testCastRequest->input('not_accessor_test', ''), '');
    }

    /**
     * @test
     */
    public function null_disabledがtrueの場合、nullのプロパティにアクセスできない()
    {
        $all = $this->testNullDisabledRequest->all();

        $this->assertTrue(!isset($all['accessor_null']));
        $this->assertTrue(!isset($all['null']));
        $this->assertFalse(property_exists($this->testNullDisabledRequest, 'accessor_null'));
        $this->assertFalse(property_exists($this->testNullDisabledRequest, 'null'));
    }

    /**
     * @test
     */
    public function null_disabledがtrueの場合、nullでなければアクセスできる()
    {
        $this->assertIsInt($this->testNullDisabledRequest->int);
        $this->assertIsInt($this->testNullDisabledRequest->int_zero);
        $this->assertIsInt($this->testNullDisabledRequest->accessor_int);
        $this->assertIsString($this->testNullDisabledRequest->string_empty);
        $this->assertIsString($this->testNullDisabledRequest->accessor_string_empty);
    }

    /**
     * @test
     */
    public function empty_disabledがtrueの場合、emptyのプロパティにアクセスできない()
    {
        $all = $this->testEmptyDisabledRequest->all();

        $this->assertTrue(!isset($all['accessor_null']));
        $this->assertTrue(!isset($all['null']));
        $this->assertTrue(!isset($all['accessor_string_empty']));
        $this->assertTrue(!isset($all['string_empty']));
        $this->assertTrue(!isset($all['accessor_int_zero']));
        $this->assertTrue(!isset($all['int_zero']));

        $this->assertFalse(property_exists($this->testEmptyDisabledRequest, 'accessor_null'));
        $this->assertFalse(property_exists($this->testEmptyDisabledRequest, 'null'));
        $this->assertFalse(property_exists($this->testEmptyDisabledRequest, 'accessor_string_empty'));
        $this->assertFalse(property_exists($this->testEmptyDisabledRequest, 'string_empty'));
        $this->assertFalse(property_exists($this->testEmptyDisabledRequest, 'accessor_int_zero'));
        $this->assertFalse(property_exists($this->testEmptyDisabledRequest, 'int_zero'));
    }

    /**
     * @test
     */
    public function immutableなRequestクラスでmergeしようとすると例外()
    {
        $this->expectException(ImmutableException::class);
        $this->testImmutableRequest->merge([
            'test' => 1,
        ]);
    }

    /**
     * @test
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
     */
    public function disabledプロパティで指定されているプロパティにはアクセスできない()
    {
        $all = $this->testDisabledRequest->all();

        $this->assertTrue(!isset($all['accessor_disabled']));
        $this->assertTrue(!isset($all['test_disabled']));

        $this->assertFalse(property_exists($this->testDisabledRequest, 'accessor_disabled'));
        $this->assertFalse(property_exists($this->testDisabledRequest, 'test_disabled'));
    }

    /**
     * @test
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
     */
    public function enabledプロパティで指定されていないプロパティにはアクセスできない()
    {
        $all = $this->testEnabledRequest->all();

        $this->assertTrue(!isset($all['accessor_disabled']));
        $this->assertTrue(!isset($all['test_disabled']));

        $this->assertFalse(property_exists($this->testEnabledRequest, 'accessor_disabled'));
        $this->assertFalse(property_exists($this->testEnabledRequest, 'test_disabled'));
    }
}
