<?php 

namespace App\Services; 

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;

class GoogleAPIService {
   
    const PLACES_API_BASE_URL="https://maps.googleapis.com/maps/api/place";

    /**
     * Instance of Client
     * 
     * @var \GuzzleHttp\Client
     */
    protected Client $client;


    /**
     * Create an instance of kwik
     */
    public function __construct()
    {
        $this->setClient();
    }

     /**
     * Set options for making the Client request
     * 
     * @param array<string,string> $options 
     */
    private function setClient(array $options=[]): void
    {
        $this->client = new Client(
            [
                'base_uri' => array_key_exists('base_uri', $options) ? $options['base_uri'] : $this->baseUrl,
                'headers' => [
                    'Content-Type'  => 'application/json',
                    'Accept'        => 'application/json'
                ]
            ]
        );
    }

     /**
     * @param string $relativeUrl
     * @param string $method
     * @param array<string, string> $body
     * @return Kwik
     * @throws Exception
     */
    private function httpRequest(String $method, String $relativeUrl,  array $body = [])
    {
        if (is_null($method)) {
            throw new Exception("Empty method not allowed");
        }
        $params = [];
        $key = env('GOOGLE_API_KEY');
        if(!is_null($key) && empty($params['key']?? null))
        {
            $params = [
                'key' => $key
            ];
    
        }
     
        $request_data = [
            "body" => json_encode($body),
            'query' => $params
        ];
        
        $this->response = $this->client->{strtolower($method)}(
            $this->baseUrl.$relativeUrl, $request_data
        );

        return $this;
    }

    /**
     * Place search via text 
     */
    public static function getPlaceTextsearch(string $address){

    }
}