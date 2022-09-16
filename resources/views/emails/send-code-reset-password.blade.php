@component('mail::message')
<h1></h1></h1>
<p>Please use the following code to recover your account:</p>

@component('mail::panel')
{{ $code }}
@endcomponent

<p>This code will expire in one hour </p>
@endcomponent