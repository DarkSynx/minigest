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
				
				//$db->exec('INSERT INTO "users"("id","ip","idsess","email","password","unum","expir","valid") VALUES (NULL,\'' . $_SERVER['REMOTE_ADDR'] . '\',\'' . session_id() . '\',\'' . $login . '\',\'' . $psswd . '\',\'' . $unum . '\',\'' . $expir . '\',\'0\');');
				//$getid = $db->lastInsertRowid();
				//$db->exec('INSERT INTO "validate"("id","key","expire") VALUES (\'' . $getid . '\',\'' . $valgen  . '\',\'' . (time() + (60*5)) . '\');');
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
				setcookie('ciu', $unum);
				$reponse = $_SESSION['vlg'] . '<br/> Enregistrement presque fini !';
				
				
				echo '<html><head><title></title></head><body>' . $reponse . ' <br/><br/><form action="./validation.php" method="post"><hr/><img src="img.php" /><br/>'. $valgen[1] .'<hr/><br/><input type="text" name="val" value="" placeholder="Votre code obtenu par mail"/><input type="submit" value="validation"/></form></body></htlm>'; 
				
				exit();
				
			} 
			
	}
		
	echo '<html><head><meta http-equiv="refresh" content="6;URL=index.php"><title></title></head><body>' . $reponse . ' Retour en page principal ...</body></htlm>'; 



function rdc($gen='',$gen2='') {

for($a=0; $a<5; $a++) {
$g = chr(rand(65,90));
$gen .= $g;
$gen2 .= '<audio controls style="width:48px;margin:5px;"><source  src="./son/'.strtolower($g).'.mp3" type="audio/mpeg"/></audio>';
}



return [$gen,$gen2];
}





?>