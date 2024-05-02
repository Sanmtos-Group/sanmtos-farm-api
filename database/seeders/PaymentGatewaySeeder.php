<?php

namespace Database\Seeders;

use App\Enums\PaymentGatewayEnum;
use App\Models\PaymentGateway;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Http\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
class PaymentGatewaySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        PaymentGateway::upsert(
            $this->defaultDefaultPaymentGateways(), 
            uniqueBy:['name'],
            update:['name'],
        );

        $folder = 'seeders/images/logos';
        $files = Storage::files($folder);

        $files_implode = implode(', ', $files);

        $payment_gateways = PaymentGateway::whereIn('name', PaymentGatewayEnum::values())->get();

        foreach ($files as $key => $file)        
        {
            $file_name_with_extension = Str::remove($folder.'/', $file);
            $file_name = Str::before($file_name_with_extension, '.');
            
            $payment_gateway = PaymentGateway::where('name', $file_name)->first();

            if(is_null($payment_gateway) ||  array_search(strtoupper($file_name), PaymentGatewayEnum::valuesToUpperCase()) === false) 
            {
                continue;
            }

            if(PaymentGateway::where('is_default', true)->count()<= 0 && strtolower($payment_gateway->name) === 'paystack')
            {
                $payment_gateway->is_active = true;
                $payment_gateway->is_default = true;
                $payment_gateway->save();
            }
           
            $logo = new File(storage_path('app/'.$file));

            $payment_gateway->deleteCloudinaryImages();
            $payment_gateway->uploadImageToCloudinary($file=$logo, $path='logos');

        }
        
    }

    public function defaultDefaultPaymentGateways()
    {
        $payment_gateways = [];

        foreach(PaymentGatewayEnum::values() as $payment_gateway)
        {
            $payment_gateways[] = [
                'name' => $payment_gateway
            ];
        }

        return $payment_gateways;
    }
}
