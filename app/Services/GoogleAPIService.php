<?php 

namespace App\Services; 

use ArgumentCountError;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;

class GoogleAPIService {
   
    const MAP_API_BASE_URL="https://maps.googleapis.com/maps/api";

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
                'base_uri' => array_key_exists('base_uri', $options) ? $options['base_uri'] : self::MAP_API_BASE_URL,
                'headers' => [
                    // 'Content-Type'  => 'application/json',
                    // 'Accept'        => 'application/json'
                ]
            ]
        );
    }

     /**
     * @param string $relativeUrl
     * @param string $method
     * @param array<string, string> $body
     * @return \App\Sservice\GoogleAPIService
     * @throws Exception
     */
    private function httpRequest(String $method, String $relativeUrl, array $body = [], array $params=[])
    {
        if (is_null($method)) {
            throw new Exception("Empty method not allowed");
        }
        $key = env('GOOGLE_API_KEY');
        if(!is_null($key) && empty($params['key']?? null))
        {
            $params['key'] =  $key;
        }
     
        $request_data = [
            "body" => json_encode($body),
            'query' => $params
        ];
        
        $this->response = $this->client->{strtolower($method)}(
            $relativeUrl, $request_data
        );

        dd($this->response);

        return $this;
    }

    /**
     * Place search via text 
     */
    public function getPlaceTextsearch(string $address)
    {
        $params['query'] = $address;
        $this->httpRequest('GET', '/place/textsearch/json', $data=[], $params);

        return $this->responseData();
    }

    /**
     * Get the access token set using the setter or after login
     * 
     * @return  \GuzzleHttp\Psr7\Response
     */
    public function getResponse(): Response
    {
        return $this->response ?? null;
    }

    public function responseData()
    {
        $data = [];

        if($this->response->getReasonPhrase() ==='OK')
        {
            $response =  \json_decode ($this->response->getBody(), true);

            if(array_key_exists('status', $response) && $response['status']===200)
            {
                $data = $response['data'];
            }
            else {
                \Log::error($response);
                throw new Exception( array_key_exists('message', $response) ? $response['message'] : "Error Processing Request", 1);
            }
           
        }

        return $data;
    }
    
}