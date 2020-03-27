<?php
	session_start();
	


	include 'myip.class.php';
	$dbip = new MyIP();
	
		$resultx = $dbip->query('SELECT * FROM "ip" WHERE "ip" LIKE \'%' . $_SERVER['REMOTE_ADDR'] . '%\' ESCAPE \'\\\' ORDER BY "id" ASC LIMIT 0, 49999;');
		$valx = $resultx->fetchArray(SQLITE3_ASSOC);	
	
	
	include 'mydb.class.php';
	$db = new MyDB();
	
	$results = $db->query('SELECT * FROM "validate" WHERE "id" ORDER BY "id" ASC LIMIT 0, 49999');
	while ($row = $results->fetchArray(SQLITE3_ASSOC)) {
		if($row['expire'] < time()) {
			$db->exec('DELETE FROM "users" WHERE "id" LIKE \'%' . $row['id'] . '%\'');	
			$db->exec('DELETE FROM "validate" WHERE "id" LIKE \'%' . $row['id'] . '%\'');
		}
	}
	
	$login = filter_input(INPUT_POST, 'login', FILTER_SANITIZE_EMAIL);
	$psswd = filter_input(INPUT_POST, 'psswd', FILTER_SANITIZE_STRING);	
	
	$result = $db->query('SELECT * FROM "users" WHERE "email" LIKE \'%' . $login . '%\' ESCAPE \'\\\' AND "password" LIKE \'%' . $psswd . '%\' ESCAPE \'\\\' ORDER BY "id" ASC LIMIT 0, 49999;');
	$val = $result->fetchArray(SQLITE3_ASSOC);	
	
	if( md5($val['email'] . $val['password']) == md5($login . $psswd) && $val['valid'] == '1' ) {
		
		$unum = $val['unum'];
		
		if($val['expir'] < time()) {
			
			$expir = time() + (3600*24);
			$unum  = hash('sha256', session_id() . random_bytes(5) . $login . $psswd . $expir );
			
			
			$db->exec('UPDATE "users" SET "idsess"="' . session_id() . '" , "ip"="' . $_SERVER['REMOTE_ADDR'] . '" ,    "unum"="' . $unum . '" ,  "expir"="' . $expir  . '" WHERE "id"=\'' . $val['id'] . '\'');
			
			
			}
		
		
		if($valx['ip'] == $_SERVER['REMOTE_ADDR']) {
			$dbip->exec('UPDATE "ip" SET "inc"="1" ,    "date"="' . time() . '"  WHERE "id"=\'' . $valx['id'] . '\'');
		} 
		else {
			$dbip->exec('INSERT INTO "ip"("id","inc","date","ip") VALUES (NULL,\'1\',\'' . time() . '\',\'' . $_SERVER['REMOTE_ADDR'] . '\');');
		}
		
		$_SESSION['ciu'] = $unum;
		setcookie('ciu', $unum);
		
		$reponse = 'identification réussite'; 
	} 
	else { 
	
		if($valx['ip'] == $_SERVER['REMOTE_ADDR']) {
			
			if(intval($valx['inc']) >= 10) {
				$ztime = time() + (60 * 10);
			} else { $ztime = time(); }
			
			$dbip->exec('UPDATE "ip" SET "inc"="' .  intval($valx['inc'] + 1) . '" ,    "date"="' . $ztime . '"  WHERE "id"=\'' . $valx['id'] . '\'');
			
		} 
		else {
			$dbip->exec('INSERT INTO "ip"("id","inc","date","ip") VALUES (NULL,\'1\',\'' . time() . '\',\'' . $_SERVER['REMOTE_ADDR'] . '\');');
		}
	
		$reponse = 'probléme d\'identification'; 
	}
	


	echo '<html><head><meta http-equiv="refresh" content="3;URL=index.php"><title></title></head><body>' . $reponse . ' Retour en page principal ...</body></htlm>';
?>