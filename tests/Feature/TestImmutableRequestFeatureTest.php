<?php

namespace Kanagama\FormRequestAccessor\Tests\Feature;

use Kanagama\FormRequestAccessor\Exceptions\ImmutableException;
use Kanagama\FormRequestAccessor\Tests\TestCase;
use Kanagama\FormRequestAccessor\Tests\TestRequest\TestImmutableRequest;

/**
 * @author k-nagama <k.nagama0632@gmail.com>
 */
class TestImmutableRequestFeatureTest extends TestCase
{
    /**
     * @var TestImmutableRequest
     */
    private TestImmutableRequest $testImmutableRequest;

    /**
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        // immutable
        $this->testImmutableRequest = new TestImmutableRequest([
            'test_immutable' => 1,
        ]);
        $this->testImmutableRequest->passedValidation();
    }

    /**
     * @test
     * @group immutable
     */
    public function immutableなRequestクラスでmergeしようとすると例外()
    {
        $this->expectException(ImmutableException::class);
        $this->testImmutableRequest->merge([
            'merge' => 1,
        ]);
    }

    /**
     * @test
     * @group immutable
     */
    public function immutableなRequestクラスでreplaceしようとすると例外()
    {
        $this->expectException(ImmutableException::class);
        $this->testImmutableRequest->replace([
            'replace' => 1,
        ]);
    }

    /**
     * @test
     * @group immutable
     */
    public function immutableなRequestクラスでoffsetUnsetしようとすると例外()
    {
        $this->expectException(ImmutableException::class);
        $this->testImmutableRequest->offsetUnset('test_immutable');
    }

    /**
     * @test
     * @gruop immutable
     */
    public function immutableなRequestクラスでoffsetSetしようとすると例外()
    {
        $this->expectException(ImmutableException::class);
        $this->testImmutableRequest->offsetSet('test_immutable', 2);
    }
}