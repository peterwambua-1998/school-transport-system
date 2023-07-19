@extends('layouts.app')

@push('plugin-styles')
<link href="{{ asset('assets/plugins/select2/select2.min.css') }}" rel="stylesheet" />
<link href="{{ asset('assets/plugins/jquery-tags-input/jquery.tagsinput.min.css') }}" rel="stylesheet" />
<link href="{{ asset('assets/plugins/dropzone/dropzone.min.css') }}" rel="stylesheet" />
<link href="{{ asset('assets/plugins/dropify/css/dropify.min.css') }}" rel="stylesheet" />
<link href="{{ asset('assets/plugins/pickr/themes/classic.min.css') }}" rel="stylesheet" />
<link href="{{ asset('assets/plugins/flatpickr/flatpickr.min.css') }}" rel="stylesheet" />
<style>
    .my-nav {
        display: grid;
        grid-template-columns: 1fr 1fr;
    }
    .my-error {
        color: #ff3366;
    }
</style>
@endpush

@section('content')

<nav class="page-breadcrumb my-nav">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{route('school-fees.index')}}">School Fees</a></li>
        <li class="breadcrumb-item active" aria-current="page">Create</li>
    </ol>

    <div style="display: flex; flex-direction: row-reverse;">
        <a href="{{route('school-fees.index')}}" class="btn btn-warning"><ion-icon style="position: relative; top:3px; right: 5px; color: #000; font-size: 16px" name="close-circle-outline"></ion-icon> Cancel</a>
    </div>
</nav>


@if (Session::has('success'))
<div class="alert alert-success" role="alert" id="success">
    {{Session::get('success')}}
</div>
@endif

@if (Session::has('unsuccess'))
<div class="alert alert-danger" role="alert" id="danger">
    {{Session::get('unsuccess')}}
</div> 
@endif

@if ($errors->any())
<div class="alert alert-danger">
<ul>
    @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
    @endforeach
</ul>
</div>
@endif

<div class="row">
    <div class="col-lg-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <div class="text-center"><h3 class="card-title">School Fees Structure</h3></div>
                

                <form method="POST" id="my-form" action="{{ route('school-fees.update', Crypt::encrypt($fees->id)) }}">
                    <div class="card-body">
                        @csrf
                        @method('PATCH')
                        <h5 class="card-title">Student</h5>
                        <hr>
                        <div class="row">
                            <div class="col-sm-4">
                                <div class="mb-3">
                                    
                                    <label class="form-label" for="student_name">{{ucfirst($tr->grade_class) ?? 'Grades'}} </label>
                                    @if(count($grades) <= 0)
                                        <p class="text-danger">Please add {{ucfirst($tr->plural) ?? 'Grades'}}</p>
                                    @else
                                    <select id="grade_id" class="js-example-basic form-select vehicle_id" name="grade" required >
                                        <option>select...</option>
                                        @foreach ($grades as $grade)
                                        <option @if($grade->id == $fees->grade) selected @endif value="{{$grade->id}}">{{ $grade->name }} </option>
                                        @endforeach
                                    </select>
                                    @endif
                                    <span class="my-error" id="grade_error"></span>
                                </div>
                            </div><!-- Col -->
                            <div class="col-sm-4">
                                <div class="mb-3">
                                    <label class="form-label" for="term">Term</label>
                                    <input required type="text" id="term" class="form-control" placeholder="Term" value="{{$term->name}}" readonly>
                                    <input type="hidden" name="term"  value="{{$term->id}}" readonly>
                                </div>
                            </div><!-- Col -->
                            <div class="col-sm-4">
                                <div class="mb-3">
                                    <label class="form-label" for="year">Year</label>
                                    <input required type="text" id="year" class="form-control" name="year" readonly value="{{$fees->year}}">
                                </div>
                            </div><!-- Col -->
                        </div><!-- Row -->

                       
                        <hr>
                        {{-- <hr> --}}
                        <h5 class="card-title mt-4">Fee Details</h5>
                        <div class="table-holder">
                            <table class="table table-responsive table-bordered" id="table">
                                <thead>
                                    <tr>
                                        <th>Detail</th>
                                        <th>Amount</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($entries as $entry)
                                    <tr>
                                        <td>
                                            <input type="text" class="form-control entries" name="entries[]" value="{{$entry->entry}}">
                                            <span class="entry_error my-error"></span>
                                        </td>
                                        <td>
                                            <input type="number" name="amount[]" placeholder="0" class="form-control amounts" value="{{$entry->amount}}">
                                            <span class="my-error"></span>
                                        
                                        </td>
                                        <td>
                                            <a class="btn btn-sm btn-success" name="add" id="add">Add More</a>
                                        </td>
                                    </tr>
                                    @endforeach
                                    
                                </tbody>
                            </table>
                        </div>

                        <hr class="">
                        <div class="text-center">
                            <button type="button" id="my-submit" class="btn btn-success submit"><ion-icon style="position: relative; top:2px; right: 5px; color: #fff;" name="save"></ion-icon> Submit form</button>
                        </div>
                    </div>
                    
                </form>
            </div>
        </div>
    </div>

</div>


@endsection

@push('custom-scripts')

<script defer>
$(function() {


    var i = 0;
    $('#add').click(function () {

        ++i;
        $('#table').append(
            `<tr>
                <td>
                    <input type="text" class="form-control entries" name="entries[]">
                    <span class="entry_error my-error"></span>
                
                </td>
                <td>
                    <input type="text" name="amount[]" placeholder="Quantity" class="form-control amounts">
                    <span class="my-error"></span>
                
                </td>
                <td>
                    <a class="btn btn-sm btn-danger remove-table-row">Remove</a>
                </td>
            </tr>`
        );
    })

    $(document).on('click', '.remove-table-row', function () {
        $(this).parents('tr').remove();
    });

    $('#my-submit').on('click',(e) => {

        if ($('#grade_id').val() == 'select...') {
            $('#grade_error').text('field required');
        e.preventDefault();

            return;
        } else {
            $('#grade_error').text('');
        }

        $('.entries').each((i,e)=>{
            if (!$(e).val()) {
                $(e).next().text('field required');
        e.preventDefault();

                return;
            } else {
                $(e).next().text('');
            }
        });

        $('.amounts').each((i,e)=>{
            if (!$(e).val()) {
                $(e).next().text('field required');
        e.preventDefault();

                return;
            } else {
                $(e).next().text('');
            }
        });

        $('#my-form').submit();
    });
});
</script>

@endpush
