<?php
	session_start();	
	$_SESSION['ciu'] = '';
	setcookie('ciu', '');
	echo '<html><head><meta http-equiv="refresh" content="0;URL=index.php"><title></title></head><body>Retour en page principal ...</body></htlm>';
?>