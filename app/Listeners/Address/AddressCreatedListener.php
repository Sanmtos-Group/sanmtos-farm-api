<?php

namespace App\Listeners\Address;

use App\Models\Address;
use App\Events\Address\AddressCreated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class AddressCreatedListener
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(AddressCreated $event): void
    {
        $address = $event->address;

        $no_of_addresable = Address::where('addressable_id', $address->addressable_id)
        ->where('addressable_type', $address->addressable_type)
        ->count();

        if($no_of_addresable <= 1)
        {
            $address->is_preferred = true;
            $address->save();
        }
        elseif ($address->is_preferred) 
        {
            Address::where('id', '<>', $address->id)
            ->where('addressable_id', $address->addressable_id)
            ->where('addressable_type', $address->addressable_type)
            ->update(['is_preferred' => false]);
        }
        
    }
}
