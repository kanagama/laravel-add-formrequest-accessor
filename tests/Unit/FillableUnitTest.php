<?php

namespace Kanagama\FormRequestAccessor\Tests\Unit;

use Kanagama\FormRequestAccessor\Exceptions\UnsupportedOperandTypesException;
use Kanagama\FormRequestAccessor\Tests\TestTraits\RefrectionClassTrait;
use Kanagama\FormRequestAccessor\Tests\TestCase;

/**
 * @author k-nagama <k.nagama0632@gmail.com>
 */
class FillableUnitTest extends TestCase
{
    use RefrectionClassTrait;

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
}
