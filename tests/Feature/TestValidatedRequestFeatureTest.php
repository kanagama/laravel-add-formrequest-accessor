<?php

namespace Kanagama\FormRequestAccessor\Tests\Feature;

use Kanagama\FormRequestAccessor\Tests\TestCase;
use Kanagama\FormRequestAccessor\Tests\TestRequest\TestValidatedAccessorRequest;

/**
 * @author k-nagama <k.nagama0632@gmail.com>
 */
class TestValidatedRequestFeatureTest extends TestCase
{
    /**
     * @var TestRequest
     */
    private TestValidatedAccessorRequest $testValidatedAccessorRequest;

    /**
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->app->resolving(TestValidatedAccessorRequest::class, function ($resolved) {
            $resolved->merge([
                'id'    => 1,
                'name'  => 'k-nagama',
                'email' => 'k.nagama0632@gmail.com',
            ]);
        });

        $this->testValidatedAccessorRequest = app(TestValidatedAccessorRequest::class);
    }

    /**
     * @test
     */
    public function validatedにアクセサが追加されている()
    {
        $this->assertSame(
            $this->testValidatedAccessorRequest->validated(),
            [
                'id'    => 1,
                'name'  => 'k-nagama',
                'int'   => 1,
            ],
        );
    }
}
