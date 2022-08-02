<?php

namespace Kanagama\FormRequestAccessor\TestRequest;

use Illuminate\Foundation\Http\FormRequest;
use Kanagama\FormRequestAccessor\FormRequestAccessor;

/**
 * @author k-nagama <k.nagama0632@gmail.com>
 */
class TestDisabledRequest extends FormRequest
{
    use FormRequestAccessor;

    protected $disabled = [
        'accessor_disabled',
        'test_disabled',
    ];

    /**
     * @return int
     */
    public function getAccessorDisabledAttribute(): int
    {
        return 1;
    }

    /**
     * @return int
     */
    public function getAccessorEnabledAttribute(): int
    {
        return 1;
    }
}
