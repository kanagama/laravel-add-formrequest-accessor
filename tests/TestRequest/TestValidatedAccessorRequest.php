<?php

namespace Kanagama\FormRequestAccessor\Tests\TestRequest;

use Illuminate\Foundation\Http\FormRequest;
use Kanagama\FormRequestAccessor\FormRequestAccessor;

/**
 * @property-read int $int
 *
 * @author k-nagama <k.nagama0632@gmail.com>
 */
class TestValidatedAccessorRequest extends FormRequest
{
    use FormRequestAccessor;

    /**
     * @var bool
     */
    protected $validated_accessor = true;

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'id' => [
                'required',
                'integer',
            ],
            'name' => [
                'required',
                'string',
                'max:255',
            ],
        ];
    }

    /**
     * @return int
     */
    public function getIntAttribute(): int
    {
        return 1;
    }
}
