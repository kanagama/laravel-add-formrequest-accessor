<?php

namespace Kanagama\FormRequestAccessor;

use Illuminate\Support\Str;
use Kanagama\FormRequestAccessor\Exceptions\ImmutableException;
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
    private static $process = true;

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
                'immutable'      => $this->checkExistImmutableProperty() ? $this->immutable : null,
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

        // $enabled もしくは $disabled が指定されている場合はそのまま返却
        if ($this->checkExistEnabledProperty() || $this->checkExistDisabledProperty()) {
            return $all;
        }

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
        $this->prepareForAccessor();

        parent::passedValidation();

        $this->addAccessorMethods();

        $this->callModelCast();

        $this->afterValidation();

        $this->endRequest();
    }

    /**
     * バリデーション準備
     *
     * @return void
     */
    public function validateResolved()
    {
        $this->startRequest();

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
     * immutable が設定されている場合、merge() を利用不可
     *
     * @param  array  $input
     * @return $this
     */
    public function merge($input): self
    {
        if (self::$process || !$this->checkExistImmutableProperty()) {
            return parent::merge($input);
        }

        throw new ImmutableException();
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
            (self::$process || ($this->checkExistDisabledProperty() || $this->checkExistEnabledProperty()))
            ||
            // fill または guarded が存在しなければ終了
            (!$this->checkExistFillProperty() || !$this->checkExistGuardedProperty())
        ) {
            return $response;
        }

        if (in_array($this->camelMethod($key), $this->getThisClassAccessorMethods(), true)) {
            return $this->{$this->camelMethod($key)}();
        }

        return null;
    }

    /**
     * アクセサ実行前の処理
     *
     * @return void
     */
    public function prepareForAccessor()
    {

    }

    /**
     * validation 終了後の処理
     *
     * @return void
     */
    public function afterValidation()
    {

    }

    /**
     * 前処理（変更前の Request を複製）
     *
     * @return void
     */
    private function startRequest()
    {
        self::$beforeRequest = clone $this;
    }

    /**
     * 後処理
     *
     * @return void
     */
    private function endRequest()
    {
        // __get() で アクセサを動作させない
        self::$process = false;

        // $enabled で指定されていない、または $disabled で指定されているプロパティは削除
        foreach (get_object_vars($this) as $key => $value) {
            if ($this->checkExistEnabledProperty()) {
                if (empty($this->enabled[$key])) {
                    unset($this->{$key});
                }
                continue;
            }
            if ($this->checkExistDisabledProperty()) {
                if (!empty($this->disabled[$key])) {
                    unset($this->{$key});
                }
                continue;
            }

            if ($this->checkNullPropertyDisabled($key) || $this->checkEmptyPropertyDisabled($key)) {
                unset($this->{$key});
                continue;
            }
        }
    }

    /**
     * 返却値が NULL のアクセサは出力しない設定かどうか
     *
     * @param  string  $key
     * @return bool
     */
    private function checkNullPropertyDisabled(string $key): bool
    {
        return (
            $this->checkExistNullDisabledProperty()
            &&
            property_exists(get_class(), $key)
            &&
            is_null($this->{$key})
        );
    }

    /**
     * 返却値が empty のアクセサは出力しない設定かどうか
     *
     * @param  string  $key
     * @return bool
     */
    private function checkEmptyPropertyDisabled(string $key): bool
    {
        return (
            $this->checkExistEmptyDisabledProperty()
            &&
            property_exists(get_class(), $key)
            &&
            empty($this->{$key})
        );
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
     * @test
     */
    private function checkExistFillProperty(): bool
    {
        return (
            property_exists(get_class(), 'fill')
            &&
            !empty($this->fill) && is_array($this->fill)
        );
    }

    /**
     * $guarded プロパティが存在しているかチェック
     *
     * @return bool
     * @test
     */
    private function checkExistGuardedProperty(): bool
    {
        return (
            property_exists(get_class(), 'guarded')
            &&
            !empty($this->guarded) && is_array($this->guarded)
        );
    }

    /**
     * $casts プロパティが存在しているかチェック
     *
     * @return bool
     * @test
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
     * @test
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
     * $immutable プロパティが存在しているかチェック
     *
     * @return bool
     * @test
     */
    private function checkExistImmutableProperty(): bool
    {
        return (
            property_exists(get_class(), 'immutable')
            &&
            $this->immutable
        );
    }

    /**
     * $empty_disabled プロパティが存在しているかチェック
     *
     * @return bool
     * @test
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
     * $disabled プロパティが存在しているかチェック
     *
     * @return bool
     * @test
     */
    private function checkExistDisabledProperty(): bool
    {
        return (
            property_exists(get_class(), 'disabled')
            &&
            !empty($this->disabled) && is_array($this->disabled)
        );
    }

    /**
     * $enabled プロパティが存在しているかチェック
     *
     * @return bool
     * @test
     */
    private function checkExistEnabledProperty(): bool
    {
        return (
            property_exists(get_class(), 'enabled')
            &&
            !empty($this->enabled) && is_array($this->enabled)
        );
    }

    /**
     * 対象リクエストクラスのアクセサメソッドを取得
     *
     * @return array
     * @test
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
     * @test
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
     * @test
     */
    private function camelMethod(string $key): string
    {
        return Str::camel('get_'. $key . '_attribute');
    }
}
