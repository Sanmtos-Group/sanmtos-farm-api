<?php

namespace App\Console\Commands;

use App\Models\Country;
use App\Models\CurrencyExchangeRate;
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
    protected $description = 'Update all currency exchange rate';

    /**
     * The base currency.
     *
     * @var string
     */
    protected $from = 'USD';


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
                $supported_currencies[$country->id] = $country->currency_code;


            } catch (\Throwable $th) {
                $unsupported_currencies[$country->id] = $country->currency_code;

                $this->warn(count($unsupported_currencies).".".$country->name.": ". $country->currency_code ." is not supported" );
                $this->error("Error Message: ".$th->getMessage());

            }
            
        });

        try {

            $unique_supp_curs = array_unique($supported_currencies);
            $result = $exchangeRates->exchangeRate($this->from, $unique_supp_curs);
            
            foreach ($result as $key => $value) 
            {
                $curr_exchng_rate = CurrencyExchangeRate::firstOrNew([
                    'from' => $this->from,
                    'to' => $key
                ]);

                $curr_exchng_rate->value = $value;
                $curr_exchng_rate->save();

                $this->info("1 ".$curr_exchng_rate->from ." - ".$curr_exchng_rate->to.' = '.$curr_exchng_rate->value );

            }
            $this->newLine();
            $this->line('<------------------------SUMMARY------------------------>');
            $this->line('Base currency = '.$this->from);
            $this->line('Supported currency   = '.count($supported_currencies));
            $this->line('Unspoported currency = '.count($unsupported_currencies));
            $this->line('Total currency       = '.count($supported_currencies+ $unsupported_currencies));

        } catch (\Throwable $th) {
            //throw $th;
            $this->newLine();
            $this->warn('All currencies exchange rate against USD failed');
            $this->error("Error Message: ".$th->getMessage()); 
            // send exchange rate notification failed to admin
        }
        

    }
}
