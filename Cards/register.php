<?php
	session_start();
	sleep(1);
	
	$login = filter_input(INPUT_POST, 'login', FILTER_SANITIZE_EMAIL);
	$psswd = filter_input(INPUT_POST, 'psswd', FILTER_SANITIZE_STRING);
	
	include 'mydb.class.php';
	$db = new MyDB();
	
	$expir = time() + (3600*24);
	$unum  = hash('sha256', session_id() . random_bytes(5) . $login . $psswd . $expir );
	
	$result = $db->query('SELECT "email" FROM "users" WHERE "email" LIKE \'' .  $login . '\' ESCAPE \'\\\' ORDER BY "id" ASC LIMIT 0, 49999;');
	$val = $result->fetchArray(SQLITE3_ASSOC);	
	
	if($val['email'] != $login) {

	
		$db->exec('INSERT INTO "users"("id","idsess","email","password","unum","expir") VALUES (NULL,\'' . session_id() . '\',\'' . $login . '\',\'' . $psswd . '\',\'' . $unum . '\',\'' . $expir . '\');');
		//var_dump($result->fetchArray(SQLITE3_ASSOC));
		
		$_SESSION['ciu'] = $unum;
		setcookie('ciu', $unum);
		$reponse = 'Enregistrement reussit !';
	}
	else { 
		$reponse = 'vous etes déjà enregistrer'; 
	}
	
	echo '<html><head><meta http-equiv="refresh" content="3;URL=index.php"><title></title></head><body>' . $reponse . ' Retour en page principal ...</body></htlm>';
?>