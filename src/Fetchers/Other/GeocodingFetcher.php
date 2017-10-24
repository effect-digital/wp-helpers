<?php
namespace Industrious\WpHelpers\Fetchers\Other;

use Exception;
use TwitterAPIExchange;
use GuzzleHttp\Exception\ClientException;
use Industrious\WpHelpers\Fetchers\TransientFetcher;

/**
 * Class GeocodingFetcher
 */
class GeocodingFetcher extends TransientFetcher
{
    /**
     * Cache/Transient Expiry Length
     * @var
     */
    protected $transient_exipiry = 900; // 15 Minutes

    /**
     * Toggle to Enable/Disable Cache
     * @var
     */
    protected $cache_enabled = TRUE;

    /**
     * Cache/Transient Key
     * @var
     */
    protected $transient_key = 'geocoding_fetcher';

    /**
     * @var \GuzzleHttp\Client
     */
    private $client;

    /**
     * @var string
     */
    private $url = 'https://maps.googleapis.com/maps/api/geocode/json';

    /**
     * @var string
     */
    private $key;

    /**
     * GeocodingFetcher constructor.
     */
    public function __construct()
    {
        $this->setupClient();

        parent::__construct();
    }

    /**
     * Fetch a latitude/longitude by address
     *
     * @param  string $address
     * @return array
     */
    public function fetchOne(string $address)
    {
        return $this->fetchAll([$address]);
    }

    /**
     * Fetch all
     *
     * @param array $args
     * @return array
     */
    public function fetchAll($addresses = [])
    {
        return array_map(function($address)
        {
            $args = ['address' => $address];

            $this->transient_key = $this->getTransientKey() . '_' . md5(json_encode($args));

            return $this->execute($args);
        }, $addresses);
    }

    /**
     * Execute call to Google Maps
     *
     * @param  array  $args [description]
     * @return [type]       [description]
     */
    private function execute($args = [])
    {
        $data = [
            'result' => null,
            'errors' => []
        ];

        $transient = $this->getTransient($this->getTransientKey());

        if ($transient)
        {
            return $transient;
        }

        try
        {
            $response = $this->client->request('GET', $this->url, [
                'query' => [
                    'sensor' => false,
                    'key' => $this->key
                ] + $args
            ]);
        }
        catch (ClientException $e)
        {
            array_push($data['errors'], $e->getMessage());
            return $data;
        }

        if ($response->getStatusCode() !== 200)
        {
            array_push($data['errors'], 'Google Maps Error (Status Code: ' . $response->getStatusCode() . ')');
            return $data;
        }

        $json = json_decode($response->getBody());

        $data['result'] = $this->mapData($json);

        $this->setTransient($data);

        return $data;
    }

    /**
     * Map data into a usable format
     *
     * @param  object   $json
     * @return array
     */
    private function mapData($json)
    {
        $result = $json->results[0];

        return [
            'formatted_address' => $result->formatted_address,
            'geometry' => $result->geometry,
            'address_components' => $result->address_components,
        ];
    }

    /**
     * setupClient
     *
     * @return void
     */
    private function setupClient()
    {
        $this->key = getenv('GOOGLE_MAPS_API_KEY');

        if (!$this->key)
        {
            throw new Exception('No Google Maps API Key found in .env (GOOGLE_MAPS_API_KEY)');
        }

        $this->client = new \GuzzleHttp\Client;
    }

    public function getTransientHookName()
    {
        // TODO: Implement getTransientHookName() method.
    }
}
