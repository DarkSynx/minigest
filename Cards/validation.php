<?php
	session_start();
		
	include 'mydb.class.php';
	$db = new MyDB();
	
	$results = $db->query('SELECT * FROM "validate" WHERE "id" ORDER BY "id" ASC LIMIT 0, 49999');
	while ($row = $results->fetchArray(SQLITE3_ASSOC)) {
		if($row['expire'] < time()) {
			$db->exec('DELETE FROM "users" WHERE "id" LIKE \'%' . $row['id'] . '%\'');	
			$db->exec('DELETE FROM "validate" WHERE "id" LIKE \'%' . $row['id'] . '%\'');
		}
	}
	
	$valid = filter_input(INPUT_POST, 'val', FILTER_SANITIZE_STRING);

	
	$result = $db->query('SELECT * FROM "validate" WHERE "key" LIKE \'%'. $val .'%\' ESCAPE \'\\\' ORDER BY "id" ASC LIMIT 0, 49999;');
	$val = $result->fetchArray(SQLITE3_ASSOC);	
	
	if( $val['key'] == $valid ) {
		
		
		$db->exec('UPDATE "users" SET "valid"="1" WHERE "id"="' . $val['id'] . '";');
		$db->exec('DELETE FROM "validate" WHERE "id" LIKE \'%' . $row['id'] . '%\'');
		
		$reponse = 'validation réussite'; 
		
		setcookie('ciu', $_SESSION['ciu']);
	} 
	else { 
		$reponse = 'probléme de validation'; 
	}
	


	echo '<html><head><meta http-equiv="refresh" content="3;URL=index.php"><title></title></head><body>' . $reponse . ' Retour en page principal ...</body></htlm>';
?>