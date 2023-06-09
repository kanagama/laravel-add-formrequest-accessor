<?php

namespace Kanagama\FormRequestAccessor\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Model の casts を利用するためのクラス
 *
 * @author k-nagama <k.nagama0632@gmail.com>
 */
class CastModel extends Model
{
    use HasFactory;

    /**
     * @var array
     */
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
     * @return mixed
     */
    public function castAttribute($key, $value)
    {
        return parent::castAttribute($key, $value);
    }

    /**
     * @param  mixed  $key
     * @return mixed
     */
    public function getCastType($key)
    {
        return $key;
    }
}
