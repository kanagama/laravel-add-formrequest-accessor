<?php

namespace Kanagama\FormRequestAccessor\Exceptions;

use Exception;

/**
 * immutable な Request で merge() を呼び出した
 *
 * @author k-nagama <k.nagama0632@gmail.com>
 */
class ImmutableException extends Exception
{
    /**
     * @var string
     */
    protected $message = 'merge() was called on an immutable request class.';
}
