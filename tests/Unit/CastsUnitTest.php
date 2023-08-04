<?php

namespace Kanagama\FormRequestAccessor\Tests\Unit;

use Kanagama\FormRequestAccessor\Exceptions\UnsupportedOperandTypesException;
use Kanagama\FormRequestAccessor\Tests\TestTraits\RefrectionClassTrait;
use Kanagama\FormRequestAccessor\Tests\TestCase;

/**
 * @author k-nagama <k.nagama0632@gmail.com>
 */
class CastsUnitTest extends TestCase
{
    use RefrectionClassTrait;

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
}
