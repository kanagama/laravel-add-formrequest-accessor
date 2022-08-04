<?php

namespace Kanagama\FormRequestAccessor\TestRequest;

use Illuminate\Foundation\Http\FormRequest;
use Kanagama\FormRequestAccessor\FormRequestAccessor;

/**
 * @property-read int $accessor_enabled
 *
 * @author k-nagama <k.nagama0632@gmail.com>
 */
class TestFillableRequest extends FormRequest
{
    use FormRequestAccessor;

    protected $fillable = [
        'accessor_fillable',
        'test_fillable',
    ];

    /**
     * @return int
     */
    public function getAccessorFillableAttribute(): int
    {
        return 1;
    }

    /**
     * @return int
     */
    public function getAccessorGuardedAttribute(): int
    {
        return 1;
    }
}
