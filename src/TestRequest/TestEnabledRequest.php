<?php

namespace Kanagama\FormRequestAccessor\TestRequest;

use Illuminate\Foundation\Http\FormRequest;
use Kanagama\FormRequestAccessor\FormRequestAccessor;

/**
 * @author k-nagama <k.nagama0632@gmail.com>
 */
class TestEnabledRequest extends FormRequest
{
    use FormRequestAccessor;

    protected $enabled = [
        'accessor_enabled',
        'test_enabled',
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
