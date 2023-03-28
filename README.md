# laravel-add-formrequest-accessor
laravel の FormRequest に accessor 機能を付与します。

## Qiita に詳しい説明書いてます
https://qiita.com/kazumacchi/items/aebfe8dfccbfd28acaf4


## インストール

```
$ composer require kanagama/laravel-add-formrequest-accessor:1.*
```

## 使い方
引数ありませんが、model のアクセサと似せました。
下記コードを参照して下さい


### Request

```php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Kanagama\FormRequestAccessor\FormRequestAccessor;

/**
 * @property-read string $full_name
 */
class BookingRequest extends FormRequest
{
    use FormRequestAccessor;

    /**
     * フルネームを取得
     *
     * @return string
     *
     * @author k.nagama <k.nagama0632@gmail.com>
     */
    public function getFullNameAttribute(): string
    {
        return $this->input('last_name') .' '. $this->input('first_name');
    }
}
```


### controller など

```php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\BookingRequest;

class BookingController extends Controller
{
    /**
     * @param  BookingRequest  $request
     *
     * @author k.nagama <k.nagama0632@gmail.com>
     */
    public function reserve(BookingRequest $request)
    {
        dd($request->full_name);
    }
```

### $guarded

 $guarded で指定したプロパティは、all() ファンクションで出力されません。


```php
protected $guarded = [
    'first_name',
];
```

### $fill

 $fill で指定したプロパティのみ all() ファンクションで出力されます。
 $guarded と一緒に記述されていた場合、$fill が優先されます。

```php
protected $fill = [
    'first_name',
];
```

### null_disabled

 $null_disabled で指定したアクセサの戻り値が null の場合、出力されません

### empty_disabled

 $empty_disabled で指定したアクセサの戻り値が空（empty()チェック）の場合、出力されません

## casts

指定したプロパティの型を変換します。
model の $casts と同様の挙動をします。

```php
protected $casts = [
    'id'        => 'int',
    'from_date' => 'string',
    'view_flg'  => 'bool',
];
```

## test

php vendor/bin/phpunit

## analysis

vendor/bin/phpstan analyse -l 3 src/

## metrics

php ./vendor/bin/phpmetrics --report-html=phpmetrics src/

