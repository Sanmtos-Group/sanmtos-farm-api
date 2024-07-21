<x-mail::message>
# {{$store_invitation->store->name}} Online Store Invitation
You have been invited to work with us on our online store <strong>{{$store_invitation->store->name?? ""}}</strong>
@php
    $accept_iv_url = env('SANMTOS_BASE_URL', '#').'/store-invitation/'.$store_invitation->id.'/accept';
    $decline_iv_url = env('SANMTOS_BASE_URL', '#').'/store-invitation/'.$store_invitation->id.'/decline';
@endphp
<x-mail::button :url="$accept_iv_url">
{{ __('Accept Invitation') }}
</x-mail::button>

{{ __('If you did not expect to receive an invitation to our store, you may discard this email or ') }} <a href="{{$decline_iv_url}}"  target="_blank" rel="noopener noreferrer" style="color:red">{{__('decline invitation')}}</a>.

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
