<?php

namespace Src\Utils;

use Exception;
use Src\Config\Config;
use Predis\Client as RedisClient;

class Auth{
	private array $config;
	private array $postData;
	private false|\CurlHandle $ch;
	private static ?Auth $instance = null;
	private string $accessToken;
	private string $refreshToken;
	private \DateTime $accessTokenExpiry;
	private RedisClient $redis;

	/**
	 * Private constructor to prevent direct instantiation
	 */
	private function __construct(){
		$this->config = Config::getInstance()->get('auth');
		$this->ch = curl_init();
		$this->resetPostData();
		$this->accessToken = '';
		$this->refreshToken = '';
		$this->accessTokenExpiry = new \DateTime('now');
		$this->redis = new RedisClient();
		$this->loadTokensFromRedis();
	}

	/**
	 * Private clone method to prevent cloning
	 */
	private function __clone(){}

	/**
	 * Get the singleton instance of Auth
	 *
	 * @return Auth
	 */
	public static function getInstance():Auth{
		if(self::$instance === null){
			self::$instance = new Auth();
		}
		return self::$instance;
	}

	/**
	 * Reset POST data with client credentials
	 */
	private function resetPostData():void{
		$this->postData = [
			'client_id' => $this->config['client_id'],
			'client_secret' => $this->config['client_secret']
		];
	}

	/**
	 * Set up cURL and execute the request
	 *
	 * @throws Exception
	 */
	private function authenticate():void{
		curl_setopt($this->ch, CURLOPT_URL, $this->config['token_endpoint']);
		curl_setopt($this->ch, CURLOPT_POST, true);
		curl_setopt($this->ch, CURLOPT_POSTFIELDS, http_build_query($this->postData));
		curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, true);
		$response = curl_exec($this->ch);
		$httpCode = curl_getinfo($this->ch, CURLINFO_HTTP_CODE);
		curl_close($this->ch);
		if($httpCode != 200){
			throw new Exception("Error fetching access token: ".$httpCode." - ".$response);
		}
		$data = json_decode($response);
		$this->accessToken = $data->access_token ?? '';
		$this->refreshToken = $data->refresh_token ?? '';
		$this->accessTokenExpiry = new \DateTime('now +'.($data->expires_in ?? 0).' seconds');
		$this->saveTokensToRedis();
	}

	/**
	 * Get access token using username and password
	 *
	 * @throws Exception
	 */
	private function getAccessTokenByUsernameAndPassword():void{
		$this->resetPostData();
		$this->postData['grant_type'] = 'password';
		$this->postData['username'] = $this->config['username'];
		$this->postData['password'] = $this->config['password'];
		$this->authenticate();
	}

	/**
	 * Get access token using a refresh token
	 *
	 * @throws Exception
	 */
	private function getAccessTokenWithRefreshToken():bool{
		if(empty($this->getRefreshToken())){
			return false;
		}
		try{
			$this->resetPostData();
			$this->postData['grant_type'] = 'refresh_token';
			$this->postData['refresh_token'] = $this->getRefreshToken();
			$this->postData['scope'] = $this->config['scope'];
			$this->authenticate();
		}catch(Exception $e){
			$this->refreshToken = '';
			echo 'Error: '.$e->getMessage().PHP_EOL;
			return false;
		}
		return true;
	}

	public function getAccessToken():string{
		return $this->accessToken;
	}

	public function getRefreshToken():string{
		return $this->refreshToken;
	}

	private function isAccessTokenExpired():bool{
		return new \DateTime() > $this->accessTokenExpiry ?? true;
	}

	/**
	 * Authenticate and get access token
	 * @throws Exception
	 */
	public function Authentication():void{
		if($this->isAccessTokenExpired()){
			if($this->getAccessTokenWithRefreshToken()){
				return;
			}else{
				try{
					$this->getAccessTokenByUsernameAndPassword();
				}catch(Exception $e){
					echo 'Error: '.$e->getMessage().PHP_EOL;
				}
			}
		}
	}

	private function saveTokensToRedis():void{
		$this->redis->set('access_token', $this->getAccessToken());
		$this->redis->set('refresh_token', $this->getRefreshToken());
		$this->redis->set('access_token_expiry', $this->accessTokenExpiry->format('Y-m-d H:i:s'));
	}

	private function loadTokensFromRedis():void{
		$this->accessToken = $this->redis->get('access_token') ?? '';
		$this->refreshToken = $this->redis->get('refresh_token') ?? '';
		try{
			$this->accessTokenExpiry = new \DateTime($this->redis->get('access_token_expiry') ?? 'now');
		}catch(Exception $e){
			echo 'Error: '.$e->getMessage().PHP_EOL;
		}
	}

}
