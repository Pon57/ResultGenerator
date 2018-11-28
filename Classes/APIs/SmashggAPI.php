<?php
	
namespace API;

class SmashggAPI {
	// Attributes
	public $errors = [];
	public $warnings = [];
	public $status_code = 0;
	public $result;
	
	/* 
	  Class Constructor
	*/
	public function __construct() {
	}
	
	/*
	  makeCall()
	  $path - String
	  $params - array()
	*/
	public function makeCall(string $path='', array $params=[]): string {
	 
		// Clear the public vars
		$this->errors = array();
		$this->status_code = 0;
		$this->result = false;
		
		// Build the URL that'll be hit. If the request is GET, params will be appended later
		$call_url = "https://api.smash.gg/".$path;
		
		$curl_handle=curl_init();
		// Common settings
		curl_setopt($curl_handle,CURLOPT_CONNECTTIMEOUT,5);
		curl_setopt($curl_handle,CURLOPT_RETURNTRANSFER,1);
		
		$curlheaders = array(); //array('Content-Type: text/xml','Accept: text/xml');
		
		// Determine REST verb and set up params
		$call_url .= "?".http_build_query($params, "", "&");
		foreach($params as $key => $param){
			if($key > 0)
				$call_url .= '&';
			$call_url .= 'expand[]='.$param;
		}
		
		curl_setopt($curl_handle, CURLOPT_HTTPHEADER, $curlheaders); 
		curl_setopt($curl_handle,CURLOPT_URL, $call_url);
		
		$curl_result = curl_exec($curl_handle);	 
		$info = curl_getinfo($curl_handle);
		$this->status_code = (int) $info['http_code'];
		$return = false;
		if ($curl_result === false) { 
			// CURL Failed
			$this->errors[] = curl_error($curl_handle);
		} else {
			switch ($this->status_code) {
					
				case 500: // Oh snap!
					$return = $this->result = "Server returned HTTP 500";
					$this->errors[] = "Server returned HTTP 500";
					break;
				 
				case 401: // Bad API Key
				case 422: // Validation errors
				case 404: // Not found/Not in scope of account
				
				case 200:
					$return = $this->result = $curl_result;
					// Check if the result set is nil/empty
					if (mb_strlen($return) === 0) {
						$this->errors[] = "Result set empty";
						$return = "Result set empty";
					}
					break;
					
				default:
					$this->errors[] = "Server returned unexpected HTTP Code (".$this->status_code.")";
					$return = "Server returned unexpected HTTP Code (".$this->status_code.")";
			}
		}
		
		curl_close($curl_handle);
		return $return;
	}
	
	public function getTournament(string $tournament_slug, string $event_slug="", array $params=[]): string { //$params=['event', 'phase', 'groups', 'stations']
		if($event_slug != "")
			$tournament_slug .= "/event/".$event_slug;
		return $this->makeCall('tournament/'.$tournament_slug, $params);
	}
	
	public function getEvent(int $event_id, array $params=[]): string { //not slug, $params=['phase', 'groups']
		return $this->makeCall('event/'.$event_id, $params);
	}
	
	public function getPhase(int $phase_id, array $params=[]): string { //not slug, $params=['groups']
		return $this->makeCall('phase/'.$phase_id, $params);
	}
	
	public function getPhaseGroup(int $phase_group_id, array $params=[]): string { //not slug, $params=['sets', 'entrants', 'standings', 'seeds']
		return $this->makeCall('phase_group/'.$phase_group_id, $params);
	}
	
	public function getStandings(string $tournament_slug, string $event_slug, int $page=1, int $per_page=25): string {
		return $this->makeCall('tournament/'.$tournament_slug.'/event/'.$event_slug.'/standings?entityType=event&expand[]=entrants&mutations[]=playerData&mutations[]=standingLosses&page='.$page.'&per_page='.$per_page);
	}
}
?>
