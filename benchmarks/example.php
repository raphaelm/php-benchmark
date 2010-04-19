<?php
/*
 *      example.php
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

// Include benchmark library
require_once '../class/benchmark.php';

// Init class
$benchmark = new Benchmark;

// Two functions to compare
// for example: switch VS ternary operator
function benchmark_compare_one(){
	$n = rand(0,9);
	$i = 0;
	switch($n) {
		case 0:
			$i++;
			break;
		case 1:
			$i++;
			break;
		case 2:
			$i++;
			break;
		case 3:
			$i++;
			break;
		case 4:
			$i++;
			break;
		case 5:
			$i++;
			break;
		case 6:
			$i++;
			break;
		case 7:
			$i++;
			break;
		case 8:
			$i++;
			break;
		case 9:
			$i++;
			break;
		default:
			$i++;
	}
}

function benchmark_compare_two(){
	$n = rand(0,9);
	$i = 0;
	($n == 0)?$i++:($n == 1)?$i++:($n == 2)?$i++:($n == 3)?$i++:($n == 4)?$i++:
	($n == 5)?$i++:($n == 6)?$i++:($n == 7)?$i++:($n == 8)?$i++:($n == 9)?$i++:$i++;
}

// Add them
$benchmark->addFunction('switch(){}', 'benchmark_compare_one');
$benchmark->addFunction('?: operator', 'benchmark_compare_two');

// Benchmark by time
echo "Benchmark by time...\n";
$time = $benchmark->benchmarkTime(2); // two functions * 2 seconds = 4 seconds
$time->outputTable();

// Benchmark by calls
echo "Benchmark by calls...\n";
$count = $benchmark->benchmarkCount(1000000); // two functions * 100000 calls = 200000 calls
$count->outputTable();

