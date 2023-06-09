<?php

namespace Kanagama\FormRequestAccessor\Tests\TestTraits;

/**
 * @author k-nagama <k.nagama0632@gmail.com>
 */
trait TestAttributeFunctionTrait
{
    /**
     * テスト用メソッド
     *
     * @return bool
     */
    public function getTestAttribute(): bool
    {
        return $this->refrectionClass('checkThisFunctionCall', [
            'test',
        ]);
    }
}
