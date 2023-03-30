<?php

namespace Kanagama\FormRequestAccessor\Tests\TestRequest;

use Illuminate\Foundation\Http\FormRequest;
use Kanagama\FormRequestAccessor\FormRequestAccessor;

/**
 * @property-read int $accessor_enabled
 *
 * @author k-nagama <k.nagama0632@gmail.com>
 */
class TestDisabledRequest extends FormRequest
{
    use FormRequestAccessor;

    /**
     * @var array
     */
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
