<?php

namespace Database\Seeders;

use App\Enums\LogisticCompanyEnum;
use App\Models\LogisticCompany;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Http\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class LogisticCompanySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        LogisticCompany::upsert(
            $this->defaultDefaultLogisticCompanies(), 
            uniqueBy:['name'],
            update:['name'],
        );

        $folder = 'seeders/images/logos';
        $files = Storage::files($folder);

        $files_implode = implode(', ', $files);

        $logistic_companys = LogisticCompany::whereIn('name', LogisticCompanyEnum::values())->get();

        foreach ($files as $key => $file)        
        {
            $file_name_with_extension = Str::remove($folder.'/', $file);
            $file_name = Str::before($file_name_with_extension, '.');
            
            $logistic_company = LogisticCompany::where('name', $file_name)->first();


            if(is_null($logistic_company) || array_search(strtoupper($file_name), LogisticCompanyEnum::valuesToUpperCase()) === false) 
            {
                continue;
            }

            if(LogisticCompany::where('is_default', true)->count()<= 0 && strtolower($logistic_company->name) === 'dhl')
            {
                $logistic_company->is_active = true;
                $logistic_company->is_default = true;
                $logistic_company->save();
            }

            $logo = new File(storage_path('app/'.$file));

            $logistic_company->deleteCloudinaryImages();
            $logistic_company->uploadImageToCloudinary($file=$logo, $path='logos');

        }
        
    }

    public function defaultDefaultLogisticCompanies()
    {
        $logistic_companys = [];

        foreach(LogisticCompanyEnum::values() as $logistic_company)
        {
            $logistic_companys[] = [
                'name' => $logistic_company
            ];
        }

        return $logistic_companys;
    }
}
