<?php

namespace Src\DTO;

class Provider1DTO extends BaseEmployeeDTO{
	private string $email;

	public function __construct($firstName, $lastName, $email){
		parent::__construct($firstName, $lastName);
		$this->setEmail($email);
	}

	public function getEmail():string{
		return $this->email;
	}

	public function setEmail(string $email):void{
		if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
			throw new \InvalidArgumentException('Invalid email address');
		}
		$this->email = $email;
	}

	public static function canHandle(array $data):bool{
		return isset($data['email']) && isset($data['firstName']) && isset($data['lastName']) && count($data) === 3;
	}

}
