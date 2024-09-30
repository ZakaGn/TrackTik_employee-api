<?php

namespace Src\Mapper;

use Src\DTO\BaseEmployeeDTO;
use Src\Factory\ProviderRegistry;
use Exception;
use ReflectionClass;

class DTOMapper{
	/**
	 * Maps the raw data to the appropriate EmployeeDTO using the provider registry.
	 *
	 * @param array $data
	 * @return BaseEmployeeDTO
	 * @throws Exception
	 */
	public static function mapToDTO(array $data):BaseEmployeeDTO{
		ProviderRegistry::initialize();
		foreach(ProviderRegistry::getProviders() as $providerClass){
			$reflection = new ReflectionClass($providerClass);
			if($reflection->hasMethod('canHandle') && $reflection->getMethod('canHandle')->invoke(null, $data)){
				$constructor = $reflection->getConstructor();
				if(!$constructor){
					throw new Exception('No constructor found in '.$providerClass);
				}
				$params = $constructor->getParameters();
				$args = [];
				foreach($params as $param){
					$paramName = $param->getName();
					if(isset($data[$paramName])){
						$args[] = $data[$paramName];
					}elseif($param->isDefaultValueAvailable()){
						$args[] = $param->getDefaultValue();
					}else{
						throw new Exception('Missing required parameter: '.$paramName);
					}
				}
				return $reflection->newInstanceArgs($args);
			}
		}
		throw new Exception('Unable to determine the provider based on the provided data');
	}

	/**
	 * Converts a DTO to an associative array.
	 *
	 * @param BaseEmployeeDTO $employeeDTO
	 * @return array
	 */
	public static function toTrackTikSchema(BaseEmployeeDTO $employeeDTO):array{
		$result = [];
		$reflectionClass = new ReflectionClass($employeeDTO);
		foreach($reflectionClass->getProperties() as $property){
			$propertyName = $property->getName();
			$getterMethod = 'get'.ucfirst($propertyName);
			if(method_exists($employeeDTO, $getterMethod) && is_callable([$employeeDTO, $getterMethod])){
				$result[$propertyName] = $employeeDTO->$getterMethod();
			}
		}
		return $result;
	}

}
