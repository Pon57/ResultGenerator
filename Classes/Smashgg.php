<?php

namespace Classes;

use API\SmashggAPI;

/**
* すまっしゅじーじークラス
*/
class Smashgg extends Functions
{
    private $smashgg;
    private $tournament_slug;
    private $event_slug;
    private $tournament;
    private $standings;
    private $results;
    private $lowest_rank;

    public function __construct($url)
    {
        $this->smashgg = new SmashggAPI();
        $url_array = explode('/', $url);
        $this->tournament_slug = $url_array[4];
        $this->event_slug = $url_array[6];
        $this->execute();
    }

    public function execute(): void
    {
        $this->setTournamentData();
        $this->setStandings();
        $this->calculateLosersRound4rank();
        $this->makeResultsList();
    }

    public function setTournamentData(): void
    {
        $this->tournament = json_decode($this->smashgg->getTournament($this->tournament_slug), true);
    }

    public function setStandings(): void
    {
        $this->standings = json_decode($this->smashgg->getStandings($this->tournament_slug, $this->event_slug, 1), true);
    }

    public function getTournamentTitle(): string
    {
        return $this->tournament['entities']['tournament']['name'];
    }

    public function getResultsList(): array
    {
        return $this->results;
    }

    public function getTotalEntrants(): int
    {
        return $this->standings['total_count'];
    }

    public function calculateLosersRound4rank(): void
    {
        $sum = 4;
        $sum_rank = 2;
        $lowest_rank = 4;
        $border = 9;
        $total_entrants = $this->getTotalEntrants() > 96 ? 96 : $this->getTotalEntrants();

        while ($border <= $total_entrants) {
            for ($i = 0; $i < 2; $i++) {
                $border += $sum;
                if ($border > $total_entrants) {
                    break;
                }
                $lowest_rank += $sum_rank;
            }
            $sum_rank += $sum_rank;
            $sum += $sum;
        }
        $this->lowest_rank = $lowest_rank;
    }

    public function makeResultsList(): void
    {
        $last_page = ceil($this->standings['total_count'] / 25);
        $current_page = 1;
        $results = [];
        try {
            while (true) {
                foreach ($this->standings['items']['entities']['entrants'] as $entrant) {
                    if ($entrant['finalPlacement'] > $this->lowest_rank) {
                        throw new \Exception('break');
                    }
                    if ($entrant['finalPlacement']) {
                        $results[(integer)$entrant['playerIds'][$entrant['participantIds'][0]]]['name'] = (string)$entrant['mutations']['players'][$entrant['playerIds'][$entrant['participantIds'][0]]]['gamerTag'];
                    }
                    $results[(integer)$entrant['playerIds'][$entrant['participantIds'][0]]]['rank'] = $entrant['finalPlacement'];
                    $results[(integer)$entrant['playerIds'][$entrant['participantIds'][0]]]['in-winner'] = 1;
                    $results[(integer)$entrant['playerIds'][$entrant['participantIds'][0]]]['lost-loser'] = 0;
                }
                $current_page++;
                $this->standings = json_decode($this->smashgg->getStandings($this->tournament_slug, $this->event_slug, $current_page), true);
            }
        } catch (\Exception $e) {
        }
        $this->results = $results;
    }
}
