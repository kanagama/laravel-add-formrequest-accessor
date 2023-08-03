<?php

namespace Kanagama\FormRequestAccessor\Tests\Unit\Facades;

use Kanagama\FormRequestAccessor\Facades\AccessorName;
use Kanagama\FormRequestAccessor\Tests\TestCase;

/**
 * @author k-nagama <k.nagama0632@gmail.com>
 */
class AccessorNameTest extends TestCase
{
    /**
     * @test
     * @dataProvider getPropertyProvider
     * @param  string  $method
     * @param  string  $result
     */
    public function getProperty(
        string $method,
        string $result
    ) {
        $this->assertSame(
            $result,
            AccessorName::getProperty($method)
        );
    }

    /**
     * @return array
     */
    public function getPropertyProvider(): array
    {
        return [
            [
                'method' => 'getIntAttribute',
                'result' => 'int',
            ],
            [
                'method' => 'getAirportIdAttribute',
                'result' => 'airport_id',
            ],
            [
                'method' => 'getLocalStationIdAttribute',
                'result' => 'local_station_id',
            ],
        ];
    }

    /**
     * @test
     * @dataProvider getMethodProvider
     * @param  string  $property
     * @param  string  $result
     */
    public function getMethod(
        string $property,
        string $result
    ) {
        $this->assertSame(
            $result,
            AccessorName::getMethod($property)
        );
    }

    /**
     * @return array
     */
    public function getMethodProvider(): array
    {
        return [
            [
                'property' => 'int',
                'result'   => 'getIntAttribute',
            ],
            [
                'property' => 'airport_id',
                'result'   => 'getAirportIdAttribute',
            ],
            [
                'property' => 'local_station_id',
                'result'   => 'getLocalStationIdAttribute',
            ],
        ];
    }
}
