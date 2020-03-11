<?php
	session_start();

	$reponse = 'vous etes déjà enregistrer';
	
	include 'mydb.class.php';
	$db = new MyDB();
	
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
				

				$db->exec('INSERT INTO "users"("id","ip","idsess","email","password","unum","expir") VALUES (NULL,\'' . $_SERVER['REMOTE_ADDR'] . '\',\'' . session_id() . '\',\'' . $login . '\',\'' . $psswd . '\',\'' . $unum . '\',\'' . $expir . '\');');
				//var_dump($result->fetchArray(SQLITE3_ASSOC));
				
				$_SESSION['ciu'] = $unum;
				setcookie('ciu', $unum);
				$reponse = 'Enregistrement reussit !';
			} 
	} 
	
	echo '<html><head><meta http-equiv="refresh" content="3;URL=index.php"><title></title></head><body>' . $reponse . ' Retour en page principal ...</body></htlm>';
?>