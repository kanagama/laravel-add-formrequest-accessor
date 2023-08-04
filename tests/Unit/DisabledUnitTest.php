<?php

namespace Kanagama\FormRequestAccessor\Tests\Unit;

use Kanagama\FormRequestAccessor\Exceptions\UnsupportedOperandTypesException;
use Kanagama\FormRequestAccessor\Tests\TestTraits\RefrectionClassTrait;
use Kanagama\FormRequestAccessor\Tests\TestCase;

/**
 * @author k-nagama <k.nagama0632@gmail.com>
 */
class DisabledUnitTest extends TestCase
{
    use RefrectionClassTrait;

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
