<?php

namespace Kanagama\FormRequestAccessor;

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