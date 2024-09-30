<?php

namespace Src\Config;

class Config{
	private static ?Config $instance = null;
	private array $config;

	private function __construct(){
		$this->config = include(ROOT_DIR.'/config/config.php');
		$this->cors();
	}

	public static function getInstance():Config{
		if(self::$instance === null){
			self::$instance = new Config();
		}
		return self::$instance;
	}

	public function get(string $key, $default = null){
		$keys = explode('.', $key);
		$value = $this->config;
		foreach($keys as $k){
			if(isset($value[$k])){
				$value = $value[$k];
			}else{
				return $default;
			}
		}
		return $value;
	}

	private function cors():void{
		header('Access-Control-Allow-Origin: *');
		header('Content-Type: application/json');
		header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
		header('Access-Control-Allow-Headers: Content-Type, Authorization');
	}

}
