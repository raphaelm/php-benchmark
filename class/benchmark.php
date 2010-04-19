<?php
/*
 *      benchmark.php
 *      
 *      Copyright 2010 Raphael Michel <webmaster@raphaelmichel.de>
 *      
 *      This program is free software; you can redistribute it and/or modify
 *      it under the terms of the GNU General Public License as published by
 *      the Free Software Foundation; either version 2 of the License, or
 *      (at your option) any later version.
 *      
 *      This program is distributed in the hope that it will be useful,
 *      but WITHOUT ANY WARRANTY; without even the implied warranty of
 *      MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *      GNU General Public License for more details.
 *      
 *      You should have received a copy of the GNU General Public License
 *      along with this program; if not, write to the Free Software
 *      Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston,
 *      MA 02110-1301, USA.
 */

// Main class
class Benchmark {
	
	private $loadedFunctions = array();
	private $id = 0;
	
	// int addFunction ( string $name, callback $callback )
	// adds function for benchmark
	// returns functions $id
	public function addFunction ( $name, $callback ) {
		$this->loadedFunctions[$this->id] = array($name, $callback);
		$this->id++;
	}
	
	// void removeFunction ( int $id )
	// removes function $id
	public function removeFunktion ( $id ) {
		unset($lthis->loadedFunctions[$id]);
	}
	
	// BenchmarkResult benchmarkTime ([ int $seconds ])
	// do benchmark: execute functions as often as possible in $seconds seconds
	public function benchmarkTime ( $seconds = 10 ) {
		$result = new BenchmarkResult(BenchmarkResult::TYPE_TIME);
		
		if(count($this->loadedFunctions) == 0) throw new BenchmarkException('No functions loaded.');
		
		foreach($this->loadedFunctions as $id => $func){
			
			$start = microtime(true);
			$end = $start + $seconds;
			$c = 0;
			
			while(microtime(true) < $end){
				call_user_func($func[1]);
				$c++;
			}
			
			$result->addRow($func[0], $c);
		}
		
		return $result;
	}
	
	// BenchmarkResult benchmarkCount ([ int $count ])
	// do benchmark: execute functions $count times
	public function benchmarkCount ( $count = 1000 ) {
		$result = new BenchmarkResult(BenchmarkResult::TYPE_COUNT);
		
		foreach($this->loadedFunctions as $id => $func){
			
			$start = microtime(true);
			
			for($i = 0; $i < $count; $i++){
				call_user_func($func[1]);
			}
			
			$end = microtime(true);
			$result->addRow($func[0], ($end-$start));
		}
		
		return $result;
	}
	
}

// Object returned by Benchmark->benchmarkTime or Benchmark->benchmarkCount
class BenchmarkResult {
	const TYPE_TIME = 0;
	const TYPE_COUNT = 1;
	
	private $rows = array();
	private $type = -1;
	
	// void BenchmarkResult( const $type )
	public function __construct($type){
		$this->type = $type;
	}
	
	// void addRow ( string $name, int $value )
	// internal function for communication between Benchmark and BenchmarkResult
	public function addRow ($name, $value){
		$this->rows[] = array($name, $value);
	}
	
	// array getRawRows ()
	// returns an array with test results
	public function getRawRows () {
		return $this->rows;
	}
	
	// void outputTable ()
	// outputs the results in a nice table
	public function outputTable () {
		
		switch ($this->type) {
			case self::TYPE_TIME:
				$tbl = array(array("Name", "Calls", "Percentage"));
				break;
			case self::TYPE_COUNT:
				$tbl = array(array("Name", "Time (s)", "Percentage"));
				break;
			default:
				throw new BenchmarkException('Invalid benchmark type.');
				return false;
		}

		usort($this->rows, array($this, "usortCmp"));
		$best = $this->rows[0][1];
		
		foreach($this->rows as $row){
			$tbl[] = array($row[0], $row[1], round(($row[1]/$best)*100).'%');
		}
		
		echo $this->formatTable($tbl);		
	}
	
	// int usortCmp ( array $a, array $b )
	// callback for usort()
	private function usortCmp ($a, $b) {
		if($a[1] > $b[1]) return 1;
		elseif($a[1] < $b[1]) return -1;
		else return 0;
	}
	
	// string formatTable ( array $d )
	// formats array as a nice table (on CLI)
	private function formatTable ($d) {
		// based on work by NoMoKeTo
		$str = '';
		$w=array_fill($f=0,$c=count($d[0]),0); 
		foreach($d as $r)for($i=0;$i<$c;$i++)if(strlen($r[$i])>$w[$i])$w[$i]=strlen($r[$i]); 
		$t='+'; 
		foreach($w as $v)$t.=str_repeat('-', $v+2).'+'; 
		$str .= $t; 
		foreach($d as $r){ 
			$str .= "\n|"; 
			for($i=0;$i<$c;$i++) $str .= ' '.$r[$i].str_repeat(' ',$w[$i]-strlen($r[$i])).' |'; 
			if(!$f++) $str .= "\n$t";
		} 
		$str .= "\n$t\n"; 
		return $str;
	} 
}

// Benchmark exception
class BenchmarkException extends Exception { }
