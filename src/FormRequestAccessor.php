<?php

namespace Kanagama\FormRequestAccessor;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Kanagama\FormRequestAccessor\Exceptions\ImmutableException;
use Kanagama\FormRequestAccessor\Exceptions\UnsupportedOperandTypesException;
use Kanagama\FormRequestAccessor\Models\CastModel;
use Illuminate\Support\Facades\Route;

/**
 * FormRequest に accessor 機能を付与
 *
 * @method void passedValidation()
 * @method array all(mixed $keys)
 * @method mixed input(mixed $key = null, mixed $default = null)
 * @method mixed __get(mixed $key)
 * @method void validateResolved()
 * @method string getController()
 * @method string getAction()
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

    private static $requestProperties;

    /**
     * アクセサ追加前の all() を取得
     *
     * @return array
     */
    public function beforeAll(): array
    {
        if (is_null($this->before())) {
            return [];
        }

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
                'immutable'      => $this->getImmutableProperty(),
                'fillable'       => $this->getFillableProperty(),
                'guarded'        => $this->getGuardedProperty(),
                'casts'          => $this->getCastsProperty(),
                'null_disabled'  => $this->getNullDisabledProperty(),
                'empty_disabled' => $this->getEmptyDisabledProperty(),
            ],
            'all' => [
                'before_all' => $this->beforeAll(),
                'after_all'  => $this->all(),
            ],
            'accessor_methods' => $this->getThisClassAccessorMethods(),
        ]);
    }

    // 内部で all() が呼び出されており、アクセサは正常にセットされるため不要
    // public function only($keys)
    // {
    //     return parent::only($keys);
    // }
    // public function except($keys)
    // {
    //     return parent::except($keys);
    // }
    // public function has($key)
    // {
    //     return parent::has($key);
    // }
    // public function hasAny($keys)
    // {
    //     return parent::hasAny($keys);
    // }
    // public function missing($key)
    // {
    //     return parent::missing($key);
    // }

    // 内部で input() が呼び出されており、アクセサは正常にセットされるため不要
    // public function boolean($key = null, $default = false)
    // {
    //     return parent::boolean($key, $default);
    // }
    // public function filled($key)
    // {
    //     return parent::filled($key);
    // }
    // public function isNotFilled($key)
    // {
    //     return parent::isNotFilled($key);
    // }

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
        if (self::$process || $this->getEnabledProperty() || $this->getDisabledProperty()) {
            return $all;
        }

        if ($this->getNullDisabledProperty()) {
            foreach ($all as $key => $value) {
                if (is_null($value)) {
                    unset($all[$key]);
                }
            }
        }

        if ($this->getEmptyDisabledProperty()) {
            foreach ($all as $key => $value) {
                if (empty($value)) {
                    unset($all[$key]);
                }
            }
        }

        if ($this->getFillableProperty()) {
            foreach ($all as $key => $value) {
                if (!in_array($key, $this->getFillableProperty(), true)) {
                    unset($all[$key]);
                }
            }

            return $all;
        }

        foreach ($this->getGuardedProperty() as $key) {
            if (in_array($key, $this->getGuardedProperty(), true)) {
                unset($all[$key]);
            }
        }

        return $all;
    }

    /**
     * get() のオーバーライド
     * 定義前のアクセサメソッドが呼び出された場合
     *
     * @param  string  $key
     * @param  mixed|null  $default
     * @return mixed
     */
    public function get(string $key, mixed $default = null): mixed
    {
        return $this->getData($key, $default, parent::get($key));
    }

    /**
     * input のオーバーライド
     * 定義前のアクセサメソッドが呼び出された場合
     *
     * @param  string|null  $key
     * @param  mixed|null  $default
     * @return mixed
     */
    public function input($key = null, $default = null)
    {
        return $this->getData($key, $default, parent::input($key));
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
        if (is_null(self::$beforeRequest)) {
            return new $this;
        }

        return self::$beforeRequest;
    }

    /**
     * immutable が設定されている場合、 merge() を利用不可
     *
     * @param  array  $input
     * @return $this
     */
    public function merge($input): self
    {
        if ($this->isImmutable()) {
            return parent::merge($input);
        }

        throw new ImmutableException();
    }

    /**
     * immutable が設定されている場合、 replace() を利用不可
     *
     * @param  array  $input
     * @return $this
     */
    public function replace(array $input): self
    {
        if ($this->isImmutable()) {
            return parent::replace($input);
        }

        throw new ImmutableException();
    }

    /**
     * immutable が設定されている場合、 offsetUnset() を利用不可
     *
     * @param  string  $offset
     * @return void
     */
    public function offsetUnset($offset): void
    {
        if (!$this->isImmutable()) {
            throw new ImmutableException();
        }

        parent::offsetUnset($offset);
    }

    /**
     * immutable が設定されている場合、 offsetSet() を利用不可
     *
     * @param  string  $offset
     * @param  mixed  $value
     * @return void
     */
    public function offsetSet($offset, $value): void
    {
        if (!$this->isImmutable()) {
            throw new ImmutableException();
        }

        parent::offsetSet($offset, $value);
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
            (self::$process || ($this->getDisabledProperty() || $this->getEnabledProperty()))
            ||
            // fill または guarded が存在しなければ終了
            (!$this->getFillableProperty() && !$this->getGuardedProperty())
        ) {
            return $response;
        }

        if (in_array($this->camelMethod($key), $this->getThisClassAccessorMethods(), true)) {
            return $this->{$this->camelMethod($key)}();
        }

        if ($this->query($key, false) !== false) {
            return $this->query($key);
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
     * get(), input() からデータを取得する
     *
     * @param  mixed  $key
     * @param  mixed  $default
     * @param  mixed  $inputValue
     * @return mixed
     */
    private function getData($key, $default, $inputValue)
    {
        if (
            self::$process
            &&
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

        // デフォルト値が設定されていればそちらを返却する
        if (is_null($inputValue) && !is_null($default)) {
            return $default;
        }

        return $inputValue;
    }

    /**
     * 設定可能なプロパティ名を取得する
     *
     * @return array
     */
    private function getPropertyNames(): array
    {
        return [
            'immutable',
            'guarded',
            'fill',
            'fillable',
            'casts',
            'null_disabled',
            'empty_disabled',
            'disabled',
            'enabled',
        ];
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

        $properties = array_merge(get_object_vars($this), $this->all());
        // $enabled で指定されていない、または $disabled で指定されているプロパティは削除
        foreach ($properties as $key => $value) {
            // リクエストクラスのプロパティの場合、何もしない
            if ($this->checkRequestPropertyName($key)) {
                continue;
            }

            if ($this->getEnabledProperty()) {
                if (!in_array($key, $this->getEnabledProperty(), true)) {
                    $this->offsetUnset($key);
                }
                continue;
            }
            if ($this->getDisabledProperty()) {
                if (in_array($key, $this->getDisabledProperty(), true)) {
                    $this->offsetUnset($key);
                }
                continue;
            }

            if ($this->checkDeleteNullProperty($key) || $this->checkDeleteEmptyProperty($key)) {
                $this->offsetUnset($key);
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
            $this->getNullDisabledProperty()
            &&
            (
                !property_exists(get_class(), $key)
                ||
                is_null($this->{$key})
            )
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
            $this->getEmptyDisabledProperty()
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
            if ($this->getNullDisabledProperty() && is_null($return_value)) {
                continue;
            }
            if ($this->getEmptyDisabledProperty() && empty($return_value)) {
                continue;
            }

            $this->merge([
                $match[0] => $return_value,
            ]);
        }
    }

    /**
     * コントローラー名を取得
     * 
     * @return string
     */
    public function getController(): string
    {
        $route = Route::currentRouteAction();
        if (empty($route) || strpos($route, '@') === false) {
            return '';
        }

        $namespace_controller = explode("@", $route)[0];
        $namespaces = explode("\\", $namespace_controller);

        return end($namespaces);
    }

    /**
     * アクション名を取得
     * 
     * @return string
     */
    public function getAction(): string
    {
        $route = Route::currentRouteAction();
        if (empty($route) || strpos($route, '@') === false) {
            return '';
        }

        return explode("@", $route)[1];
    }

    /**
     * model クラスの cast 処理を呼び出す
     *
     * @return void
     */
    private function callModelCast()
    {
        // $casts が存在している
        if (!$this->getCastsProperty()) {
            return;
        }

        $model = new CastModel($this->casts);
        foreach ($this->casts as $key => $value) {
            if (!isset($this->{$key}) && $this->getNullDisabledProperty()) {
                continue;
            }

            $this->merge([
                $key => $model->castAttribute($value, $this->{$key}),
            ]);
        }
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

    /**
     * リクエストクラス プロパティ名の一覧を取得する
     *
     * @return array
     */
    private function getRequestProperties(): array
    {
        if (!empty(self::$requestProperties)) {
            return self::$requestProperties;
        }

        $request = new FormRequest();
        $request->passedValidation();

        return self::$requestProperties = array_keys(get_object_vars($request));
    }

    /**
     * 固定プロパティ名であれば true
     *
     * @param  string $key
     * @return bool
     */
    private function checkRequestPropertyName(string $key): bool
    {
        return (
            in_array($key, $this->getRequestProperties(), true)
            ||
            in_array($key, $this->getPropertyNames(), true)
        );
    }

    /**
     * null の プロパティを削除するかどうか
     *
     * @param  string $key
     * @return bool
     */
    private function checkDeleteNullProperty(string $key): bool
    {
        return (
            // null_disabled が存在しており、true である
            $this->checkNullPropertyDisabled($key)
            &&
            // 対象プロパティが存在している
            property_exists(get_class(), $key)
            &&
            // null である
            is_null($this->{$key})
        );
    }

    /**
     * empty の プロパティを削除するかどうか
     *
     * @param  string $key
     * @return bool
     */
    private function checkDeleteEmptyProperty(string $key): bool
    {
        return (
            // empty_disabled が存在しており、true である
            $this->checkEmptyPropertyDisabled($key)
            &&
            // 対象プロパティが存在している
            property_exists(get_class(), $key)
            &&
            // 空である
            empty($this->{$key})
        );
    }

    /**
     * immutable かどうかチェック
     *
     * @return bool
     */
    private function isImmutable(): bool
    {
        return (self::$process || !$this->getImmutableProperty());
    }

    /**
     * null_disabled かどうかチェック
     *
     * @return bool
     */
    private function isNullDisabled(): bool
    {
        return (self::$process || !$this->getNullDisabledProperty());
    }

    /**
     * empty_disabled かどうかチェック
     *
     * @return bool
     */
    private function isEmptyDisabled(): bool
    {
        return (self::$process || !$this->getEmptyDisabledProperty());
    }

    /**
     * $immutable プロパティを取得
     *
     * @return bool
     * @test
     */
    private function getImmutableProperty(): bool
    {
        if (!property_exists(get_class(), 'immutable')) {
            return false;
        }

        if (is_bool($this->immutable)) {
            return $this->immutable;
        }

        throw new UnsupportedOperandTypesException();
    }

    /**
     * $null_disabled プロパティを取得
     *
     * @return bool
     * @test
     */
    private function getNullDisabledProperty(): bool
    {
        if (!property_exists(get_class(), 'null_disabled')) {
            return false;
        }

        if (is_bool($this->null_disabled)) {
            return $this->null_disabled;
        }

        throw new UnsupportedOperandTypesException();
    }

    /**
     * $empty_disabled プロパティを取得
     *
     * @return bool
     * @test
     */
    private function getEmptyDisabledProperty(): bool
    {
        if (!property_exists(get_class(), 'empty_disabled')) {
            return false;
        }

        if (is_bool($this->empty_disabled)) {
            return $this->empty_disabled;
        }

        throw new UnsupportedOperandTypesException();
    }

    /**
     * $fillable プロパティを取得
     *
     * @return array
     * @test
     */
    private function getFillableProperty(): array
    {
        if (property_exists(get_class(), 'fillable')) {
            if (is_array($this->fillable)) {
                return $this->fillable;
            }

            throw new UnsupportedOperandTypesException();
        }

        if (property_exists(get_class(), 'fill')) {
            if (is_array($this->fill)) {
                return $this->fill;
            }

            throw new UnsupportedOperandTypesException();
        }

        return [];
    }

    /**
     * $guarded プロパティを取得
     *
     * @return array
     * @test
     */
    private function getGuardedProperty(): array
    {
        if (!property_exists(get_class(), 'guarded')) {
            return [];
        }

        if (is_array($this->guarded)) {
            return $this->guarded;
        }

        throw new UnsupportedOperandTypesException();
    }

    /**
     * $enabled プロパティを取得
     *
     * @return array
     * @test
     */
    private function getEnabledProperty(): array
    {
        if (!property_exists(get_class(), 'enabled')) {
            return [];
        }

        if (is_array($this->enabled)) {
            return $this->enabled;
        }

        throw new UnsupportedOperandTypesException();
    }
    /**
     * $disabled プロパティを取得
     *
     * @return array
     * @test
     */
    private function getDisabledProperty(): array
    {
        if (!property_exists(get_class(), 'disabled')) {
            return [];
        }

        if (is_array($this->disabled)) {
            return $this->disabled;
        }

        throw new UnsupportedOperandTypesException();
    }

    /**
     * $casts プロパティを取得
     *
     * @return array
     * @test
     */
    public function getCastsProperty()
    {
        if (!property_exists(get_class(), 'casts')) {
            return [];
        }

        if (is_array($this->casts)) {
            return $this->casts;
        }

        throw new UnsupportedOperandTypesException();
    }
}
