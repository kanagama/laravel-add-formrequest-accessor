<?php

namespace Kanagama\FormRequestAccessor\Exceptions;

use Exception;

/**
 * 型が異なる
 */
class UnsupportedOperandTypesException extends Exception
{
    protected $message = 'Unsupported operand types';
}
