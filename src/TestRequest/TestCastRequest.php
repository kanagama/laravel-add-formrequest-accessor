<?php

namespace Kanagama\FormRequestAccessor\TestRequest;

use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Kanagama\FormRequestAccessor\FormRequestAccessor;

/**
 * @property-read int $cast_int
 * @property-read string $cast_string
 * @property-read bool $cast_bool
 * @property-read Carbon $cast_carbon
 *
 * @author k-nagama <k.nagama0632@gmail.com>
 */
class TestCastRequest extends FormRequest
{
    use FormRequestAccessor;

    protected $casts = [
        'int'         => 'integer',
        'cast_int'    => 'integer',

        'string'      => 'string',
        'cast_string' => 'string',

        'bool'        => 'boolean',
        'cast_bool'   => 'boolean',

        'carbon'      => 'datetime',
        'cast_carbon' => 'datetime',
    ];

    /**
     * @return string
     */
    public function getCastIntAttribute(): string
    {
        return '1';
    }

    /**
     * @return int
     */
    public function getCastStringAttribute(): int
    {
        return 1;
    }

    /**
     * @return int
     */
    public function getCastBoolAttribute(): int
    {
        return 1;
    }

    /**
     * @return string
     */
    public function getCastCarbonAttribute(): string
    {
        return date('Y-m-d');
    }
}
