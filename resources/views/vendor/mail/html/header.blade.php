@props(['url'])
<tr>
<td class="header">
<a href="{{ $url }}" style="display: inline-block;">
@if (trim($slot) === 'Laravel')
@if ($settings->company_logo)
<img src="{{asset('store/'.$settings->company_logo)}}" class="logo" alt="school Logo">
@else
<img src="{{asset('images/school-logo.png')}}" class="logo" alt="school Logo">
@endif
@else
{{ $slot }}
@endif
</a>
</td>
</tr>
