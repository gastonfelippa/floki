@component('mail::message')
# Hola, {{$demo->receiver}} bienvenida a FlokI!

The body of your message.

@component('mail::button', ['url' => ''])
Button Text
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent