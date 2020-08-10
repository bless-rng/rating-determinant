<?php

namespace BlessRng;

use PHPUnit\Exception;

class RatingDeterminant
{
    public static function getRankedTeamsFromEndPoint($endPoint) {
        if (filter_var($endPoint, FILTER_VALIDATE_URL) === false) {
            throw new \Exception("Bad endpoint");
        }
        $jsonData = static::getEndpointData($endPoint);
        if (!$jsonData) {
            throw new \Exception("Json data not found");
        }
        return static::getRankedTeamsFromJsonData($jsonData);
    }

    public static function getRankedTeamsFromJsonData($jsonTeamsData) {
        $teams = static::convertJsonToTeamsData($jsonTeamsData);
        if (!$teams) {
            throw new \Exception("Json data can not be converted to expected object type");
        }
        usort($teams, function ($teamA, $teamB) {
            return $teamB->getScores() - $teamA->getScores();
        });
        return json_encode(static::getRankedTeams($teams));
    }

    private static function getEndpointData($endpoint)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_HEADER,"Content-type: application/json");
        curl_setopt($curl, CURLOPT_URL, $endpoint);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $result = curl_exec($curl);

        curl_close($curl);
        return $result;
    }

    private static function convertJsonToTeamsData($data) {
        try {
            $rawData = json_decode($data);
            if (!$rawData) return null;
            $teams = [];
            foreach ($rawData as $object) {
                if (!isset($object)) continue;
                $team = new Team();
                $team->setTeam($object->team);
                $team->setScores($object->scores);
                $teams[] = $team;
            }
            return $teams;
        } catch (Exception $e) {
            return null;
        }
    }

    private static function getRankedTeams($teams) {
        $rank = 1;
        $lastTeam = null;
        foreach ($teams as $team) {
            if ($lastTeam == null) {
                $team->setRank($rank);
                $lastTeam = $team;
                $rank++;
                continue;
            }
            $currentRank = $lastTeam->getScores() == $team->getScores()?$lastTeam->getRank():$rank;
            $team->setRank($currentRank);
            $lastTeam = $team;
            $rank++;
        }
        return $teams;
    }
}