<?php
	session_start();

	$reponse = 'vous etes déjà enregistrer';
	
	include 'mydb.class.php';
	$db = new MyDB();
	
	$results = $db->query('SELECT * FROM "validate" WHERE "id" ORDER BY "id" ASC LIMIT 0, 49999');
	while ($row = $results->fetchArray(SQLITE3_ASSOC)) {
		if($row['expire'] < time()) {
			$db->exec('DELETE FROM "users" WHERE "id" LIKE \'%' . $row['id'] . '%\'');	
			$db->exec('DELETE FROM "validate" WHERE "id" LIKE \'%' . $row['id'] . '%\'');
		}
	}	
	
	
	$result = $db->query('SELECT "ip" FROM "users" WHERE "ip" LIKE \'' .  $_SERVER['REMOTE_ADDR'] . '\' ESCAPE \'\\\' ORDER BY "id" ASC LIMIT 0, 49999;');
	$val = $result->fetchArray(SQLITE3_ASSOC);	
	
	if($val['ip'] == $_SERVER['REMOTE_ADDR']) { 
		$reponse = 'vous vous etes déjà enregistrer plusieurs fois !';
	}
	else {
	
		$login = filter_input(INPUT_POST, 'login', FILTER_SANITIZE_EMAIL);
		$psswd = filter_input(INPUT_POST, 'psswd', FILTER_SANITIZE_STRING);
	
		$expir = time() + (3600*24);
		$unum  = hash('sha256', session_id() . random_bytes(5) . $login . $psswd . $expir );
		
		$result = $db->query('SELECT "email" FROM "users" WHERE "email" LIKE \'' .  $login . '\' ESCAPE \'\\\' ORDER BY "id" ASC LIMIT 0, 49999;');
		$val = $result->fetchArray(SQLITE3_ASSOC);	
		
			if($val['email'] != $login) {
				
				
				
				$db->exec('INSERT INTO "users"("id","ip","idsess","email","password","unum","expir","valid") VALUES (NULL,\'' . $_SERVER['REMOTE_ADDR'] . '\',\'' . session_id() . '\',\'' . $login . '\',\'' . $psswd . '\',\'' . $unum . '\',\'' . $expir . '\',\'1\');');
				$getid = $db->lastInsertRowid();
				$db->exec('INSERT INTO "validate"("id","key","expire") VALUES (\'' . $getid . '\',\'' . $unum . '\',\'' . (time() + (60*5)) . '\');');
				//var_dump($result->fetchArray(SQLITE3_ASSOC));
				
				$_SESSION['ciu'] = $unum;
				setcookie('ciu', $unum);
				$reponse = 'Enregistrement reussit !';
			} 
	} 
	
	echo '<html><head><meta http-equiv="refresh" content="6;URL=index.php"><title></title></head><body>' . $reponse . ' Retour en page principal ...</body></htlm>';
?>