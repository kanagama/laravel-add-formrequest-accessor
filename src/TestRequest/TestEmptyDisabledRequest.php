<?php

namespace Kanagama\FormRequestAccessor\TestRequest;

use Illuminate\Foundation\Http\FormRequest;
use Kanagama\FormRequestAccessor\FormRequestAccessor;

/**
 * @property-read int $accessor_int
 * @property-read string $accessor_string
 *
 * @author k-nagama <k.nagama0632@gmail.com>
 */
class TestEmptyDisabledRequest extends FormRequest
{
    use FormRequestAccessor;

    protected $empty_disabled = true;

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
    public function getAccessorIntZeroAttribute(): int
    {
        return 0;
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

    /**
     * @return string
     */
    public function getAccessorStringAttribute(): string
    {
        return 'a';
    }
}
