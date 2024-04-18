<?php

namespace Database\Seeders;

use App\Enums\HtmlInputTypeEnum;
use App\Models\Setting;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if(Setting::count() <=0 )
        {
            Setting::upsert(
                $this->defaultSettings(), 
                uniqueBy:['key', 'store_id'],
                update:(new Setting)->getFillable(),
            );
        }
    }

    public function defaultSettings(){
        return [
            [
                'store_id' => null,
                'html_input_type'  => HtmlInputTypeEnum::Number,
                'select_options' => null,
                'name'      => 'VAT',
                'description' => 'Value Added Tax (%) to be deducted from all total purchased price',
                'key' => 'vat',
                'value' => 5,
                'group_name' => 'app setting',
                'settable_id' => null,
                'settable_type' => null,
                'allowed_editor_roles' => json_encode(['super-admin']),
                'allowed_view_roles'  => json_encode(['*']),
                'owner_feature' => 'app setting'
            ],
        ];
    }
}
