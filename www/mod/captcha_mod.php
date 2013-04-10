<?
class captcha
{
	public $return;	
	function createCaptcha(){
		$rand = rand(1000, 9999);
		$_SESSION["captcha"]=$rand;
		$result = $_SESSION["captcha"]*48;
		return $result;
	}
	
	function checkCaptcha($return){
		if($_SESSION["captcha"] == ($return/48)){return true;}
		else{return false;}
	}
}
?>