<?php

namespace Kanagama\FormRequestAccessor\Tests\Unit;

use Kanagama\FormRequestAccessor\Exceptions\UnsupportedOperandTypesException;
use Kanagama\FormRequestAccessor\Tests\TestTraits\RefrectionClassTrait;
use Kanagama\FormRequestAccessor\Tests\TestCase;

/**
 * @author k-nagama <k.nagama0632@gmail.com>
 */
class AttributeUnitTest extends TestCase
{
    use RefrectionClassTrait;

    /**
     * @test
     */
    public function アクセサメソッドが存在していればTrue()
    {
        $response = $this->refrectionClass('getThisClassAccessorMethods', []);
        $this->assertNotEmpty($response);
    }
}
