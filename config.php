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
	// сериал "Интерны"
	if (match($fname, '(интерн|intern).+?(\d+)', $m))
	{
		$episode = $m[2];
		return array(
			'series_id'      => 1003752,
			'season_number'  => floor(($episode - 1) / 20) + 1,
			'episode_number' => ($episode - 1) % 20 + 1,
		);
	}
}
