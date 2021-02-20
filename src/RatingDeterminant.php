<?php

namespace BlessRng;

use Exception;

class RatingDeterminant
{
    private CurlClient $curlClient;

    public function __construct(CurlClient $client)
    {
        $this->curlClient = $client;
    }

    /**
     * @param string $endPoint
     * @return string
     * @throws Exception
     */
    public function getRankedTeamsFromEndPoint(string $endPoint): string {
        if (filter_var($endPoint, FILTER_VALIDATE_URL) === false) {
            throw new Exception("Bad endpoint");
        }
        $jsonData = $this->getEndpointData($endPoint);
        return $this->getRankedTeamsFromJsonData($jsonData);
    }

    /**
     * @param string $jsonTeamsData
     * @return string
     * @throws Exception
     */
    private function getRankedTeamsFromJsonData(string $jsonTeamsData): string {
        $teams = $this->convertJsonToTeamsData($jsonTeamsData);
        if (null === $teams) {
            throw new Exception("Json data can not be converted to expected object type");
        }
        usort($teams, function ($teamA, $teamB) {
            return $teamB->getScores() - $teamA->getScores();
        });
        $teams = $this->getRankedTeams($teams);
        return json_encode($teams);
    }

    /**
     * @param string $endpoint
     * @return string
     * @throws Exception
     */
    private function getEndpointData(string $endpoint): string
    {
        $result = $this->curlClient->getFromEndPoint($endpoint);
        if ($result === false) {
            throw new Exception("No curl response data");
        }
        return $result;
    }

    /**
     * @param string $jsonData
     * @return ?array
     */
    private function convertJsonToTeamsData(string $jsonData): ?array
    {
        try {
            $rawData = json_decode($jsonData);
            if (null === $rawData) return null;
            $teams = array();
            foreach ($rawData as $object) {
                if (!isset($object)) continue;
                $team = new Team();
                $team->setTeam($object->team);
                $team->setScores($object->scores);
                $teams[] = $team;
            }
            return $teams;
        } catch (\Exception $e) {
            return null;
        }
    }

    private function getRankedTeams($teams) {
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