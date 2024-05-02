<?php

namespace Database\Seeders;

use App\Enums\NotificationPreferenceEnum;
use App\Models\NotificationPreference;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class NotificationPreferenceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if(NotificationPreference::count() <= 0) 
        {
            NotificationPreference::upsert(
                $this->defaultNotificationPreferences(), 
                uniqueBy:['code'],
                update:['description', 'channel', 'type'],
    
            );
        }
    }

    public function defaultNotificationPreferences(){
        return [
            [
                'code'=> NotificationPreferenceEnum::Summary->value,
                'description'=> 'Receive an email summary notification',
                'channel' => 'email',
                'type' => null
            ],
            [
                'code'=>NotificationPreferenceEnum::AnnouncementAndSales->value,
                'description'=> 'Announcements and Sales promotions',
                'channel' => 'email',
                'type' => null
            ],
            [
                'code'=>NotificationPreferenceEnum::SellerCommunityUpdates->value,
                'description'=> 'Get notifications to stay up to date with seller community',
                'channel' => 'email',
                'type' => null
            ],
            [
                'code'=>NotificationPreferenceEnum::NewOrder->value,
                'description'=> 'Notify about new orders or requests',
                'channel' => 'email',
                'type' => null
            ]
        ];
    }
}
