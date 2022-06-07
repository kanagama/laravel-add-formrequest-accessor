<?php

namespace Kanagama\FormRequestAccessor;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Kanagama\FormRequestAccessor\Models\CastModel;

/**
 * FormRequest に accessor 機能を付与
 *
 * @method void passedValidation()
 * @method array all(mixed $keys)
 * @method mixed input(mixed $key = null, mixed $default = null)
 * @method mixed __get(mixed $key)
 * @method void validateResolved()
 *
 * @author k.nagama <k.nagama0632@gmail.com>
 */
trait FormRequestAccessor
{
    /**
     * 変更前の Request クラス
     */
    private static $beforeRequest;

    /**
     * アクセサ追加前の all() を取得
     *
     * @return array
     */
    public function beforeAll(): array
    {
        return $this->before()->all();
    }

    /**
     * 設定値を取得
     *
     * @return void
     */
    public function settings()
    {
        dd([
            'settings' => [
                'fill'           => $this->checkExistFillProperty() ? $this->fill : null,
                'guarded'        => $this->checkExistGuardedProperty() ? $this->guarded : null,
                'casts'          => $this->checkExistCastsProperty() ? $this->casts : null,
                'null_disabled'  => $this->checkExistNullDisabledProperty() ? $this->null_disabled : false,
                'empty_disabled' => $this->checkExistEmptyDisabledProperty() ? $this->empty_disabled : false,
            ],
            'all' => [
                'before_all' => $this->beforeAll(),
                'after_all'  => $this->all(),
            ],
            'accessor_methods' => $this->getThisClassAccessorMethods(),
        ]);
    }

    /**
     * laravel\framework\src\Illuminate\Http\Concerns\InteractsWithInput.php
     *
     * @param  array|mixed  $keys
     * @return array
     */
    public function all($keys = null): array
    {
        $all = parent::all($keys);

        if ($this->checkExistFillProperty()) {
            foreach ($all as $key => $value) {
                if (!in_array($key, $this->fill, true)) {
                    unset($all[$key]);
                }
            }

            return $all;
        }

        if ($this->checkExistGuardedProperty()) {
            foreach ($this->guarded as $key) {
                unset($all[$key]);
            }
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
            $method = $this->camelMethod($key);
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
     */
    public function passedValidation(): void
    {
        parent::passedValidation();

        $this->addAccessorMethods();

        $this->callModelCast();

        $this->afterValidation();
    }

    /**
     * バリデーション準備
     *
     * @return void
     */
    public function validateResolved()
    {
        $this->construct();

        parent::validateResolved();
    }

    /**
     * 変更前の Request クラスを返却
     *
     * @return mixed
     */
    public function before()
    {
        return self::$beforeRequest;
    }

    /**
     * 未定義プロパティへのアクセス
     *
     * @param  mixed  $key
     * @return mixed
     */
    public function __get($key)
    {
        $response = parent::__get($key);
        if (
            !is_null($response)
            ||
            // fill または guarded が存在しなければ終了
            !$this->checkExistFillProperty() || !$this->checkExistGuardedProperty()
        ) {
            return $response;
        }

        if (in_array($this->camelMethod($key), $this->getThisClassAccessorMethods(), true)) {
            return $this->{$this->camelMethod($key)}();
        }

        return null;
    }

    /**
     * validation 終了後の処理
     *
     * @return void
     */
    protected function afterValidation()
    {

    }

    /**
     * 前処理（変更前の Request を複製）
     *
     * @return void
     */
    private function construct()
    {
        self::$beforeRequest = clone $this;
    }

    /**
     * アクセサメソッドを追加
     *
     * @return void
     */
    private function addAccessorMethods()
    {
        foreach ($this->getThisClassAccessorMethods() as $method) {
            preg_match('/(?<=get_).+(?=_attribute)/', str::snake($method), $match);
            if (empty($match[0])) {
                continue;
            }

            $return_value = $this->{$method}();
            // 返却値が NULL のアクセサは出力しない設定かどうか
            if ($this->checkExistNullDisabledProperty() && is_null($return_value)) {
                continue;
            }
            // 返却値が empty のアクセサは出力しない設定かどうか
            if ($this->checkExistEmptyDisabledProperty() && empty($return_value)) {
                continue;
            }

            $this->merge([
                $match[0] => $return_value,
            ]);
        }
    }

    /**
     * model クラスの cast 処理を呼び出す
     *
     * @return void
     */
    private function callModelCast()
    {
        // $casts が存在している
        if (!$this->checkExistCastsProperty()) {
            return;
        }

        $model = new CastModel($this->casts);
        foreach ($this->casts as $key => $value) {
            if (!isset($this->{$key}) && $this->checkExistNullDisabledProperty()) {
                continue;
            }

            $this->merge([
                $key => $model->castAttribute($value, $this->{$key}),
            ]);
        }
    }

    /**
     * $fill プロパティが存在しているかチェック
     *
     * @return bool
     */
    private function checkExistFillProperty(): bool
    {
        return (
            property_exists(get_class(), 'fill')
            &&
            is_array($this->fill) && !empty($this->fill)
        );
    }

    /**
     * $guarded プロパティが存在しているかチェック
     *
     * @return bool
     */
    private function checkExistGuardedProperty(): bool
    {
        return (
            property_exists(get_class(), 'guarded')
            &&
            is_array($this->guarded) && !empty($this->guarded)
        );
    }

    /**
     * $casts プロパティが存在しているかチェック
     *
     * @return bool
     */
    private function checkExistCastsProperty(): bool
    {
        return (
            property_exists(get_class(), 'casts')
            &&
            !empty($this->casts) && is_array($this->casts)
        );
    }

    /**
     * $null_disabled プロパティが存在しているかチェック
     *
     * @return bool
     */
    private function checkExistNullDisabledProperty(): bool
    {
        return (
            property_exists(get_class(), 'null_disabled')
            &&
            $this->null_disabled
        );
    }

    /**
     * $empty_disabled プロパティが存在しているかチェック
     *
     * @return bool
     */
    private function checkExistEmptyDisabledProperty(): bool
    {
        return (
            property_exists(get_class(), 'empty_disabled')
            &&
            $this->empty_disabled
        );
    }

    /**
     * 対象リクエストクラスのアクセサメソッドを取得
     *
     * @return array
     */
    private function getThisClassAccessorMethods(): array
    {
        return array_merge(
            preg_grep('/^get.*Attribute/', get_class_methods(get_class())) ?? []
        );
    }

    /**
     * アクセサから同じアクセサが呼び出されているかチェック
     *
     * @param  string  $key
     * @return bool
     */
    private function checkThisFunctionCall(string $key): bool
    {
        $debug_backtrace = array_column(debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 10), 'function');

        return (in_array($this->camelMethod($key), $debug_backtrace, true) !== false);
    }

    /**
     * キャメルケースに変換
     *
     * @param  string  $key
     * @return string
     */
    private function camelMethod(string $key): string
    {
        return Str::camel('get_'. $key . '_attribute');
    }
}
