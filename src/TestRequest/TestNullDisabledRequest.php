<?php

namespace Kanagama\FormRequestAccessor\TestRequest;

use Illuminate\Foundation\Http\FormRequest;
use Kanagama\FormRequestAccessor\FormRequestAccessor;

/**
 * @author k-nagama <k.nagama0632@gmail.com>
 */
class TestNullDisabledRequest extends FormRequest
{
    use FormRequestAccessor;

    protected $null_disabled = true;

    /**
     * @return null
     */
    public function getAccessorNullAttribute()
    {
        return null;
    }

    /**
     * @return int
     */
    public function getAccessorIntAttribute(): int
    {
        return 1;
    }

    /**
     * @return string
     */
    public function getAccessorStringEmptyAttribute(): string
    {
        return '';
    }
}
