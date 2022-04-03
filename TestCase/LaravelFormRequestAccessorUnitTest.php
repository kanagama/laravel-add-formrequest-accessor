<?php

namespace Tests\Feature;

use Kanagama\FormRequestAccessor\RefrectionClassTrait;
use Tests\TestCase;

/**
 * @method castAttributeString()
 */
class LaravelFormRequestAccessorUnitTest extends TestCase
{
    use RefrectionClassTrait;

    /**
     * string 型に変換されているか
     *
     * @test
     */
    public function castAttributeString()
    {
        $response = $this->refrectionClass('castAttribute', [
            'string',
            1,
        ]);
        $this->assertIsString($response);
    }

    /**
     * int 型に変換されているか
     *
     * @test
     *
     * @dataProvider intProvider
     */
    public function castAttributeInt(...$params)
    {
        $response = $this->refrectionClass('castAttribute', $params);
        $this->assertIsInt($response);
    }

    /**
     * @return array
     */
    public function intProvider(): array
    {
        return [
            [
                'integer',
                "1",
            ],
            [
                'int',
                "2",
            ],
        ];
    }

    /**
     * bool 型に変換されているか
     *
     * @test
     *
     * @dataProvider boolProvider
     */
    public function castAttributeBool(...$params)
    {
        $response = $this->refrectionClass('castAttribute', $params);
        $this->assertIsBool($response);
    }

    /**
     * @return array
     */
    public function boolProvider(): array
    {
        return [
            [
                'boolean',
                "1",
            ],
            [
                'bool',
                "0",
            ],
        ];
    }

    /**
     * attribute メソッドが取得できるかどうか
     *
     * @test
     */
    public function getThisClassAccessorMethods()
    {
        $response = $this->refrectionClass('getThisClassAccessorMethods', []);
        $this->assertNotEmpty($response);
    }

    /**
     * アクセサが同じアクセサから呼び出されているか
     *
     * @test
     */
    public function checkThisFunctionCallTrue()
    {
        $this->assertTrue($this->getTestAttribute());
    }

    /**
     * アクセサが同じアクセサから呼び出されているか
     *
     * @test
     */
    public function checkThisFunctionCallFalse()
    {
        $response = $this->refrectionClass('checkThisFunctionCall', [
            'test',
        ]);
        $this->assertFalse($response);
    }
}
