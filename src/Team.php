<?php


namespace BlessRng;


class Team implements \JsonSerializable
{
    private $rank;
    private $team;
    private $scores;

    public function getTeam(): string {
        return $this->team;
    }

    public function setTeam(string $team) {
        $this->team = $team;
    }

    public function getScores(): int {
        return $this->scores;
    }

    public function setScores(int $scores) {
        $this->scores = $scores;
    }

    public function setRank(int $rank) {
        $this->rank = $rank;
    }

    public function getRank(): int {
        return $this->rank;
    }

    public function jsonSerialize()
    {
        return (object) get_object_vars($this);
    }
}