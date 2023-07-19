@component('mail::message')
<h1>INVOICE</h1>


@component('mail::headergrid')
<div><p>Inv No: {{ $invoice->inv_num }}<p></div>
<div><p>Date Issued: {{ date('Y-m-d') }}</p></div>





@endcomponent



@component('mail::billedto')
@component('mail::anothergrid')
<div><p>Billed to</p> {{ $parentName }} <br>{{ $parentPhone }}</div>

<div>
    <p>Company</p>
    {{ $settings->company_name ?? 'company name' }} <br>
    TEL: {{ $settings->company_contact ?? '0700 000 000' }} <br>
   
</div>

@endcomponent

@endcomponent

<br>

<p>Transport Fee This Month</p>
@component('mail::table')
| Description   | Amount        |
| ------------- |:-------------:|
| Transport Fee |KSH {{ $amt }} |

@endcomponent

@component('mail::button', ['url' => ''])
Button Text
@endcomponent

Thanks, 
{{ $settings->company_name ?? 'company name' }}
@endcomponent

