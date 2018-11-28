<?php

namespace Classes;

use API\ChallongeAPI;

/**
* ちゃろんげクラス
*/
class Challonge extends Functions
{

    private $challonge;
    private $tournament_slug;
    private $tournament;
    private $participants;
    private $results = [];

    public function __construct(String $challonge_api, String $url)
    {
        $this->challonge = new ChallongeAPI($challonge_api);
        $this->challonge->verify_ssl = false;
        $url_array = explode('/', $url);
        $this->tournament_slug = $url_array[3];
        $this->execute();
    }

    public function execute(): void
    {
        $this->setTournamentData();
        $this->setParticipants();
        $this->makeParticipantsList();
        $this->makeResultsList();
    }

    public function setTournamentData(): void
    {
        $this->tournament = $this->challonge->getTournament($this->tournament_slug);
    }

    public function setParticipants(): void
    {
        $this->participants = $this->challonge->getParticipants($this->tournament_slug);
    }

    public function getTournamentTitle(): string
    {
        return $this->tournament->name;
    }

    public function getTotalEntrants(): int
    {
        return count($this->participants);
    }

    public function getResultsList(): array
    {
        return $this->results;
    }

    public function makeParticipantsList(): void
    {
        foreach ($this->participants as $key => $participant) {
            $this->results[(integer)$participant->id]['name'] = (string)$participant->name;
            $this->results[(integer)$participant->id]['rank'] = '?';
            $this->results[(integer)$participant->id]['in-winner'] = 1;
            $this->results[(integer)$participant->id]['lost-loser'] = 0;
        }
    }

    public function makeResultsList(): void
    {
        $matches = $this->challonge->getMatches($this->tournament_slug, ['state'=>'complete']);
        $results = $this->results;
        if ($matches) {
            $rounds = [];
            foreach ($matches as $m_key => $match) {
                if ($results[(integer)$match->{'loser-id'}]['in-winner'] === 0) {
                    $results[(integer)$match->{'loser-id'}]['lost-loser'] = (integer)$match->round;
                    if ((integer)$match->round > 0) {
                        $results[(integer)$match->{'loser-id'}]['lost-winner'] = (integer)$match->round;
                        $results[(integer)$match->{'winner-id'}]['rank'] = 1;
                        $results[(integer)$match->{'loser-id'}]['rank'] = 2;
                    } else {
                        $rounds[] = (integer)$match->round;
                    }
                } else {
                    $results[(integer)$match->{'loser-id'}]['lost-winner'] = (integer)$match->round;
                    $results[(integer)$match->{'loser-id'}]['in-winner'] = 0;
                }
            }
            $this->sortArrayByKey($results, 'lost-loser', SORT_DESC);
            $rounds = array_count_values($rounds);
            $pre_round = 0;
            $sum_count = 0;

            foreach ($results as $id => $result) {
                if ($result['lost-loser'] < 0) {
                    if ($pre_round !== $result['lost-loser']) {
                        $pre_round = $result['lost-loser'];
                        $sum_count += $rounds[$result['lost-loser']];
                    }
                    if ($result['lost-loser'] > -4) {
                        unset($results[$id]);
                        continue;
                    }
                    $results[$id]['rank'] = $this->tournament->{'participants-count'} - $sum_count + 1;
                }
            }
            $this->results = $results;
        }
    }
}
