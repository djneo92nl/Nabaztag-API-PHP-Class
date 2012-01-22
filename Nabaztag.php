<?php

/**
 * A PHP class to control the functions of a Nabaztag rabbit
 * through the Nabaztag API.
 *
 * Includes support for:
 * - Text to speech
 * - Ear positioning
 * - Wake / sleep
 * - Streaming media
 *
 * Requires PHP5, CURL and a Nabaztag rabbit: http://www.nabaztag.com/
 *
 * Nabaztag API documentation: https://github.com/jcheype/NabAlive/wiki/api
 *
 * @author Dan Ruscoe: http://ruscoe.org/
 *
 * @todo Add support for Nabaztag choreography.
 */

class Nabaztag
{
	// The API URL for all standard actions.
	const API_URL = "http://api.nabaztag.com/vl/FR/api.jsp";

	// The API URL for streaming media actions.
	const STREAMING_API_URL = "http://api.nabaztag.com/vl/FR/api_stream.jsp";

	// API parameter names.
	const API_SERIAL = "sn";
	const API_TOKEN = "token";
	const API_TEXT_TO_SPEECH = "tts";
	const API_EAR_POSITION_LEFT = "posleft";
	const API_EAR_POSITION_RIGHT = "posright";
	const API_ACTION = "action";
	const API_STREAMING_URL_LIST = "urlList";

	// API action codes.
	const ACTION_WAKE = 13;
	const ACTION_SLEEP = 14;

	// Wake status codes.
	const STATUS_WAKE = 1;
	const STATUS_SLEEP = 0;

	private $serial;
	private $token;

	private $leftEarPos;
	private $rightEarPos;

	private $apiParams = array();
	private $streamingApiUrls = array();

	private $lastApiResponse;

	/**
	 * Class constructor.
	 *
	 * @param String $serial - The Nabaztag serial number.
	 * @param Srring $token - The Nabaztag API token.
	 */
	public function __construct($serial, $token)
	{
		$this->serial = $serial;
		$this->token = $token;
	}

	/**
	 * Speaks text input through the rabbit's
	 * text to speech (TTS) feature.
	 *
	 * @parma String $phrase - The phrase to speak.
	 */
	public function speak($phrase)
	{
		$this->apiParams[self::API_TEXT_TO_SPEECH] = $phrase;

		return $this->callApi();
	}

	/**
	 * Rotates the rabbit's ears.
	 * Pass only one value to move a single ear.
	 * Min value: 0, max value: 16
	 *
	 * @param int $left - The new position of the left ear.
	 * @param int $right - The new position of the right ear.
	 */
	public function moveEars($left = null, $right = null)
	{
		if (!is_null($left))
		{
			$this->apiParams[self::API_EAR_POSITION_LEFT] = (int) $left;
		}

		if (!is_null($right))
		{
			$this->apiParams[self::API_EAR_POSITION_RIGHT] = (int) $right;
		}

		return $this->callApi();
	}

	/**
	 * Controls the wake status of the rabbit. Putting it to sleep
	 * will make it inactive.
	 *
	 * @param int $status - The required wake status.
	 * 	Use self::STATUS_WAKE or self::STATUS_SLEEP
	 * @return String - The API response.
	 */
	public function setWakeStatus($status)
	{
		$this->apiParams[self::API_ACTION] = ($status == self::STATUS_WAKE)? self::ACTION_WAKE : self::ACTION_SLEEP;

		return $this->callApi();
	}

	/**
	 * Streams a streaming media URL to the Nabaztag.
	 *
	 * @param String $url - The URL of the streaming media.
	 *	May be an MP3 or online radio station.
	 * @return String - The API response. 
	 */
	public function streamUrl($url)
	{
		$this->streamingApiUrls = array($url);

		return $this->callStreamingApi();
	}

	/**
	 * Streams multiple streaming media URL to the Nabaztag.
	 *
	 * @param array $urls - An array of streaming media URLs.
	 * @return String - The API response. 
	 */
	public function streamMultipleUrls($urls)
	{
		if (is_array($urls))
		{
			$this->streamingApiUrls = $urls;
		}

		return $this->callStreamingApi();
	}

	/**
	 * Uses an array of parameters to build a complete
	 * Nabaztag API URL.
	 *
	 * @return String - The API URL.
	 */
	private function buildApiUrl()
	{
		$url = self::API_URL;
		$url .= "?" . self::API_SERIAL . "=" . $this->serial;
		$url .= "&" . self::API_TOKEN . "=" . $this->token;

		foreach ($this->apiParams as $key => $value)
		{
			$url .= "&" . $key . "=" . urlencode($value);
		}

		return $url;
	}

	/**
	 * Uses an array of streaming URLs to build a complete
	 * Nabaztag streaming API URL.
	 *
	 * @return String - The streaming API URL.
	 */
	private function buildStreamingApiUrl()
	{
		$url = self::STREAMING_API_URL;
		$url .= "?" . self::API_SERIAL . "=" . $this->serial;
		$url .= "&" . self::API_TOKEN . "=" . $this->token;

		$url .= "&" . self::API_STREAMING_URL_LIST . "=" . implode("|", $this->streamingApiUrls);

		return $url;
	}

	/**
	 * Makes a call to the standard Nabaztag API URL.
	 *
	 * @return String - The API response.
	 */
	private function callApi()
	{
		return $this->callApiUrl($this->buildApiUrl());
	}

	/**
	 * Makes a call to the streaming Nabaztag API URL.
	 *
	 * @return String - The API response.
	 */
	private function callStreamingApi()
	{
		return $this->callApiUrl($this->buildStreamingApiUrl());
	}

	/**
	 * Uses CURL to make the call to a Nabaztag API URL.
	 * The API response is stored in the variable $lastApiResponse.
	 *
	 * @param String $url - The API URL to call.
	 * @return String - The API response.
	 */
	private function callApiUrl($url)
	{
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

		$result = curl_exec($curl);
		curl_close($curl);

		if (!$result)
		{
			return null;
		}

		$this->lastApiResponse = $result;

		return $this->lastApiResponse;
	}

	/**
	 * Re-sends the last API call made by the class.
	 *
	 * @return String - The API response.
	 */
	public function replay()
	{
		return $this->callApi();
	}

	public function getApiParams()
	{
		return $this->apiParams;
	}

	public function getStreamingApiUrls()
	{
		return $this->streamingApiUrls;
	}

	public function getLastApiResponse()
	{
		return $this->lastApiResponse;
	}
}

?>
