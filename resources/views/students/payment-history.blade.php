<div class="table-responsive">
    <table class="table table-bordered table-striped" id="dataTableExample" data-ordering="false">
        <thead>
            <tr>
                <th>#</th>
                <th>{{ucfirst($tr->grade_class) ?? 'Grade'}}</th>
                <th>Term</th>
                <th>Year</th>
                <th>Fee ({{$settings->currency}})</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php $number = 1; ?>
            @foreach ($fee_history as $history)
            <tr>
                <td>{{$number}}</td>
                <?php $number++ ?>
                <td>
                    <?php $grade = DB::table('student_classes')->where('id','=', $history->grade)->first(); ?>
                    {{$grade->name}}
                    
                </td>
                <td>
                    <?php $term = App\Models\SchoolTermDate::where('id','=',$history->term)->first(); ?>
                    {{$term->name}}</td>
                <td>{{date('Y')}}</td>
                <td>{{number_format($history->amount, 2)}}</td>
                <td>
                    <i class="fa-solid fa-eye text-info" data-bs-toggle="modal" data-bs-target="#history{{$history->id}}">
                </td>
            </tr>
            @endforeach
            
        </tbody>
        
    </table>    
</div>

@foreach ($fee_history as $history)
<div class="modal fade" id="history{{$history->id}}" tabindex="-1" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-primary" id="exampleModalCenterTitle">Fee History Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="btn-close"></button>
            </div>
        <div class="modal-body">
            @foreach ($history->payments as $payment)
            <ul class="list-group mb-3">
                <li class="list-group-item">
                    <span class="ml-5 text-muted">Amount Paid:</span> <span>{{$settings->currency}} {{$payment->amount_paid}}</span>
                </li>
                <li class="list-group-item">
                    <span class="ml-5 text-muted">Payment Method:</span> <span>{{$payment->payment_method}}</span>
                </li>
                <li class="list-group-item">
                    <span class="ml-5 text-muted">Balance:</span> <span>{{$settings->currency}}  {{$payment->balance}}</span>
                </li>
                <li class="list-group-item">
                    <span class="ml-5 text-muted">Date Paid:</span> <span>{{$payment->date_paid}}</span>
                </li>
              </ul>
            @endforeach
        </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-warning" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endforeach