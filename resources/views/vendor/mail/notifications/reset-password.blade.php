<x-mail::message>
{{-- Greeting --}}

# <h5 style="font-size: 20px;color: #ffc107; margin:0; padding:0">Hello</h5>

<x-mail::subcopy></x-mail::subcopy>
{{-- Intro Lines --}}
<p >Your token is:</p>
<p style="font-weight: bold; color: #ffc107">{{$token}}</p>



<x-mail::subcopy>
@lang("The code will expire in two minutes.") <br>
@lang("If you have not tried to login ignore this mail.")
</x-mail::subcopy>
{{-- Outro Lines --}}
@foreach ($outroLines as $line)
{{ $line }}
@endforeach

{{-- Salutation --}}
@if (! empty($salutation))
{{ $salutation }}
@else
@lang('Regards'),<br>
@if ($settings)
    {{$settings->company_name}}
@else
@lang('Institution')
@endif
@endif


</x-mail::message>
