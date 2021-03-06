<?php

namespace Kanagama\FormRequestAccessor\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Model の casts を利用するためのクラス
 *
 * @author k-nagama <k.nagama0632@gmail.com>
 */
class CastModel extends Model
{
    use HasFactory;

    protected $casts = [];

    /**
     * @param  array  $casts
     */
    public function __construct(array $casts)
    {
        $this->casts = $casts;
    }

    /**
     * model の castAttribute() を呼び出す
     *
     * @param  mixed  $key
     * @param  mixed  $value
     * @return void
     */
    public function castAttribute($key, $value)
    {
        return parent::castAttribute($key, $value);
    }

    /**
     * @param  $key
     */
    public function getCastType($key)
    {
        return $key;
    }
}
