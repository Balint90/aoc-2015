#!/usr/bin/php
<?php
	require_once(dirname(__FILE__) . '/../common/common.php');
	$input = getInputLines();

	$molecules = array();
	$medicine = '';
	foreach ($input as $details) {
		if (preg_match('#(.*) => (.*)#SADi', $details, $m)) {
			list($all, $start, $replacement) = $m;
			if (!isset($molecules[$start])) { $molecules[$start] = array(); }
			$molecules[$start][] = $replacement;
		} else if (!empty($details)) {
			$medicine = $details;
		}
	}

	function getReverseMapping($molecules) {
		$reverse = array();
		foreach ($molecules as $start => $results) {
			foreach ($results as $res) {
				$reverse[$res] = $start;
			}
		}
		uksort($reverse, function($a,$b) { return strlen($b) - strlen($a); });
		return $reverse;
	}

	/**
	 * For a given input, get an array of all the replacements that it can
	 * generate.
	 *
	 * @param $in Input molecule as a string.
	 * @return Array of possible outcomes.
	 */
	function getReplacements($in, $molecules) {
		$replacements = array();

		preg_match_all('/(e|[A-Z][a-z]*)/', $in, $match);

		for ($i = 0; $i < count($match[1]); $i++) {
			$r = $match[1];
			$m = $r[$i];
			if (isset($molecules[$m])) {
				foreach ($molecules[$m] as $mole) {
					$r[$i] = $mole;
					$replacements[] = implode('', $r);
				}
			}
		}

		return array_unique($replacements);
	}

	/**
	 * Take a given input, and find how many replacements are needed to get
	 * to there from a start of 'e'.
	 *
	 * This loops repeatedly, finding the single LONGEST replacement it can
	 * make and making it each time, until such time as we can make no more
	 * replacements. Hopefully by then, we are at 'e'.
	 *
	 * @param $input Desired input.
	 * @param $molecules Molecules that can make up $input
	 * @return Count of replacements needed from 'e'.
	 */
	function getFromE($input, $molecules) {
		$reverse = getReverseMapping($molecules);
		$result = 0;
		while (true) {
			foreach ($reverse as $k => $v) {
				if (strpos($input, $k) !== false) {
					if (isDebug()) { echo $k, " => ", $v, "\n"; }
					$out = preg_replace('/'.$k.'/', $v, $input, 1);
					break;
				}
			}
			if ($input == $out) { break; }
			$input = $out;
			$result++;
			if (isDebug()) { echo $input, "\n", $result, "\n"; }
		}
		return $result;
	}

	$replacements = getReplacements($medicine, $molecules);
	echo "Part 1: ", count($replacements), "\n";

	$count = getFromE($medicine, $molecules);
	echo 'Part 2: ', $count, "\n";
