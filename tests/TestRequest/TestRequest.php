<?php

namespace Kanagama\FormRequestAccessor\Tests\TestRequest;

use Illuminate\Foundation\Http\FormRequest;
use Kanagama\FormRequestAccessor\FormRequestAccessor;

/**
 * @author k-nagama <k.nagama0632@gmail.com>
 */
class TestRequest extends FormRequest
{
    use FormRequestAccessor;

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'test_offset_unset' => [
                'required',
                'integer',
            ],
        ];
    }
}
