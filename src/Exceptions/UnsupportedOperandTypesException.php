<?php

namespace Kanagama\FormRequestAccessor\Exceptions;

use Exception;

/**
 * 型が異なる
 *
 * @author k-nagama <k.nagama0632@gmail.com>
 */
class UnsupportedOperandTypesException extends Exception
{
    protected $message = 'Unsupported operand types';
}
