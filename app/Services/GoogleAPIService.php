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
        $this->isVendor =  Config::get('kwik.is_vendor') == true ? true : false;
        
        $this->setEmail();
        $this->setPassword();
        $this->setBaseUrl();
        $this->setdomainName();
        $this->setAccessToken();
        $this->setAppAccessToken();
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

    public static function getPlaceTextsearch(){

    }
}