<?php

namespace Tests\Feature;

use Kanagama\FormRequestAccessor\TestTraits\RefrectionClassTrait;
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
     *
     * @dataProvider camelCaseProvider
     */
    public function camelCase(...$params)
    {
        $request = $params[0];
        $camel_case = $params[1];

        $response = $this->refrectionClass('camelMethod', [
            $request,
        ]);
        $this->assertTrue($response === $camel_case);
    }

    /**
     * @return array
     */
    public function camelCaseProvider(): array
    {
        return [
            [
                'request'  => 'test',
                'response' => 'getTestAttribute',
            ],
            [
                'request'  => 'test_case',
                'response' => 'getTestCaseAttribute',
            ],
            [
                'request'  => 'test_case_last',
                'response' => 'getTestCaseLastAttribute',
            ],
        ];
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

    /**
     * attribute メソッドが取得できるかどうか
     *
     * @test
     */
    public function checkGetThisClassAccessorMethods()
    {
        $response = $this->refrectionClass('getThisClassAccessorMethods', []);
        $this->assertNotEmpty($response);
    }

    /**
     * $empty_disabled が存在した場合、true になるか
     *
     * @test
     */
    public function getCheckExistEmptyDisabledProperty()
    {
        $response = $this->refrectionClass('checkExistEmptyDisabledProperty', []);
        $this->assertTrue($response);
    }

    /**
     * $null_disabled が存在した場合、true になるか
     *
     * @test
     */
    public function getCheckExistNullDisabledProperty()
    {
        $response = $this->refrectionClass('checkExistNullDisabledProperty', []);
        $this->assertTrue($response);
    }

    /**
     * $casts が存在した場合、true になるか
     *
     * @test
     */
    public function getCheckCastsProperty()
    {
        $response = $this->refrectionClass('checkExistCastsProperty', []);
        $this->assertTrue($response);
    }

    /**
     * $guarded が存在した場合、true になるか
     *
     * @test
     */
    public function getCheckGuardedProperty()
    {
        $response = $this->refrectionClass('checkExistCastsProperty', []);
        $this->assertTrue($response);
    }

    /**
     * $fill が存在した場合、true になるか
     *
     * @test
     */
    public function getCheckFillProperty()
    {
        $response = $this->refrectionClass('checkExistFillProperty', []);
        $this->assertTrue($response);
    }

    /**
     * $enabled が存在した場合、true になるか
     *
     * @test
     */
    public function getCheckEnabledProperty()
    {
        $response = $this->refrectionClass('checkExistEnabledProperty', []);
        $this->assertTrue($response);
    }

    /**
     * $disabled が存在した場合、true になるか
     *
     * @test
     */
    public function getCheckDisabledProperty()
    {
        $response = $this->refrectionClass('checkExistDisabledProperty', []);
        $this->assertTrue($response);
    }
}
