<?php

namespace BlessRng\Test;

use BlessRng\RatingDeterminant;
use PHPUnit\Framework\TestCase;

class DeterminantTest extends TestCase
{

    /**
     * Test that true does in fact equal true
     */
    public function testRatingDeterminantWithJson()
    {
        $jsonData = '[{"team":"Axiom","scores": 88 },{"team":"BnL","scores": 65 },{"team":"Eva","scores": 99 },{"team":"WALL-E","scores": 99}]';
        $awaitResult = '[{"rank":1,"team":"Eva","scores":99},{"rank":1,"team":"WALL-E","scores":99},{"rank":3,"team":"Axiom","scores":88},{"rank":4,"team":"BnL","scores":65}]';
        $teams = RatingDeterminant::getRankedTeamsFromJsonData($jsonData);
        $this->assertEquals($awaitResult, $teams);
    }

    public function testRatingDeterminantWithEndpoint() {
        $endPoint = "http://localhost:8080/api/cities";
        $awaitResult = '[{"rank":1,"team":"Eva","scores":99},{"rank":1,"team":"WALL-E","scores":99},{"rank":3,"team":"Axiom","scores":88},{"rank":4,"team":"BnL","scores":65}]';
        $teams = RatingDeterminant::getRankedTeamsFromEndPoint($endPoint);
        $this->assertEquals($awaitResult, $teams);
    }
}
