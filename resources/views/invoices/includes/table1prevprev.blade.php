<table class="table table-striped" style="border: 1px solid gray;" id="vehTable3">
    <thead style="background-color: #0071f3; color: #fff">
        <tr>
        
            <th>Inv No</th>
            <th>Paid</th>
            <th>Balance</th>
            <th>Total</th>

            <th>Status</th>
            <th>Parent</th>
            <th>Student</th>
            <th>Date</th>
            
        </tr>
    </thead>
    <tbody>

        @foreach ($invoicePrevPrevMonth as $invoice)

            <tr>
            <td>{{ $invoice->inv_num }}</td>
            @php 
                $amtReceipt = 0;

                foreach ($invoice->receipt as $receipt) {
                    $amtReceipt += $receipt->amount;
                }
                $invAmt = $invoice->amount;

                $balance = $invAmt - $amtReceipt;

                $parent = App\Models\User::where('id', '=', $invoice->parent_id)->first();
                $student = App\Models\Student::where('id', '=', $invoice->student_id)->first();

                $date=date_create($invoice->created_at);

                $fDate = date_format($date,"Y/m/d");
            @endphp
            <td>{{ $amtReceipt }}</td>
            <td>{{ $balance }}</td>
            <td>{{ $invAmt }}</td>
            <td>{{ $invoice->status }}</td>
            <td>{{ $parent->name }}</td>
            <td>{{ $student->first_name }} {{ $student->last_name }}</td>
            <td>{{ $fDate }}</td>
            </tr>
        @endforeach
        
        
    </tbody>

    
</table>