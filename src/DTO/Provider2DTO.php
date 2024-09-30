<?php

namespace Src\DTO;

class Provider2DTO extends BaseEmployeeDTO{
	private string $username;

	public function __construct($firstName, $lastName, $username){
		parent::__construct($firstName, $lastName);
		$this->setUsername($username);
	}

	public function getUsername():string{
		return $this->username;
	}

	public function setUsername(string $username):void{
		$this->username = $username;
	}

	public static function canHandle(array $data):bool{
		return isset($data['username']) && isset($data['firstName']) && isset($data['lastName']) && count($data) === 3;
	}

}
