<?php

namespace Kanagama\FormRequestAccessor\TestTraits;

use Kanagama\FormRequestAccessor\FormRequestAccessor;
use ReflectionClass;

trait RefrectionClassTrait
{
    /**
     * private, protected の function もテスト出来るようにする
     * プロパティあり
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
            protected $immutable = true;
            protected $casts = [
                'test_casts',
            ];
            protected $guarded = [
                'test_guarded',
            ];
            protected $fill = [
                'test_fill',
            ];
            protected $enabled = [
                'test_enabled',
            ];
            protected $disabled = [
                'test_disabled',
            ];

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
     * private, protected の function もテスト出来るようにする
     * プロパティ無し
     *
     * @param  string  $methodName
     * @param  array  $params
     * @return mixed
     *
     * @see https://qiita.com/ponsuke0531/items/6dc6fc34fff1e9b37901
     */
    public function refrectionClassNoProperty(string $methodName, array $params)
    {
        // テスト用の無名クラスを定義
        $formRequestAccessor = new class {
            use FormRequestAccessor, TestAttributeFunctionTrait;

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
     * cast が正常に行われるかチェック
     *
     * @param  string  $methodName
     * @param  array  $params
     * @return mixed
     *
     * @see https://qiita.com/ponsuke0531/items/6dc6fc34fff1e9b37901
     */
    public function refrectionClassCastProperty(string $methodName, array $params)
    {
        // テスト用の無名クラスを定義
        $formRequestAccessor = new class {
            use FormRequestAccessor, TestAttributeFunctionTrait;

            protected $casts = [
                'cast_int'        => 'integer',
                'cast_string'     => 'string',
                'cast_bool_false' => 'bool',
                'cast_bool_true'  => 'bool',
                'cast_datetime'   => 'datetime',
            ];

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
        return $this->getIntAttribute();
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
        return $this->getIntAttribute();
    }

    /**
     * @return string
     */
    public function getCastDatetimeAttribute(): string
    {
        return date('Y-m-d');
    }
}
