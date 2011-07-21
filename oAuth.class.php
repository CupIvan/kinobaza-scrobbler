<?php

class oAuth
{
	private $params = array('oauth_signature_method' => 'PLAINTEXT');

	/** конструктор */
	function __construct($params = array())
	{
		$this->params = array_merge($this->params, $params);
	}
	/** получение параметра */
	function __get($x)
	{
		return isset($this->params[$x]) ? $this->params[$x] : '';
	}
	/** временный токен */
	function getRequestToken($uri)
	{
		$context = stream_context_create($this->getOpts($uri));
		parse_str(file_get_contents($uri, false, $context), $data);
		$this->params = array_merge($this->params, $data);
		return $data;
	}
	/** настоящий токен */
	function getAccessToken($uri, $verifier)
	{
		$params['verifier'] = $verifier;
		$context = stream_context_create($this->getOpts($uri));
		parse_str(file_get_contents($uri, false, $context), $data);
		$this->params = array_merge($this->params, $data);
		return $data;
	}
	/** получение страницы */
	public function get($uri)
	{
		$context = stream_context_create($this->getOpts($uri, 'GET'));
		return file_get_contents($uri, false, $context);
	}
	/** отправка данных */
	public function post($uri, $data = '')
	{
		$context = stream_context_create($this->getOpts($uri, 'POST', $data));
		return @file_get_contents($uri, false, $context);
	}
	/** генерация заголовков */
	function getOpts($uri, $method = 'POST', $data = '')
	{
		if (is_array($data)) $data = http_build_query($data);
		$data_len = strlen($data);
		return array
		(
			'http' => array
			(
				'method' => $method,
				'header' =>
					"Connection: close\r\n".
					"Content-Type: application/x-www-form-urlencoded\r\n".
					'Authorization: OAuth '.
					'realm="'.$uri.'",'.
					'oauth_consumer_key="'.$this->oauth_consumer_key.'",'.
					'oauth_signature_method="'.$this->oauth_signature_method.'",'.
					'oauth_signature="'.urlencode($this->oauth_consumer_secret.'&'.$this->oauth_token_secret).'",'.
					(!$this->oauth_token?'':'oauth_token="'.$this->oauth_token.'",').
					(!$this->verifier?'':'oauth_verifier="'.$this->verifier.'",').
					'oauth_nonce="'.uniqid('').'",'.
					'oauth_timestamp="'.time().'",'.
					'oauth_version="1.0"',
				'content' => $data,
			)
		);
	}
}
