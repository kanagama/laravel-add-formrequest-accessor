<?php

namespace Tests\Feature;

use Kanagama\FormRequestAccessor\TestTraits\RefrectionClassTrait;
use Tests\TestCase;

/**
 * @author k-nagama <k.nagama0632@gmail.com>
 */
class LaravelFormRequestAccessorUnitTest extends TestCase
{
    use RefrectionClassTrait;

    /**
     * @test
     *
     * @dataProvider camelCaseProvider
     */
    public function スネークケースファンクション名をキャメルケースに変換(...$params)
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
     * @test
     */
    public function アクセサが同じアクセサから呼び出されている()
    {
        $response = $this->refrectionClass('checkThisFunctionCall', [
            'test',
        ]);
        $this->assertFalse($response);
    }

    /**
     * @test
     */
    public function Attributeファンクションが存在していればTrue()
    {
        $response = $this->refrectionClass('getThisClassAccessorMethods', []);
        $this->assertNotEmpty($response);
    }

    /**
     * @test
     */
    public function empty_disabledプロパティが設定されていればTrue()
    {
        $response = $this->refrectionClass('checkExistEmptyDisabledProperty', []);
        $this->assertTrue($response);
    }

    /**
     * @test
     */
    public function empty_disabledプロパティが存在しなければFalse()
    {
        $response = $this->refrectionClassNoProperty('checkExistEmptyDisabledProperty', []);
        $this->assertFalse($response);
    }

    /**
     * @test
     */
    public function null_disabledプロパティが設定されていればTrue()
    {
        $response = $this->refrectionClass('checkExistNullDisabledProperty', []);
        $this->assertTrue($response);
    }

    /**
     * @test
     */
    public function null_disabledプロパティが存在しなければFalse()
    {
        $response = $this->refrectionClassNoProperty('checkExistNullDisabledProperty', []);
        $this->assertFalse($response);
    }

    /**
     * @test
     */
    public function immutableプロパティが設定されていればTrue()
    {
        $response = $this->refrectionClass('checkExistImmutableProperty', []);
        $this->assertTrue($response);
    }

    /**
     * @test
     */
    public function immutableプロパティが存在しなければFalse()
    {
        $response = $this->refrectionClassNoProperty('checkExistImmutableProperty', []);
        $this->assertFalse($response);
    }

    /**
     * @test
     */
    public function castプロパティが設定されていればTrue()
    {
        $response = $this->refrectionClass('checkExistCastsProperty', []);
        $this->assertTrue($response);
    }

    /**
     * @test
     */
    public function castプロパティが存在しなければFalse()
    {
        $response = $this->refrectionClassNoProperty('checkExistCastsProperty', []);
        $this->assertFalse($response);
    }

    /**
     * @test
     */
    public function guardプロパティが設定されていればTrue()
    {
        $response = $this->refrectionClass('checkExistCastsProperty', []);
        $this->assertTrue($response);
    }

    /**
     * @test
     */
    public function guardプロパティが存在しなければFalse()
    {
        $response = $this->refrectionClassNoProperty('checkExistCastsProperty', []);
        $this->assertFalse($response);
    }

    /**
     * @test
     */
    public function fillプロパティが設定されていればTrue()
    {
        $response = $this->refrectionClass('checkExistFillProperty', []);
        $this->assertTrue($response);
    }

    /**
     * @test
     */
    public function fillプロパティが存在しなければFalse()
    {
        $response = $this->refrectionClassNoProperty('checkExistFillProperty', []);
        $this->assertFalse($response);
    }

    /**
     * @test
     */
    public function enabledプロパティが設定されていればTrue()
    {
        $response = $this->refrectionClass('checkExistEnabledProperty', []);
        $this->assertTrue($response);
    }

    /**
     * @test
     */
    public function enabledプロパティが存在しない場合False()
    {
        $response = $this->refrectionClassNoProperty('checkExistEnabledProperty', []);
        $this->assertFalse($response);
    }

    /**
     * @test
     */
    public function disabledプロパティが設定されていればTrue()
    {
        $response = $this->refrectionClass('checkExistDisabledProperty', []);
        $this->assertTrue($response);
    }

    /**
     * @test
     */
    public function disabledプロパティが存在しない場合False()
    {
        $response = $this->refrectionClassNoProperty('checkExistDisabledProperty', []);
        $this->assertFalse($response);
    }
}
