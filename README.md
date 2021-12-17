# laravel-add-formrequest-accessor
laravel の FormRequest に accessor 機能を付与します。


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

### 注意事項
#### アクセサは記述されている順番で実行されるため、アクセサが自身より下に記述されているパラメータは取得できません。


```php
    /**
     * 消費税を算出
     *
     * @return int
     */
    public function getTaxAttribute(): int
    {
        // NG getTaxAttribute() より下に記述されている getPriceAttribute() は取得できません。
        // return floor($this->input('price') * 0.1);

        // OK
        return floor($this->getPriceAttribute() * 0.1);
    }

    /**
     * 料金を取得
     */
    public function getPriceAttribute(): int
```