<?php

require_once '../../core/lib/path.class.php';
require_once Path :: get_path() . 'core/lib/global.inc.php';
require_once Path :: get_path() . 'pages/puzzle/lib/puzzle_manager.class.php';

	$rating_user = 1539.81;
	$rd_user = 178.31;
	$first_login = strtotime("06/01/2010");
	$last_login = strtotime("06/15/2010");
	$now = strtotime("07/01/2010");
	$number_exc = 64;
	$rating_puzzle = 1700;
	$score = 0;
	
	PuzzleManager::calculate_new_rating_and_rd($rating_user, $rd_user, $first_login, $last_login, $number_exc, $rating_puzzle, $score, $now);

	dump($rating_user);
	dump($rd_user);
	dump($rating_puzzle);
?>