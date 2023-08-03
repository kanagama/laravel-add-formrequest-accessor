<?php

namespace Kanagama\FormRequestAccessor\Tests\Unit;

use Kanagama\FormRequestAccessor\Exceptions\UnsupportedOperandTypesException;
use Kanagama\FormRequestAccessor\Tests\TestTraits\RefrectionClassTrait;
use Kanagama\FormRequestAccessor\Tests\TestCase;

/**
 * @author k-nagama <k.nagama0632@gmail.com>
 */
class LaravelFormRequestAccessorUnitTest extends TestCase
{
    use RefrectionClassTrait;

    /**
     * @test
     */
    public function attributeファンクションが存在していればTrue()
    {
        $response = $this->refrectionClass('getThisClassAccessorMethods', []);
        $this->assertNotEmpty($response);
    }

    /**
     * @test
     */
    public function validatedAccessorプロパティが存在していればbool型が取得できる()
    {
        $response = $this->refrectionClass('getValidatedAccessorProperty', []);
        $this->assertIsBool($response);
        $this->assertTrue($response);
    }

    /**
     * @test
     */
    public function validatedAccessorプロパティが存在していなくてもbool型が取得できる()
    {
        $response = $this->refrectionClassNoProperty('getValidatedAccessorProperty', []);
        $this->assertIsBool($response);
        $this->assertFalse($response);
    }

    /**
     * @test
     */
    public function validatedAccessorプロパティの型が異なれば例外()
    {
        $this->expectException(UnsupportedOperandTypesException::class);
        $this->refrectionClassExceptionProperty('getValidatedAccessorProperty', []);
    }

    /**
     * @test
     */
    public function emptyDisabledプロパティが存在していればbool型が取得できる()
    {
        $response = $this->refrectionClass('getEmptyDisabledProperty', []);
        $this->assertIsBool($response);
        $this->assertTrue($response);
    }

    /**
     * @test
     */
    public function emptyDisabledプロパティが存在していなくてもbool型が取得できる()
    {
        $response = $this->refrectionClassNoProperty('getEmptyDisabledProperty', []);
        $this->assertIsBool($response);
        $this->assertFalse($response);
    }

    /**
     * @test
     */
    public function empty_disabledプロパティの型が異なれば例外()
    {
        $this->expectException(UnsupportedOperandTypesException::class);
        $this->refrectionClassExceptionProperty('getEmptyDisabledProperty', []);
    }

    /**
     * @test
     */
    public function null_disabledプロパティが存在していればbool型が取得できる()
    {
        $response = $this->refrectionClass('getNullDisabledProperty', []);
        $this->assertIsBool($response);
        $this->assertTrue($response);
    }

    /**
     * @test
     */
    public function null_disabledプロパティが存在していなくてもbool型が取得できる()
    {
        $response = $this->refrectionClassNoProperty('getNullDisabledProperty', []);
        $this->assertIsBool($response);
        $this->assertFalse($response);
    }

    /**
     * @test
     */
    public function null_disabledプロパティの型が異なれば例外()
    {
        $this->expectException(UnsupportedOperandTypesException::class);
        $this->refrectionClassExceptionProperty('getNullDisabledProperty', []);
    }

    /**
     * @test
     */
    public function immutableプロパティが存在していればbool型が取得できる()
    {
        $response = $this->refrectionClass('getImmutableProperty', []);
        $this->assertIsBool($response);
        $this->assertTrue($response);
    }

    /**
     * @test
     */
    public function immutableプロパティが存在していなくてもbool型が取得できる()
    {
        $response = $this->refrectionClassNoProperty('getImmutableProperty', []);
        $this->assertIsBool($response);
        $this->assertFalse($response);
    }

    /**
     * @test
     */
    public function immutableプロパティの型が異なれば例外()
    {
        $this->expectException(UnsupportedOperandTypesException::class);
        $this->refrectionClassExceptionProperty('getImmutableProperty', []);
    }

    /**
     * @test
     */
    public function castsプロパティが存在していれば配列が取得出来る()
    {
        $response = $this->refrectionClass('getCastsProperty', []);
        $this->assertIsArray($response);
    }

    /**
     * @test
     */
    public function castsプロパティが存在していなくても配列が取得出来る()
    {
        $response = $this->refrectionClassNoProperty('getCastsProperty', []);
        $this->assertIsArray($response);
    }

    /**
     * @test
     */
    public function castsプロパティの型が異なれば例外()
    {
        $this->expectException(UnsupportedOperandTypesException::class);
        $this->refrectionClassExceptionProperty('getCastsProperty', []);
    }

    /**
     * @test
     */
    public function guardedプロパティが存在していれば配列が取得出来る()
    {
        $response = $this->refrectionClass('getGuardedProperty', []);
        $this->assertIsArray($response);
    }

    /**
     * @test
     */
    public function guardedプロパティが存在していなくても列が取得出来る()
    {
        $response = $this->refrectionClassNoProperty('getGuardedProperty', []);
        $this->assertIsArray($response);
    }

    /**
     * @test
     */
    public function guardedプロパティの型が異なれば例外()
    {
        $this->expectException(UnsupportedOperandTypesException::class);
        $this->refrectionClassExceptionProperty('getGuardedProperty', []);
    }

    /**
     * @test
     */
    public function fillもしくはfillableプロパティが存在していれば配列が取得出来る()
    {
        $response = $this->refrectionClass('getFillableProperty', []);
        $this->assertIsArray($response);
    }

    /**
     * @test
     */
    public function fillもしくはfillableプロパティが存在していなくても配列が取得出来る()
    {
        $response = $this->refrectionClassNoProperty('getFillableProperty', []);
        $this->assertIsArray($response);
    }

    /**
     * @test
     */
    public function fillもしくはfillableプロパティの型が異なれば例外()
    {
        $this->expectException(UnsupportedOperandTypesException::class);
        $this->refrectionClassExceptionProperty('getFillableProperty', []);
    }

    /**
     * @test
     */
    public function enabledプロパティが存在していれば配列が取得出来る()
    {
        $response = $this->refrectionClass('getEnabledProperty', []);
        $this->assertIsArray($response);
    }

    /**
     * @test
     */
    public function enabledプロパティが存在していなくても配列が取得出来る()
    {
        $response = $this->refrectionClassNoProperty('getEnabledProperty', []);
        $this->assertIsArray($response);
    }

    /**
     * @test
     */
    public function enabledプロパティの型が異なれば例外()
    {
        $this->expectException(UnsupportedOperandTypesException::class);
        $this->refrectionClassExceptionProperty('getEnabledProperty', []);
    }

    /**
     * @test
     */
    public function disabledプロパティが存在していれば配列が取得出来る()
    {
        $response = $this->refrectionClass('getDisabledProperty', []);
        $this->assertIsArray($response);
    }

    /**
     * @test
     */
    public function disabledプロパティが存在していなくても配列が取得出来る()
    {
        $response = $this->refrectionClassNoProperty('getDisabledProperty', []);
        $this->assertIsArray($response);
    }

    /**
     * @test
     */
    public function disabledプロパティの型が異なれば例外()
    {
        $this->expectException(UnsupportedOperandTypesException::class);
        $this->refrectionClassExceptionProperty('getDisabledProperty', []);
    }
}
