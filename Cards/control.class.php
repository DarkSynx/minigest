<?php
class control {
	
	private $_head = '';
	private $_body = '';
	
	private $_db;

	public function __construct($id, &$db) {
		//$this->_db = &$db;
		
		
		$nolog = true;
		echo '['.$_SESSION['ciu'].']' . '<br/>';
		echo '>'.$_COOKIE['ciu'].'<' . '<br/>';
		
			if(isset($_SESSION['ciu'])){

				$result = $db->query('SELECT id,idsess,unum FROM "users" WHERE "idsess" LIKE \'%' . $id . '%\' ESCAPE  \'\\\' AND "unum" LIKE \'%' . $_SESSION['ciu'] . '%\' ESCAPE \'\\\' ORDER BY "id" DESC LIMIT 0, 49999;');
				$val = $result->fetchArray(SQLITE3_ASSOC);			
				//var_dump($val);
				
				if($val['idsess'] == $id && $val['unum'] == $_SESSION['ciu'] && $_SESSION['ciu'] == $_COOKIE['ciu'] ) {
					$nolog = false;
					$this->loadpage('page de salon',['salon']);
				}
			}
			
			
			if( $nolog ) {
				$this->loadpage('page de login',['login','register']);
			}
		
	}
	
	private function loadpage($name,$mypagex=[],$style='') {
		$link = ['jquery-3.4.1.min.js','jadc.js']; 
		$this->head($name,$link,$style);
		
		foreach($mypagex as $mypage) {
			switch($mypage) {
				case 'login' :
					$this->_body .= file_get_contents('html/login.html');
				break;
				case 'register':
					$this->_body .= file_get_contents('html/register.html');
				break;
				case 'salon';
					$this->_body .= file_get_contents('html/salon.html');
				break;
		}}
		$this->page();
	}
	
	private function head($name='',$link=[],$style='',$links='') {
		foreach($link as $l){ $links .= "<script type=\"text/javascript\" src=\"js/$l\"></script>\r\n";}
		$this->_head .= "<title>$name</title>$links<style>$style</style>";
	}
	

	
	
	/*------------------------------------------------------*/
	
	private function page() {
		echo '<html><head>' . $this->_head . '</head><body>' . $this->_body . '</body></html>';
	}


	
	
}
?>