<?php

namespace Kanagama\FormRequestAccessor\Tests\Unit;

use Kanagama\FormRequestAccessor\Exceptions\UnsupportedOperandTypesException;
use Kanagama\FormRequestAccessor\Tests\TestTraits\RefrectionClassTrait;
use Kanagama\FormRequestAccessor\Tests\TestCase;

/**
 * @author k-nagama <k.nagama0632@gmail.com>
 */
class EmptyDisabledUnitTest extends TestCase
{
    use RefrectionClassTrait;

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
}
