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
				
				$valgen = rdc();
				
				$db->exec('INSERT INTO "users"("id","ip","idsess","email","password","unum","expir","valid") VALUES (NULL,\'' . $_SERVER['REMOTE_ADDR'] . '\',\'' . session_id() . '\',\'' . $login . '\',\'' . $psswd . '\',\'' . $unum . '\',\'' . $expir . '\',\'0\');');
				$getid = $db->lastInsertRowid();
				$db->exec('INSERT INTO "validate"("id","key","expire") VALUES (\'' . $getid . '\',\'' . $valgen[0] . '\',\'' . (time() + (60*5)) . '\');');
				//var_dump($result->fetchArray(SQLITE3_ASSOC));
				
				
					/*
					$subject = 'INSCRIPTION';
					$message = 'pour valider votre inscription copier ce code <br/>' . PHP_EOL .$valgen;
					$headers = array(
						'From' => 'webmaster@example.com',
						'Reply-To' => 'webmaster@example.com',
						'X-Mailer' => 'PHP/' . phpversion()
					);

					mail($login, $subject, $message, $headers);
					*/

				$_SESSION['vlg'] = $valgen[0];
				$_SESSION['ciu'] = $unum;
				
				$reponse = '<br/> Enregistrement presque fini !';
				
				
				echo str_ireplace('{{valgen}}',$valgen[1],file_get_contents('html/validation.html')); 
				
				exit();
				
			} 
			
	}
		
	echo str_ireplace('{{reponse}}',$reponse,file_get_contents('html/reponse.html')); 



function rdc($gen='',$gen2='') {
	for($a=0; $a<5; $a++) {
		$oo = rand(65,90);
		if($oo%2 == 0) { $ooo = $oo . '1'; } else { $ooo = $oo . '0'; }
		$gen .= chr($oo);
		$gen2 .= '<audio controls style="width:48px;margin:5px;"><source  src="./son/' . $ooo . '.mp3" type="audio/mpeg"/></audio>';
	}

return [$gen,$gen2];
}





?>