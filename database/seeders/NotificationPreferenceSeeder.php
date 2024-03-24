<?php

namespace Database\Seeders;

use App\Enums\NotificationPreferenceEnums;
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
        // notification preference 
        $preferences =[
            [
                'code'=> NotificationPreferenceEnums::Summary->value,
                'description'=> 'Receive an email summary notification',
                'channel' => 'email',
                'type' => null
            ],
            [
                'code'=>NotificationPreferenceEnums::AnnouncementAndSales->value,
                'description'=> 'Announcements and Sales promotions',
                'channel' => 'email',
                'type' => null
            ],
            [
                'code'=>NotificationPreferenceEnums::SellerCommunityUpdates->value,
                'description'=> 'Get notifications to stay up to date with seller community',
                'channel' => 'email',
                'type' => null
            ],
            [
                'code'=>NotificationPreferenceEnums::NewOrder->value,
                'description'=> 'Notify about new orders or requests',
                'channel' => 'email',
                'type' => null
            ]
        ];

        NotificationPreference::upsert(
            $preferences, 
            uniqueBy:['code'],
            update:['description', 'channel', 'type'],

        );

    }
}
