<?php

namespace Database\Seeders;

use App\Enums\LogisticEnum;
use App\Models\Logistic;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Http\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
class LogisticSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Logistic::upsert(
            $this->defaultDefaultLogistics(), 
            uniqueBy:['name'],
            update:['name'],
        );

        $folder = 'seeders/images/logos';
        $files = Storage::files($folder);

        $files_implode = implode(', ', $files);

        $logistics = Logistic::whereIn('name', LogisticEnum::values())->get();

        foreach ($files as $key => $file)        
        {
            $file_name_with_extension = Str::remove($folder.'/', $file);
            $file_name = Str::before($file_name_with_extension, '.');
            
            $logistic = Logistic::where('name', $file_name)->first();


            if(is_null($logistic) || array_search(strtoupper($file_name), LogisticEnum::valuesToUpperCase()) === false) 
            {
                continue;
            }

            if(Logistic::where('is_default', true)->count()<= 0 && strtolower($logistic->name) === 'dhl')
            {
                $logistic->is_active = true;
                $logistic->is_default = true;
                $logistic->save();
            }

            $logo = new File(storage_path('app/'.$file));

            $logistic->deleteCloudinaryImages();
            $logistic->uploadImageToCloudinary($file=$logo, $path='logos');

        }
        
    }

    public function defaultDefaultLogistics()
    {
        $logistics = [];

        foreach(LogisticEnum::values() as $logistic)
        {
            $logistics[] = [
                'name' => $logistic
            ];
        }

        return $logistics;
    }
}
