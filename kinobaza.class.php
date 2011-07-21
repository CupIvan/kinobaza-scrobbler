<?php

class kinobaza
{
	private $server            = 'http://api.kinobaza.tv';
	private $request_token_uri = 'http://api.kinobaza.tv/auth/request-token';
	private $authorize_uri     = 'http://api.kinobaza.tv/auth/authorize';
	private $access_token_uri  = 'http://api.kinobaza.tv/auth/access-token';

	/** конструктор */
	function __construct($oAuth_params)
	{
		$oAuth_params = array_merge($oAuth_params, $this->consumerToken());
		$this->oAuth = new oAuth($oAuth_params);
	}
	/** ключ приложения на kinobaza.tv */
	function consumerToken()
	{
		return array(
			'oauth_consumer_key'    => '13938404b29bcf109b5f9f82ba50fe4604e2011ab',
			'oauth_consumer_secret' => '0e5f8a1728fbba4d4fe21a88f6c14697',
		);
	}
	/** получение access токена */
	function auth()
	{
		$oAuth = new oAuth($this->consumerToken());
		echo "Request token: "; flush();
		$token = $oAuth->getRequestToken($this->request_token_uri);
		echo $token['oauth_token']."\n";

		echo "Go to {$this->authorize_uri}?oauth_token=".$token['oauth_token']."&oauth_callback=".urlencode('http://localhost/')."\n";
		echo "And type verifier in back URI: ";
		$f = fopen('php://stdin', 'r'); $verifier = trim(fgets($f)); fclose($f);

		echo "Access token:"; flush();
		$token = $oAuth->getAccessToken($this->access_token_uri, $verifier);
		echo $token['oauth_token']."\n";
		return $token;
	}
	/** поиск по названию фильма */
	function search($query)
	{
		$query = urlencode($query);
		return json_decode($this->oAuth->get($this->server."/films/search?query=$query&limit=5"), true);
	}
	/** поиск по содержимому файла */
	function search_by_file($fname)
	{
		$f     = @fopen($fname, 'r') or die("No such file '$fname'\n");
		$data  = fread($f, 1024*1024); fclose($f);
		$hash  = sha1($data);
		$fname = urlencode($fname);
		return json_decode($this->oAuth->get($this->server."/films/search-by-file?filepath=$fname&hash=$hash"), true);
	}
	/** поиск по идентификатору */
	function search_by_id($params)
	{
		if (!is_array($params)) $params = array('id' => $params);
		$search = '';
		if (isset($params[$x='episode_number'])) { $search = "/episodes/".$params[$x].$search; }
		if (isset($params[$x='season_number']))  { $search = "/seasons/". $params[$x].$search; }
		if (isset($params[$x='series_id']))      { $search = "/films/".   $params[$x].$search; $params['type'] = 'series'; }
		if (!$search && isset($params['id'])) $search = '/films/'.$params['id'];
		if (!$search) return false;
		return $params + json_decode($this->oAuth->get($this->server.$search), true);
	}
	/** отметить фильм */
	function check($params)
	{
		if (is_string($params)) $params = array('id' => $params);
		if (isset($params['series_id']))
		{
			$p = array
			(
				'series_id' => $params['series_id'],
				'season'    => $params['season_number'],
				'episode'   => $params['episode_number'],
				'inclusive' => 0,
			);
			$res = $this->oAuth->post($this->server."/my/series/mark-seen", $p);
			if (!$res) die("oAuth error! Cann't check film!\n");
			return $params + json_decode($res, true);
		}
		else
		{
			$params['status'] = 'seen';
			return $params + json_decode($this->oAuth->post($this->server."/my/films/set-status", $params), true);
		}
	}
}
