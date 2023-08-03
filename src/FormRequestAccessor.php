<?php

namespace Kanagama\FormRequestAccessor;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Kanagama\FormRequestAccessor\Exceptions\ImmutableException;
use Kanagama\FormRequestAccessor\Exceptions\UnsupportedOperandTypesException;
use Kanagama\FormRequestAccessor\Models\CastModel;
/**
 * @property bool|null $immutable
 * @property array|null $casts;
 * @property array|null $guarded
 * @property array|null $disabled
 * @property bool|null $null_disabled
 * @property bool|null $nullDisabled
 * @property array|null $fillable
 * @property array|null $fill
 * @property bool|null $validated_accessor
 * @property bool|null $validatedAccessor
 *
 * FormRequest に accessor 機能を付与
 *
 * @author k.nagama <k.nagama0632@gmail.com>
 */
trait FormRequestAccessor
{
    /**
     * @var FormRequest
     */
    private FormRequest $beforeRequest;

    /**
     * @var bool
     */
    private bool $accessorProcess = false;

    /**
     * @var array
     */
    private array $requestProperties;

    /**
     * @var array
     */
    private array $accessors = [];

    /**
     * @var array
     */
    private array $alls = [];

    /**
     * @var array
     */
    private array $validatedProperties;

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
                'nullDisabled'  => $this->getNullDisabledProperty(),
                'emptyDisabled' => $this->getEmptyDisabledProperty(),
                'validated'      => $this->getValidatedAccessorProperty(),
            ],
            'all' => [
                'beforeAll' => $this->beforeAll(),
                'afterAll'  => $this->all(),
            ],
            'accessorMethods' => $this->getThisClassAccessorMethods(),
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
        if (!$this->getProcess()) {
            return parent::all($keys);
        }

        return $this->alls;
    }

    /**
     * get() のオーバーライド
     *
     * @param  string  $key
     * @param  mixed|null  $default
     * @return mixed
     */
    public function get(string $key, mixed $default = null): mixed
    {
        if (isset($this->accessors[$key])) {
            return parent::get($key, $this->accessors[$key] ?? $default);
        }

        return parent::get($key, $default);
    }

    /**
     * input のオーバーライド
     *
     * @param  string|null  $key
     * @param  mixed|null  $default
     * @return mixed
     */
    public function input($key = null, $default = null)
    {
        if (isset($this->accessors[$key])) {
            return parent::input($key, $this->accessors[$key] ?? $default);
        }

        return parent::input($key, $default);
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

        $this->setValidated();

        $this->addAll();

        $this->addAccessorMethods();

        $this->delAll();

        $this->callModelCast();

        $this->afterValidation();

        $this->endRequest();
    }

    private function setValidated()
    {
        $this->validatedProperties = parent::validated();
    }

    /**
     * @return array
     */
    public function validated($key = null, $default = null)
    {
        if (!$this->getProcess()) {
            return parent::validated($key, $default);
        }

        if (!$this->getValidatedAccessorProperty()) {
            if (empty($key)) {
                return $this->validatedProperties;
            }
            return $this->validatedProperties[$key] ?? $default;
        }

        $validatedProperties = array_merge($this->validatedProperties, $this->accessors);

        if (empty($key)) {
            return $validatedProperties;
        }

        return $validatedProperties[$key] ?? $default;
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
        if (is_null($this->beforeRequest)) {
            return new $this;
        }

        return $this->beforeRequest;
    }

    /**
     * immutable が設定されている場合、 merge() を利用不可
     *
     * @param  array  $input
     * @return $this
     */
    public function merge($input): self
    {
        if (!$this->getProcess()) {
            return parent::merge($input);
        }

        if (!$this->isImmutable()) {
            $this->accessors = array_merge(
                $this->accessors,
                $input
            );
            return $this;
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
        if (!$this->getProcess()) {
            return parent::replace($input);
        }

        if (!$this->isImmutable()) {
            $this->accessors = $input;
            return $this;
        }

        throw new ImmutableException();
    }

    /**
     * immutable が設定されている場合、 offsetUnset() を利用不可
     *
     * @param  string  $offset
     * @return void
     * @throws ImmutableException
     */
    public function offsetUnset($offset): void
    {
        if (!$this->getProcess()) {
            parent::offsetUnset($offset);
            return;
        }

        if ($this->isImmutable()) {
            throw new ImmutableException();
        }

        if (isset($this->accessors[$offset])) {
            unset($this->accessors[$offset]);
        }
    }

    /**
     * immutable が設定されている場合、 offsetSet() を利用不可
     *
     * @param  string  $offset
     * @param  mixed  $value
     * @return void
     * @throws ImmutableException
     */
    public function offsetSet($offset, $value): void
    {
        if (!$this->getProcess()) {
            parent::offsetSet($offset, $value);
            return;
        }

        if ($this->isImmutable()) {
            throw new ImmutableException();
        }

        $this->accessors[$offset] = $value;
    }

    /**
     * 未定義プロパティへのアクセス
     *
     * @param  mixed  $key
     * @return mixed
     */
    public function __get($key)
    {
        if (!$this->getProcess()) {
            return parent::__get($key);
        }

        if (isset($this->accessors[$key])) {
            return $this->accessors[$key];
        }

        return null;
    }

    /**
     * 未定義プロパティへの格納
     *
     * @param  string  $key
     * @param  mixed  $value
     * @throws ImmutableException
     */
    public function __set($key, $value)
    {
        if (in_array($key, $this->getPropertyNames(), true)) {
            $this->{$key} = $value;
            return;
        }

        if ($this->immutable) {
            throw new ImmutableException();
        }

        $this->accessors[$key] = $value;
    }

    /**
     * アクセサ実行前の処理
     */
    public function prepareForAccessor()
    {

    }

    /**
     * validation 終了後の処理
     */
    public function afterValidation()
    {

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
            'nullDisabled',
            'empty_disabled',
            'emptyDisabled',
            'validated_accessor',
            'validatedAccessor',
            'disabled',
            'enabled',
        ];
    }

    /**
     * 前処理（変更前の Request を複製）
     */
    private function startRequest()
    {
        $this->beforeRequest = clone $this;
    }

    /**
     * 後処理
     */
    private function endRequest()
    {
        // __get() で アクセサを動作させない
        $this->accessorProcess = true;
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
     *
     */
    private function addAll()
    {
        $this->accessors = $this->all();
        if ($this->getNullDisabledProperty()) {
            foreach ($this->accessors as $key => $value) {
                if (is_null($value)) {
                    unset($this->accessors[$key]);
                }
            }
        }

        if ($this->getEmptyDisabledProperty()) {
            foreach ($this->accessors as $key => $value) {
                if (empty($value)) {
                    unset($this->accessors[$key]);
                }
            }
        }

        if ($this->getEnabledProperty()) {
            foreach ($this->accessors as $key => $value) {
                if (!in_array($key, $this->getEnabledProperty(), true)) {
                    unset($this->accessors[$key]);
                }
            }
        }

        if (!$this->getEnabledProperty() && $this->getDisabledProperty()) {
            foreach ($this->getDisabledProperty() as $value) {
                if (isset($this->accessors[$value])) {
                    unset($this->accessors[$value]);
                }
            }
        }
    }

    /**
     * アクセサメソッドを追加
     */
    private function addAccessorMethods()
    {
        foreach ($this->getThisClassAccessorMethods() as $method) {
            preg_match('/(?<=get_).+(?=_attribute)/', str::snake($method), $match);
            if (empty($match[0])) {
                continue;
            }

            $returnValue = $this->{$method}();
            if ($this->getNullDisabledProperty() && is_null($returnValue)) {
                continue;
            }
            if ($this->getEmptyDisabledProperty() && empty($returnValue)) {
                continue;
            }

            $this->accessors[$match[0]] = $returnValue;
        }

        $this->alls = $this->accessors;
    }

    /**
     * all の結果を削る
     */
    private function delAll()
    {
        if ($this->getFillableProperty()) {
            foreach ($this->alls as $key => $value) {
                if (!in_array($key, $this->getFillableProperty(), true)) {
                    unset($this->alls[$key]);
                }
            }
        }
        if (!$this->getFillableProperty() && $this->getGuardedProperty()) {
            foreach ($this->getGuardedProperty() as $value) {
                if (!isset($this->accessors[$value])) {
                    unset($this->alls[$key]);
                }
            }
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

            $this->accessors[$key] = $model->castAttribute($value, $this->accessors[$key]);
        }
    }

    /**
     * 対象リクエストクラスのアクセサメソッドを取得
     *
     * @test
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
     * @test
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
     * @test
     * @param  string  $key
     * @return string
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
        if (!empty($this->requestProperties)) {
            return $this->requestProperties;
        }

        $request = new FormRequest();
        $request->passedValidation();

        return $this->requestProperties = array_keys(get_object_vars($request));
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
        return (
            $this->getProcess()
            &&
            $this->getImmutableProperty()
        );
    }

    /**
     * null_disabled かどうかチェック
     *
     * @return bool
     */
    private function isNullDisabled(): bool
    {
        return (
            $this->getProcess()
            ||
            !$this->getNullDisabledProperty()
        );
    }

    /**
     * empty_disabled かどうかチェック
     *
     * @return bool
     */
    private function isEmptyDisabled(): bool
    {
        return (
            $this->getProcess()
            ||
            !$this->getEmptyDisabledProperty()
        );
    }

    /**
     * @return bool
     */
    private function getProcess(): bool
    {
        return $this->accessorProcess;
    }

    /**
     * $immutable プロパティを取得
     *
     * @test
     * @return bool
     * @throws UnsupportedOperandTypesException
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
     * $validatedAccessor プロパティを取得
     *
     * @return bool
     * @throws UnsupportedOperandTypesException
     */
    private function getValidatedAccessorProperty(): bool
    {
        if (
            !property_exists(get_class(), 'validated_accessor')
            &&
            !property_exists(get_class(), 'validatedAccessor')
        ) {
            return false;
        }

        if (property_exists(get_class(), 'validated_accessor') && is_bool($this->validated_accessor)) {
            $this->validatedAccessor = $this->validated_accessor;
            return $this->validatedAccessor;
        }
        if (property_exists(get_class(), 'validatedAccessor') && is_bool($this->validatedAccessor)) {
            return $this->validatedAccessor;
        }

        throw new UnsupportedOperandTypesException();
    }

    /**
     * $nullDisabled プロパティを取得
     *
     * @test
     * @return bool
     * @throws UnsupportedOperandTypesException
     */
    private function getNullDisabledProperty(): bool
    {
        if (
            !property_exists(get_class(), 'null_disabled')
            &&
            !property_exists(get_class(), 'nullDisabled')
        ) {
            return false;
        }

        if (property_exists(get_class(), 'null_disabled') && is_bool($this->null_disabled)) {
            $this->nullDisabled = $this->null_disabled;
            return $this->nullDisabled;
        }
        if (property_exists(get_class(), 'nullDisabled') && is_bool($this->nullDisabled)) {
            return $this->nullDisabled;
        }

        throw new UnsupportedOperandTypesException();
    }

    /**
     * $emptyDisabled プロパティを取得
     *
     * @test
     * @return bool
     * @throws UnsupportedOperandTypesException
     */
    private function getEmptyDisabledProperty(): bool
    {
        if (
            !property_exists(get_class(), 'empty_disabled')
            &&
            !property_exists(get_class(), 'emptyDisabled')
        ) {
            return false;
        }

        if (property_exists(get_class(), 'empty_disabled') && is_bool($this->empty_disabled)) {
            $this->emptyDisabled = $this->empty_disabled;
            return $this->emptyDisabled;
        }
        if (property_exists(get_class(), 'emptyDisabled') && is_bool($this->emptyDisabled)) {
            return $this->emptyDisabled;
        }

        throw new UnsupportedOperandTypesException();
    }

    /**
     * $fillable プロパティを取得
     *
     * @test
     * @return array
     * @throws UnsupportedOperandTypesException
     */
    private function getFillableProperty(): array
    {
        if (property_exists(get_class(), 'fill') && !is_null($this->fill)) {
            $this->fillable = $this->fill;
            unset($this->fill);
        }

        if (property_exists(get_class(), 'fillable') && !is_null($this->fillable)) {
            if (is_array($this->fillable)) {
                return $this->fillable;
            }

            throw new UnsupportedOperandTypesException();
        }

        return [];
    }

    /**
     * $guarded プロパティを取得
     *
     * @test
     * @return array
     * @throws UnsupportedOperandTypesException
     */
    private function getGuardedProperty(): array
    {
        if (!property_exists(get_class(), 'guarded') || is_null($this->guarded)) {
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
     * @test
     * @return array
     * @throws UnsupportedOperandTypesException
     */
    private function getEnabledProperty(): array
    {
        if (!property_exists(get_class(), 'enabled') || is_null($this->enabled)) {
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
     * @test
     * @return array
     * @throws UnsupportedOperandTypesException
     */
    private function getDisabledProperty(): array
    {
        if (!property_exists(get_class(), 'disabled') || is_null($this->disabled)) {
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
     * @test
     * @return array
     * @throws UnsupportedOperandTypesException
     */
    private function getCastsProperty()
    {
        if (!property_exists(get_class(), 'casts') || is_null($this->casts)) {
            return [];
        }

        if (is_array($this->casts)) {
            return $this->casts;
        }

        throw new UnsupportedOperandTypesException();
    }
}
