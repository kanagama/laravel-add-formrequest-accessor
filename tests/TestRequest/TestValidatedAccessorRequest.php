<?php

namespace Kanagama\FormRequestAccessor\TestRequest;

use Illuminate\Foundation\Http\FormRequest;
use Kanagama\FormRequestAccessor\FormRequestAccessor;

/**
 * @property-read int $int
 *
 * @author k-nagama <k.nagama0632@gmail.com>
 */
class TestValidatedAccessorRequest extends FormRequest
{
    use FormRequestAccessor;

    /**
     * @var bool
     */
    protected $validated_accessor = true;

    /**
     * @return int
     */
    public function getIntAccessor(): int
    {
        return 1;
    }
}
