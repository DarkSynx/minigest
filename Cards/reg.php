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
	
	setcookie('ctr', 'register');
	
	echo '<html><head><meta http-equiv="refresh" content="0;URL=index.php"><title></title></head><body>Retour en page principal ...</body></htlm>';

?>