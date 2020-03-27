<?php
class MyIP extends SQLite3{
	function __construct(){
		$this->open('administration/ipban.db');
	}
}
?>