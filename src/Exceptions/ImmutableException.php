<?php

namespace Kanagama\FormRequestAccessor\Exceptions;

use Exception;

/**
 * immutable な Request で merge() を呼び出した
 */
class ImmutableException extends Exception
{
    protected $message = 'merge() was called on an immutable request class.';
}
