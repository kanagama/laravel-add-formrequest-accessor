<?php

namespace Kanagama\FormRequestAccessor\Tests\Feature;

use Carbon\Carbon;
use Illuminate\Support\Facades\Route;
use Kanagama\FormRequestAccessor\Exceptions\ImmutableException;
use Kanagama\FormRequestAccessor\Tests\TestRequest\TestCastsRequest;
use Kanagama\FormRequestAccessor\Tests\TestRequest\TestDisabledRequest;
use Kanagama\FormRequestAccessor\Tests\TestRequest\TestEmptyDisabledRequest;
use Kanagama\FormRequestAccessor\Tests\TestRequest\TestEnabledRequest;
use Kanagama\FormRequestAccessor\Tests\TestRequest\TestFillableRequest;
use Kanagama\FormRequestAccessor\Tests\TestRequest\TestGuardedRequest;
use Kanagama\FormRequestAccessor\Tests\TestRequest\TestImmutableRequest;
use Kanagama\FormRequestAccessor\Tests\TestRequest\TestNullDisabledRequest;
use Kanagama\FormRequestAccessor\Tests\TestRequest\TestRequest;
use Kanagama\FormRequestAccessor\Tests\TestCase;

/**
 * @author k-nagama <k.nagama0632@gmail.com>
 */
class LaravelFormRequestAccessorFeatureTest extends TestCase
{
    /**
     * @var TestRequest
     */
    private TestRequest $testRequest;
    /**
     * @var TestCastsRequest
     */
    private TestCastsRequest $testCastRequest;
    /**
     * @var TestDisabledRequest
     */
    private TestDisabledRequest $testDisabledRequest;
    /**
     * @var TestEmptyDisabledRequest
     */
    private TestEmptyDisabledRequest $testEmptyDisabledRequest;
    /**
     * @var TestEnabledRequest
     */
    private TestEnabledRequest $testEnabledRequest;
    /**
     * @var TestFillableRequest
     */
    private TestFillableRequest $testFillableRequest;
    /**
     * @var TestGuardedRequest
     */
    private TestGuardedRequest $testGuardedRequest;
    /**
     * @var TestImmutableRequest
     */
    private TestImmutableRequest $testImmutableRequest;
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

        $this->testRequest = new TestRequest([
            'test_offset_unset' => 1,
            'test_offset_set'   => 2,
        ]);
        $this->testRequest->passedValidation();

        $this->testFillableRequest = new TestFillableRequest([
            'test_fillable' => 1,
            'test_guarded'  => 1,
        ]);
        $this->testFillableRequest->passedValidation();

