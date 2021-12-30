<?php

namespace Kanagama\FormRequestAccessor;

use Illuminate\Support\Str;

/**
 * FormRequest に accessor 機能を付与
 *
 * @method array $all($keys = null)
 *
 * @author k.nagama <k.nagama0632@gmail.com>
 */
trait FormRequestAccessor
{
    /**
     * Illuminate\Http\Concerns\InteractsWithInput::all() を override
     *
     * @param  array|mixed|null  $keys
     * @return array
     *
     * @author k.nagama <k.nagama0632@gmail.com>
     */
    public function all($keys = null): array
    {
        $accessor = preg_grep('/^get.*Attribute/', get_class_methods(get_class())) ?? [];
        foreach ($accessor as $method) {
            preg_match('/(?<=get_).+(?=_attribute)/', str::snake($method), $match);
            if (empty($match[0])) {
                continue;
            }

            // Request に追加
            $this->merge([
                $match[0] => $this->{$method}(),
            ]);
        }

        return parent::all($keys);
    }
}
