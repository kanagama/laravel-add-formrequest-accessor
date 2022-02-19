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
     * Illuminate\Http\Concerns\InteractsWithInput::passedValidation() を override
     *
     * @return void
     *
     * @author k.nagama <k.nagama0632@gmail.com>
     */
    public function passedValidation(): void
    {
        parent::passedValidation();

        $accessor = preg_grep('/^get.*Attribute/', get_class_methods(get_class())) ?? [];
        foreach ($accessor as $method) {
            preg_match('/(?<=get_).+(?=_attribute)/', str::snake($method), $match);
            if (empty($match[0])) {
                continue;
            }

            $this->merge([
                $match[0] => $this->{$method}(),
            ]);
        }
    }
}
