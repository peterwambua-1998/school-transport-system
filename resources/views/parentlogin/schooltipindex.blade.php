@extends('layouts.app')
@section('css')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.1/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.1/css/dataTables.bootstrap4.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.3.2/css/buttons.bootstrap4.min.css">
<style>
    .card-header {
        border-top: 1px solid rgba(0,0,0,.125);
        border-radius: 0.25rem;
        background: #fff;
        border-left: 1px solid rgba(0,0,0,.125);
        border-right: 1px solid rgba(0,0,0,.125);
        align-content: center;
        height: 10vh;
    }

    .my-btn {
        background: #0071f3;
        border: 1px solid rgba(0,0,0,.125);
        border-radius: 0.10rem;
        padding: 5px;
        color: #fff;
        display: block;
        width: 100%;
        text-align: center;
    }

    .my-btn:hover {
        background: #014da3;
        cursor: pointer;
        color: #fff;
    }

    .top-navigation {
        padding: 8px;
        border-radius: .25rem;
        border: 1px solid rgba(0,0,0,.125);
        margin-bottom: 15px;
        display: flex;
        
    }

    .top-navigation p {
        flex-grow: 8;
        position: relative;
        top: 5px;
        letter-spacing: 1px;
    }
    
</style>
@endsection
@section('content')

<div class="top-navigation" style="background: #e2e8f0">
    <p style="font-weight: 600; font-size: 16px">School Trips</p>
</div>




<div class="page-wrapper">
    @foreach ($students as $student)
    <div>
        <p>School Trips for {{$student->first_name}} {{$student->last_name}}</p>
    </div>
    <div class="row">
        

        @php
            
            $schooltrips = App\Models\SchoolTrip::where('term_id', '=', $term->id)->where('grade', '=', $student->grade)->orWhere('grade', 'LIKE', 'general')->where('term_id', '=', $term->id)->orderBy('created_at', 'DESC')->get();

        @endphp
        @foreach ($schooltrips as $schooltrip)
            
        
        <div class="col-lg-3 col-md-3 col-sm-12">

            

            
                
           
                <div class="card">
                    <div class="card-body">
                        <p style="font-weight: bold; font-size: 16px">{{ $schooltrip->trip_name }}</p>  
                        <p><span style="font-weight: bold; font-size: 16px">Destination</span> : {{ $schooltrip->destination }}</p>
                        <p>{{ $settings->currency }} {{ $schooltrip->price }}</p>  
                        <p>Grade: {{ $schooltrip->grade }}</p>

                        @if ($schooltrip->price > 0)
                        <form action="{{ route('pcheckouttrip') }}" method="POST" style="padding: 0; margin: 0">
                            @csrf
                            <input type="hidden" name="inv" value="{{ $schooltrip->id }}">
                            <input type="hidden" name="student_id" value="{{ $student->id }}">
                            @php
                               $ispaid = App\Models\DepatureChecklist::where('schooltrip_id', '=', $schooltrip->id)->where('student_id', '=', $student->id)->first();
                            @endphp
                            @if (!$ispaid)
                            <button class="my-btn" >Pay</button>
                            @endif
                            
                        </form>
                        
                        @endif
                        
                    </div>    
                </div>   
            
            
        </div>
        @endforeach
        
    </div>
    @endforeach
</div>
@endsection


@section('js')
    <script defer src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.min.js"></script>
    <script defer>
        $(document).ready( function () {
            $('#vehTable').DataTable({
                language: { searchPlaceholder: "Search records", search: "",},
            });
        } );

    </script>
@endsection