<?php
	
	namespace Classes;
	
	/**
	* 使う関数持ってるクラス
	*/
	class Functions {
	
		public function __construct() {
		}
		
		public function sortArrayByKey(array &$array, string $sortKey, int $sortType = SORT_ASC): void {
		    $tmp_array = [];
		    foreach ($array as $key => $row) {
		        $tmp_array[$key] = $row[$sortKey];
		    }
		    array_multisort($tmp_array, $sortType, $array);
		    unset($tmp_array);
		}
	
	}