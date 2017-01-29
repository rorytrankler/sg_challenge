<?php

// read input and load into $people array
$file_str = file_get_contents('input.txt');
$people = json_decode($file_str, true);

// key value dictionary to keep track of alive counts in a given year
$people_alive_each_year = [];

foreach ($people as $person) {
	$birth_year = $person['birth_year'];
	$end_year = $person['end_year'];
	
	for ($i = 0; $i < ($end_year - $birth_year); $i++) {
		$year_str = ((string)($birth_year + $i));
		
		if (array_key_exists($year_str, $people_alive_each_year)) {
			$people_alive_each_year[$year_str] += 1;
		} else {
			$people_alive_each_year[$year_str] = 1;
		}
	}
}

// sort by number of people alive each year descending
arsort($people_alive_each_year);

$output = '{ "year_with_most_people_alive": ' . key($people_alive_each_year) . ' }';
file_put_contents('output.txt', $output, FILE_APPEND);

?>
