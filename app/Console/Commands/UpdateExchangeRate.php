<?php

namespace App\Console\Commands;

use App\Models\Country;
use AshAllenDesign\LaravelExchangeRates\Classes\ExchangeRate;
use AshAllenDesign\LaravelExchangeRates\Classes\Validation;

use Illuminate\Console\Command;

class UpdateExchangeRate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:update-exchange-rate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update all currency exchange rate against USD';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $countries = Country::all();
        $exchangeRates = app(ExchangeRate::class);

        $exchange_driver = config('laravel-exchange-rates.driver');

       

        $supported_currencies = [];
        $unsupported_currencies = [];

        $countries->each(function ($country) use (&$supported_currencies, &$unsupported_currencies)
        {
            try {

                Validation::validateCurrencyCode($country->currency_code);
                $this->info($country->name.": ". $country->currency_code ." is supported" );
                $supported_currencies[$country->id] = $country->currency_code;


            } catch (\Throwable $th) {
                $this->warn($country->name.": ". $country->currency_code ." is supported" );
                $this->error("Error Message: ".$th->getMessage());

                $unsupported_currencies[$country->id] = $country->currency_code;
            }
            
        });

        try {

            $unique_supp_curs = array_unique($supported_currencies);
            $result = $exchangeRates->exchangeRate('USD', $unique_supp_curs);
            dd($result);
            // update exchange rate in database using result

        } catch (\Throwable $th) {
            //throw $th;
            $this->newLine();
            $this->warn('All currencies exchange rate against USD failed');
            $this->error("Error Message: ".$th->getMessage()); 
            // send exchange rate notification failed to admin
        }
        

    }
}
