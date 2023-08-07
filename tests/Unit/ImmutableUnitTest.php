<?php

namespace Kanagama\FormRequestAccessor\Tests\Unit;

use Kanagama\FormRequestAccessor\Exceptions\UnsupportedOperandTypesException;
use Kanagama\FormRequestAccessor\Tests\TestTraits\RefrectionClassTrait;
use Kanagama\FormRequestAccessor\Tests\TestCase;

/**
 * @author k-nagama <k.nagama0632@gmail.com>
 */
class ImmutableUnitTest extends TestCase
{
    use RefrectionClassTrait;

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
}
