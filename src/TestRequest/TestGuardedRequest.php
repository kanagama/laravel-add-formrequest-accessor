<?php

namespace Kanagama\FormRequestAccessor\TestRequest;

use Illuminate\Foundation\Http\FormRequest;
use Kanagama\FormRequestAccessor\FormRequestAccessor;

/**
 * @property-read int $accessor_guarded
 * @property-read int $accessor_int
 * @property-read string $accessor_string
 *
 * @author k-nagama <k.nagama0632@gmail.com>
 */
class TestGuardedRequest extends FormRequest
{
    use FormRequestAccessor;

    protected $guarded = [
        'accessor_guarded',
        'test_guarded',
    ];

    /**
     * @return string
     */
    public function getAccessorGuardedAttribute(): string
    {
        return 'a';
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
    public function getAccessorStringAttribute(): string
    {
        return '1';
    }
}
