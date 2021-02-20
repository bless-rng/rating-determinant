<?php

namespace BlessRng\Test;

use BlessRng\CurlClient;
use BlessRng\RatingDeterminant;
use Exception;
use Iterator;
use PHPUnit\Framework\TestCase;

class DeterminantTest extends TestCase
{
    private CurlClient $curlClient;
    private RatingDeterminant $ratingDeterminant;

    protected function setUp(): void
    {
        $this->curlClient = $this->createMock(CurlClient::class);
        $this->ratingDeterminant = new RatingDeterminant($this->curlClient);
    }

    /** @dataProvider goodData */
    public function testGetRankedTeamsFromEndPointSuccess(string $expected, string $curlResponse)
    {
        $endpoint = 'https://ya.ru';
        $this->curlClient->method('getFromEndPoint')->willReturn($curlResponse);
        $result = $this->ratingDeterminant->getRankedTeamsFromEndPoint($endpoint);
        $this->assertEquals($expected, $result);
    }

    public function goodData()
    {
        yield [
            '[{"rank":"1","team":"Eva","scores":99},{"rank":"1","team":"WALL-E","scores":99},{"rank":"3","team":"Axiom","scores":88},{"rank":"4","team":"BnL","scores":65}]',
            '[{"team":"Eva","scores":99},{"team":"BnL","scores":65},{"team":"WALL-E","scores":99},{"team":"Axiom","scores":88}]',
        ];
        yield [
            '[{"rank":"1","team":"Eva","scores":99},{"rank":"2","team":"WALL-E","scores":98},{"rank":"3","team":"Axiom","scores":88},{"rank":"4","team":"BnL","scores":65}]',
            '[{"team":"Eva","scores":99},{"team":"BnL","scores":65},{"team":"WALL-E","scores":98},{"team":"Axiom","scores":88}]',
        ];
        yield [
            '[]',
            '[]',
        ];
    }

    /** @dataProvider throwableData */
    public function testGetRankedTeamsFromEndPointWithThrow(string $endpoint, $curlResult, string $expectionMessage)
    {
        $exception = new Exception($expectionMessage);
        $this->curlClient->method('getFromEndPoint')->with($endpoint)->willReturn($curlResult);
        $this->expectExceptionObject($exception);
        $this->ratingDeterminant->getRankedTeamsFromEndPoint($endpoint);
    }

    public function throwableData(): Iterator
    {
        yield [
            'endpoint',
            null,
            'Bad endpoint'
        ];

        yield [
            'https://ya.ru',
            false,
            'No curl response data',
        ];

        yield [
            'https://ya.ru',
            '',
            'Json data can not be converted to expected object type',
        ];
    }

}
