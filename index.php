<?php
require 'vendor/autoload.php';

use GuzzleHttp\Client;

class GoogleRequest
{
	/**
	 * the base uri we would be calling its endpoints
	 * @var string
	 */
	protected $base_uri = "https://maps.googleapis.com/maps/api/";
	
	/**
	 * Our google api key used in calling the endpoints
	 * @var string
	 */
	private $api_key = 'AIzaSyAyc5n1BQTKa1FV58sbF0MfmAxzSJJ-1jY';

	/**
	 * The guzzle client instance
	 * @var null
	 */
	protected $client = null;

	/**
	 * [__construct description]
	 */
	public function __construct()
	{
		// setting up the guzzle client instance to the base url
		$this->client = new Client([
							'base_uri' => $this->base_uri,
							'verify' => false
						]);
	}

	/**
	 * [getRequest description]
	 * @param  string $uri [description]
	 * @return [type]      [description]
	 */
	public function getRequest($uri)
	{
		// append the api_key to the end of the uri
		$uri = "{$uri}&key={$this->api_key}";
		// query the endpoint
		$response = $this->client->get($uri);
		//return the result
		return $response->getBody();
	}
}

$google = new GoogleRequest();

$mode = isset($_GET['mode']) ? $_GET['mode'] : 'driving';

$uri = "distancematrix/json?units=metric&origins={$_GET['originLong']},{$_GET['originLat']}&destinations={$_GET['destinationLong']},{$_GET['destinationLat']}&mode={$mode}";

header('Access-Control-Allow-Origin: *');

header('Content-type: application/json');

echo $google->getRequest($uri);