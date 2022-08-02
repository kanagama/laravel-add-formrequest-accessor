<?php

namespace Kanagama\FormRequestAccessor\TestRequest;

use Illuminate\Foundation\Http\FormRequest;
use Kanagama\FormRequestAccessor\FormRequestAccessor;

/**
 * @author k-nagama <k.nagama0632@gmail.com>
 */
class TestImmutableRequest extends FormRequest
{
    use FormRequestAccessor;

    protected $immutable = true;
}