        // $casts
        $this->testCastRequest = new TestCastsRequest([
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
        $this->testImmutableRequest = new TestImmutableRequest([
            'test_immutable' => 1,
        ]);
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

        // guarded
        $this->testGuardedRequest = new TestGuardedRequest([
            'test_guarded'  => 1,
            'test'          => 1,
        ]);
        $this->testGuardedRequest->passedValidation();

        Route::shouldReceive('currentRouteAction')
            ->andReturn('App\Controller\TestController@index');
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
     * @group casts
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
        $this->assertInstanceOf(Carbon::class, $this->testCastRequest->carbon);
    }

    /**
     * @test
     * @group casts
     */
    public function 文字列型アクセサがCarbon型にキャストされている()
    {
        $this->assertInstanceOf(Carbon::class, $this->testCastRequest->cast_carbon);
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

    /**
     * @test
     * @group null_disabled
     */
    public function null_disabledがtrueの場合、nullのプロパティにアクセスできない()
    {
        $all = $this->testNullDisabledRequest->all();

        $this->assertFalse(isset($all['accessor_null']));
        $this->assertFalse(isset($all['null']));
        $this->assertFalse(property_exists($this->testNullDisabledRequest, 'accessor_null'));
        $this->assertFalse(property_exists($this->testNullDisabledRequest, 'null'));
    }

    /**
     * @test
     * @group null_disabled
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

    /**
     * @test
     * @group immutable
     */
    public function immutableではないRequestクラスでmergeできる()
    {
        $this->assertNull($this->testRequest->merge);

        $this->testRequest->merge([
            'merge' => 1,
        ]);
        $this->assertEquals($this->testRequest->merge, 1);
        $this->assertEquals($this->testRequest->test_offset_unset, 1);
        $this->assertEquals($this->testRequest->test_offset_set, 2);
    }

    /**
     * @test
     * @group immutable
     */
    public function immutableではないRequestクラスでoffsetUnsetできる()
    {
        $this->assertEquals($this->testRequest->test_offset_unset, 1);

        $this->testRequest->offsetUnset('test_offset_unset');
        $this->assertNull($this->testRequest->test_offset_unset);
        $this->assertEquals($this->testRequest->test_offset_set, 2);
    }

    /**
     * @test
     * @group immutable
     */
    public function immutableではないRequestクラスでoffsetSetできる()
    {
        $this->assertEquals($this->testRequest->test_offset_set, 2);

        $this->testRequest->offsetSet('test_offset_set', 3);
        $this->assertEquals($this->testRequest->test_offset_set, 3);
    }

    /**
     * @test
     * @group immutable
     */
    public function immutableではないRequestクラスでreplaceできる()
    {
        $this->assertNull($this->testRequest->merge);

        $this->testRequest->replace([
            'merge' => 1,
        ]);
        $this->assertEquals($this->testRequest->merge, 1);

        // 上書きされて消えている
        $this->assertNull($this->testRequest->test_offset_unset);
        $this->assertNull($this->testRequest->test_offset_set);
    }

    /**
     * @test
     * @group immutable
     */
    public function immutableなRequestクラスでmergeしようとすると例外()
    {
        $this->expectException(ImmutableException::class);
        $this->testImmutableRequest->merge([
            'merge' => 1,
        ]);
    }

    /**
     * @test
     * @group immutable
     */
    public function immutableなRequestクラスでreplaceしようとすると例外()
    {
        $this->expectException(ImmutableException::class);
        $this->testImmutableRequest->replace([
            'replace' => 1,
        ]);
    }

    /**
     * @test
     * @group immutable
     */
    public function immutableなRequestクラスでoffsetUnsetしようとすると例外()
    {
        $this->expectException(ImmutableException::class);
        $this->testImmutableRequest->offsetUnset('test_immutable');
    }

    /**
     * @test
     * @gruop immutable
     */
    public function immutableなRequestクラスでoffsetSetしようとすると例外()
    {
        $this->expectException(ImmutableException::class);
        $this->testImmutableRequest->offsetSet('test_immutable', 2);
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
    public function fillableプロパティに指定されているプロパティにはアクセスできる()
    {
        $all = $this->testFillableRequest->all();

        $this->assertTrue(isset($all['accessor_fillable']));
        $this->assertTrue(isset($all['test_fillable']));

        $this->assertEquals($this->testFillableRequest->accessor_fillable, 1);
        $this->assertEquals($this->testFillableRequest->test_fillable, 1);
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
     * @group disabled
     */
    public function disabledプロパティで指定されているプロパティにはアクセスできない()
    {
        $all = $this->testDisabledRequest->all();

        $this->assertFalse(isset($all['accessor_disabled']));
        $this->assertFalse(isset($all['test_disabled']));

        $this->assertFalse(property_exists($this->testDisabledRequest, 'accessor_disabled'));
        $this->assertFalse(property_exists($this->testDisabledRequest, 'test_disabled'));
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

    /**
     * @test
     * @group guarded
     */
    public function guardedプロパティに指定されていないプロパティにはアクセスできる()
    {
        $all = $this->testGuardedRequest->all();

        $this->assertTrue(isset($all['test']));
        $this->assertTrue(isset($all['accessor_int']));
        $this->assertTrue(isset($all['accessor_string']));

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


    /**
     * @test
     */
    public function getControllerで正常にコントローラー名が取得できる()
    {
        $this->assertNotEmpty($this->testGuardedRequest->getController());
    }

    /**
     * @test
     */
    public function getActionで正常にアクション名が取得できる()
    {
        $this->assertNotEmpty($this->testGuardedRequest->getAction());
    }
}
