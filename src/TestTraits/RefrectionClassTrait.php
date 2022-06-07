<?php

namespace Kanagama\FormRequestAccessor\TestTraits;

use Kanagama\FormRequestAccessor\FormRequestAccessor;
use Kanagama\FormRequestAccessor\TestTraits\TestAttributeFunctionTrait;
use ReflectionClass;

trait RefrectionClassTrait
{
    /**
     * private, protected の function もテスト出来るようにする
     *
     * @param  string  $methodName
     * @param  array  $params
     * @return mixed
     *
     * @see https://qiita.com/ponsuke0531/items/6dc6fc34fff1e9b37901
     */
    public function refrectionClass(string $methodName, array $params)
    {
        // テスト用の無名クラスを定義
        $formRequestAccessor = new class {
            use FormRequestAccessor, TestAttributeFunctionTrait;

            protected $null_disabled = true;
            protected $empty_disabled = true;

            public function passedValidation() {}
            public function all($key = null) {
                return [];
            }
            public function input($key = null, $default = null) {
                return null;
            }
        };

        // ReflectionClassをテスト対象のクラスを元に作る.
        $reflection = new ReflectionClass($formRequestAccessor);
        // 対象メソッド取得
        $method = $reflection->getMethod($methodName);
        // アクセス許可
        $method->setAccessible(true);

        return $method->invokeArgs($formRequestAccessor, $params);
    }

    /**
     * テスト用メソッド
     *
     * @return bool
     */
    public function getTestAttribute(): bool
    {
        return $this->refrectionClass('checkThisFunctionCall', [
            'test',
        ]);
    }

    /**
     * @return int
     */
    public function getIntAttribute(): int
    {
        return 1;
    }

    /**
     * @return string
     */
    public function getCastIntAttribute(): string
    {
        return (string) $this->getIntAttribute();
    }

    /**
     * @return int
     */
    public function getCastStringAttribute(): int
    {
        return $this->getIntAttribute();;
    }

    /**
     * @return int
     */
    public function getCastBoolFalseAttribute(): int
    {
        return 0;
    }

    /**
     * @return int
     */
    public function getCastBoolTrueAttribute(): int
    {
        return $this->getIntAttribute();;
    }

    /**
     * @return string
     */
    public function getCastDatetimeAttribute(): string
    {
        return date('Y-m-d');
    }
}
