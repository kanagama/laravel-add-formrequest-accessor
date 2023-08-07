<?php

namespace Kanagama\FormRequestAccessor\Tests\Unit;

use Kanagama\FormRequestAccessor\Exceptions\UnsupportedOperandTypesException;
use Kanagama\FormRequestAccessor\Tests\TestTraits\RefrectionClassTrait;
use Kanagama\FormRequestAccessor\Tests\TestCase;

/**
 * @author k-nagama <k.nagama0632@gmail.com>
 */
class ValidatedAccessorUnitTest extends TestCase
{
    use RefrectionClassTrait;

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
}
