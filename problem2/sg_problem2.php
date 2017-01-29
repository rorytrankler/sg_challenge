<?php
	$player_id = isset($_POST["player_id"]) ? (int) $_POST["player_id"] : null;
	$hash = isset($_POST["hash"]) ? $_POST["hash"] : null;
	$coins_won = isset($_POST["coins_won"]) ? (int) $_POST["coins_won"] : null;
	$coins_bet = isset($_POST["coins_bet"]) ? (int) $_POST["coins_bet"] : null;
	
	$connectionString = 'mysql:host=173.194.254.248;dbname=sg_challenge;charset=utf8';
	$db_username = 'root';
	$db_pass = 'gamemaster5';	
	
	// validate fields
	if ( ! isset($player_id) || ! isset($hash) || ! isset($coins_won) || ! isset($coins_bet)) {
		echo '{ "error": "missing one or more arguments." }';
		return;
	}
	
	// get player by player_id	
	$db = new PDO($connectionString, $db_username, $db_pass);
	$stmt = $db->prepare('CALL get_player_by_id(:player_id)');
	$stmt->execute(
		array(
			':player_id' => $player_id
		)
	);
	
	$rows = $stmt->fetchAll();
	$player = count($rows) > 0 ? $rows[0] : null;
	
	if ( ! isset($player)) {
		echo '{ "error": "player does not exist" }';
		return;
	}
	
	// Update player's lifetime spins and credits
	$lifetime_spins = ((int) $player['lifetime_spins']) + 1;
	$credits = ((double) $player['credits']) + ($coins_won - $coins_bet);
	
	$stmt = $db->prepare('CALL update_player_data(:player_id, :credits, :lifetime_spins)');
	$stmt->execute(
		array(
			':player_id' => $player_id,
			':credits' => $credits,
			':lifetime_spins' => $lifetime_spins
		)
	);
	
	// output updated info
	$output = [
		'player_id' => $player_id,
		'name' => $player['name'],
		'credits' => $credits,
		'lifetime_spins' => $lifetime_spins,
		'lifetime_average_return' => (($credits/$lifetime_spins) * 100) . '%'
	];
	
	echo json_encode($output, true);
?>
