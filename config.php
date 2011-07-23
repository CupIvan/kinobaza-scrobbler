<?php

// ACCESS TOKEN
// autoupdated at 16:35:54 21.07.11
$oAuth_token = array(
	'oauth_token'        => '',
	'oauth_token_secret' => '',
);

/** пользовательская обработка имени файла */
function parseFileName($fname)
{
	$res = array();
	// какой-либо сериал
	if (match($fname, 's(\d+)e(\d+)', $m))
	{
		$res['season_number']  = $m[1];
		$res['episode_number'] = $m[2];
	}
	// сериал "Интерны"
	if (match($fname, '(интерн|interny).+?(\d+)?', $m))
	{
		$res['series_id'] = 1003752;
		if (isset($m[2]))
		{
			$episode = $m[2];
			$res['season_number']  = floor(($episode - 1) / 20) + 1;
			$res['episode_number'] = ($episode - 1) % 20 + 1;
		}
	}
	// сериал "Зайцев + 1"
	if (match($fname, '(zaicev|зайцев).{3,}(\d+)?', $m))
	{
		$res['series_id']      = 1642278;
		$res['season_number']  = 1;
		$res['episode_number'] = $m[2];
	}
	return $res;
}
