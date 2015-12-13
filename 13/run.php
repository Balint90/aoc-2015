#!/usr/bin/php
<?php
	require_once(dirname(__FILE__) . '/../common/common.php');
	$input = getInputLines();

	$people = array();
	foreach ($input as $details) {
		preg_match('#(.*) would (gain|lose) ([0-9]+) happiness units by sitting next to (.*).#SAD', $details, $m);
		list($all, $who, $direction, $units, $person) = $m;

		if (!isset($people[$who])) { $people[$who] = array(); }

		$people[$who][$person] = ($direction == 'lose') ? 0 - $units : $units;
	}

	function calculateHappiness($people, $order) {
		$total = 0;
		for ($i = 0; $i < count($order); $i++) {
			$last = ($i == 0) ? count($order) - 1 : $i - 1;
			$next = ($i + 1) % count($order);

			$total += $people[$order[$i]][$order[$next]];
			$total += $people[$order[$i]][$order[$last]];
		}

		return $total;
	}

	function getBest($people) {
		$perms = array_keys($people);
		$start = array_shift($perms);
		$perms = getPermutations($perms);

		$best['order'] = array();
		$best['happiness'] = 0;
		foreach ($perms as $p) {
			array_unshift($p, $start);
			$happiness = calculateHappiness($people, $p);

			if ($happiness > $best['happiness']) {
				$best['happiness'] = $happiness;
				$best['order'] = $p;
			}
		}

		return $best;
	}

	$part1 = getBest($people);
	echo 'Part 1: ', $part1['happiness'], "\n";
	echo "\t", 'Seating Order: ', implode(', ', $part1['order']), "\n";

	$people['You'] = array();
	foreach (array_keys($people) as $p) {
		$people['You'][$p] = 0;
		$people[$p]['You'] = 0;
	}

	$part2 = getBest($people);
	echo 'Part 2: ', $part2['happiness'], "\n";
	echo "\t", 'Seating Order: ', implode(', ', $part2['order']), "\n";
