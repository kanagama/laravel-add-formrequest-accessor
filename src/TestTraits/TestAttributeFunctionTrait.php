<?php

namespace Kanagama\FormRequestAccessor\TestTraits;

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
