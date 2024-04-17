<?php
namespace App\Enums;

use  App\Traits\Enum\HasEnumStaticMethods;

enum NotificationPreferenceEnum:string
{
    use HasEnumStaticMethods;

    case Summary = 'SUMMARY_NOTIFY';
    case AnnouncementAndSales = 'ANNOUNCEMENT_AND_SALES_NOTIFY';
    case SellerCommunityUpdates = 'SELLER_COMMUNITY_UPDATE_NOTIFY';
    case NewOrder = 'NEW_ORDER_NOTIFY';
}