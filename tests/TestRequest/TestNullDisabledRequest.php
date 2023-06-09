<?php

namespace Kanagama\FormRequestAccessor\Tests\TestRequest;

use Illuminate\Foundation\Http\FormRequest;
use Kanagama\FormRequestAccessor\FormRequestAccessor;

/**
 * @property-read null $accessor_null
 * @property-read int $accessor_int
 * @property-read string $accessor_string_empty
 *
 * @author k-nagama <k.nagama0632@gmail.com>
 */
class TestNullDisabledRequest extends FormRequest
{
    use FormRequestAccessor;

    /**
     * @var bool
     */
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
