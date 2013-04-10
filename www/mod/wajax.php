<?php

class Ajax
{
	private $show_errors = false;
	
	private $errors = array();
	
	private function getHeader($header)
	{
		// Try to get it from the $_SERVER array first
		$temp = 'HTTP_' . strtoupper(str_replace('-', '_', $header));
		if (isset($_SERVER[$temp])) {
			return $_SERVER[$temp];
		}
	
		// This seems to be the only way to get the Authorization header on Apache
		if (function_exists('apache_request_headers')) {
			$headers = apache_request_headers();
			if (isset($headers[$header])) {
				return $headers[$header];
			}
			$header = strtolower($header);
			foreach ($headers as $key => $value) {
				if (strtolower($key) == $header) {
					return $value;
				}
			}
		}
	
		return false;		
	}
	
	private function isXmlHttpRequest()
	{
		return (self::getHeader('X_REQUESTED_WITH') == 'XMLHttpRequest');
	}
	
	public function processing()
	{
		if (!$_POST && !isset($_POST['function'])){
			return;
		}
		$data = $_POST;
		
		if (stripos($_POST['function'], '::') !== false){
			$function = explode('::', $_POST['function']);
		}elseif(stripos($_POST['function'], '->') !== false){
			$function = explode('->', $_POST['function']);
			$function[0] = new $function[0];
		}else{
			$function = $_POST['function'];
		}
		unset($data['function']);
		header('content-type: text/html; charset="windows-1251"');
		echo call_user_func($function, $data);
		exit();
	}
}
Ajax::processing();

