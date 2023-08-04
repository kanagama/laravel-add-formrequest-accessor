<?php

namespace Kanagama\FormRequestAccessor\Tests\Unit;

use Kanagama\FormRequestAccessor\Exceptions\UnsupportedOperandTypesException;
use Kanagama\FormRequestAccessor\Tests\TestTraits\RefrectionClassTrait;
use Kanagama\FormRequestAccessor\Tests\TestCase;

/**
 * @author k-nagama <k.nagama0632@gmail.com>
 */
class GuardedUnitTest extends TestCase
{
    use RefrectionClassTrait;

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
}
