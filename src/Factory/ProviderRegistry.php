<?php

namespace Src\Factory;

use Src\DTO\Provider1DTO;
use Src\DTO\Provider2DTO;

class ProviderRegistry{
	private static array $providers = [];

	public static function initialize():void{
		if(empty(self::$providers)){
			self::$providers = [
				'provider1' => Provider1DTO::class,
				'provider2' => Provider2DTO::class
			];
		}
	}

	/**
	 * Get the list of registered providers.
	 *
	 * @return array
	 */
	public static function getProviders():array{
		return self::$providers;
	}

	/**
	 * Get the class name for a specific provider key.
	 *
	 * @param string $provider
	 * @return string|null
	 */
	public static function getProviderClass(string $provider):?string{
		return self::$providers[$provider] ?? null;
	}
}
