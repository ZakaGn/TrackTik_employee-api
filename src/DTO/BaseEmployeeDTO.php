<?php

namespace Src\DTO;

/**
 * Class BaseEmployeeDTO
 *
 * This class is the base class for all employee DTOs.
 * It contains the common attributes and methods that all employee DTOs should have.
 *
 * @package Src\DTO
 */
abstract class BaseEmployeeDTO{
	protected int $id = 0;
	protected string $firstName;
	protected string $lastName;

	/**
	 * BaseEmployeeDTO constructor.
	 *
	 * @param string $firstName
	 * @param string $lastName
	 */
	public function __construct(string $firstName, string $lastName){
		$this->setFirstName($firstName);
		$this->setLastName($lastName);
	}

	/**
	 * Getter for the ID attribute.
	 *
	 * @return int
	 */
	public function getId():int{
		return $this->id;
	}

	/**
	 * Getter for the first name attribute.
	 *
	 * @return string
	 */
	public function getFirstName():string{
		return $this->firstName;
	}

	/**
	 * Getter for the last name attribute.
	 *
	 * @return string
	 */
	public function getLastName():string{
		return $this->lastName;
	}

	/**
	 * Setter for the ID attribute.
	 *
	 * @param int $id
	 */
	public function setId(int $id):void{
		if($id < 1){
			throw new \InvalidArgumentException('ID must be a positive integer');
		}
		$this->id = $id;
	}

	/**
	 * Setter for the first name attribute.
	 *
	 * @param string $firstName
	 */
	public function setFirstName(string $firstName):void{
		if(strlen($firstName) < 2){
			throw new \InvalidArgumentException('First name must be at least 2 characters long');
		}
		if(strlen($firstName) > 50){
			throw new \InvalidArgumentException('First name must be at most 50 characters long');
		}
		$this->firstName = $firstName;
	}

	/**
	 * Setter for the last name attribute.
	 *
	 * @param string $lastName
	 */
	public function setLastName(string $lastName):void{
		if(strlen($lastName) < 2){
			throw new \InvalidArgumentException('Last name must be at least 2 characters long');
		}
		if(strlen($lastName) > 50){
			throw new \InvalidArgumentException('Last name must be at most 50 characters long');
		}
		$this->lastName = $lastName;
	}

	/**
	 * Abstract method to map the employee data to TrackTik's schema.
	 *
	 */
	abstract public static function canHandle(array $data):bool;

	public function getFullName():string{
		return $this->getFirstName() . ' ' . $this->getLastName();
	}

}
