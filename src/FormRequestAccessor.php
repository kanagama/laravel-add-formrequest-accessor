<?php

namespace Kanagama\FormRequestAccessor;

use Illuminate\Support\Str;

/**
 * FormRequest に accessor 機能を付与
 *
 * @method void $passedValidation()
 *
 * @author k.nagama <k.nagama0632@gmail.com>
 */
trait FormRequestAccessor
{
    /**
     * laravel\framework\src\Illuminate\Http\Concerns\InteractsWithInput.php
     *
     * @param  array|mixed  $keys
     * @return array
     */
    public function all($keys = null): array
    {
        $all = parent::all($keys);

        // property $guarded が存在しない、または配列でない
        if (!property_exists(get_class(), 'guarded') || !is_array($this->guarded)) {
            return $all;
        }

        foreach ($this->guarded as $key) {
            unset($all[$key]);
        }

        return $all;
    }

    /**
     * input のオーバーライド
     * 定義前のアクセサメソッドを呼び出された場合
     *
     * @param  string|null  $key
     * @param  mixed|null  $default
     */
    public function input($key = null, $default = null)
    {
        $inputValue = parent::input($key);
        if (
            is_null($inputValue) && !is_null($key) && is_null($default)
            &&
            // アクセサから同じアクセサが呼び出されるとループして例外が発生するため
            !$this->checkThisFunctionCall($key)
        ) {
            // 対象アクセサメソッドが存在していれば呼び出す
            $method = Str::camel('get_'. $key . '_attribute');
            if (in_array($method, $this->getThisClassAccessorMethods(), true) !== false) {
                return $this->{$method}();
            }
        }

        return $inputValue;
    }

    /**
     * Illuminate\Http\Concerns\InteractsWithInput::passedValidation() を override
     *
     * @return void
     *
     * @author k.nagama <k.nagama0632@gmail.com>
     */
    public function passedValidation(): void
    {
        parent::passedValidation();

        foreach ($this->getThisClassAccessorMethods() as $method) {
            preg_match('/(?<=get_).+(?=_attribute)/', str::snake($method), $match);
            if (empty($match[0])) {
                continue;
            }

            $this->merge([
                $match[0] => $this->{$method}(),
            ]);
        }
    }

    /**
     * 対象リクエストクラスのアクセサメソッドを取得
     *
     * @return array
     */
    private function getThisClassAccessorMethods(): array
    {
        return preg_grep('/^get.*Attribute/', get_class_methods(get_class())) ?? [];
    }

    /**
     * アクセサから同じアクセサが呼び出されているかチェック
     *
     * @param  string  $key
     * @return boolean
     */
    private function checkThisFunctionCall(string $key): bool
    {
        $debug_backtrace = array_column(debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 10), 'function');

        return (in_array(Str::camel('get_'. $key . '_attribute'), $debug_backtrace, true) !== false);
    }
}
