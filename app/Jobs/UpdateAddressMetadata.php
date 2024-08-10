<?php

namespace App\Jobs;

use App\Models\Address;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class UpdateAddressMetadata implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    /**
     * The address instance.
     *
     * @var \App\Models\Address
     */
    public $address;
    
    /**
     * Create a new job instance.
     */
    public function __construct(Address $address)
    {
        $this->address = $address;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        
        $metadata = $this->address->metadata;
        $metadata['googlemaps']['places']['text_search'] = \GoogleMaps::load('textsearch')
                                                            ->setParam([
                                                                'query' => $this->address->full_address,
                                                                'radius' => '500',
                                                            ])->getResponseByKey(' '); 

        $this->address->metadata = $metadata;
        $this->address->saveQuietly();
    }
}
