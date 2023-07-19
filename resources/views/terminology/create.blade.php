@extends('layouts.app')

@push('plugin-styles')
    <style>
        #my-sin {
            position: relative;
            margin-top: 5%;
        }

        .my-button {
            background: transparent;
            border: 0;
            padding: 0;
            margin: 0;
            position: relative;
            margin-top: 5%;
        }

        .my-button i {
            font-size: 26px;
            color: #05a34a;
        }
        .my-button i:hover {
            color: #048b3f;
        }
        .issues {
        color: #ff3366;
    }
    </style>
@endpush

@section('content')
<div class="row">
  <div class="col-md-12 grid-margin stretch-card">  
      <div class="card">
          <div class="card-body">
            <h6 class="card-title">Terminology Settings</h6>

            <hr>

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

            <div class="">
                
                    {{-- <input required type="text" name="terminology_grade" class="form-control"  placeholder="Grades or Classes" @if($terminology) value="{{$terminology->grade_class ?? ""}}" @endif> --}}
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered" id="dataTableExample2">
                            <thead>
                                <tr>   
                                    <th>SIN</th>
                                    <th>Name</th>
                                    <th>Label</th>
                                    <th>Plural</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <form action="{{route('terminology.store')}}" id="my-form" method="post">
                                        @csrf
                                        <td>1</td>
                                        <td><input  type="text" name="" class="form-control"  placeholder="0" value="Class" readonly></td>
                                        <td><input required type="text" name="terminology_grade" class="form-control inputs"  placeholder="Grades or Classes" @if($terminology) value="{{$terminology->grade_class ?? ""}}" @endif ><p class="issues"></p></td>
                                        <td><input required type="text" name="plural" class="form-control inputs"  placeholder="Grades or Classes" @if($terminology) value="{{$terminology->plural ?? ""}}" @endif ><p class="issues"></p></td>
                                        <td><button type="button" id="submit-btn" class="my-button"><i class="fa-solid fa-floppy-disk"></i></button></td>
                                    </form>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    
                    
            </div>
          </div>
      </div>

    </div>
</div>


@endsection

@push('custom-scripts')
    <script defer>
        $(function() {
            $('#submit-btn').on('click', (ev) => {
                let is_empty;

                $('.inputs').each((i, e) => {
                    if (!$(e).val()) {
                        ev.preventDefault();
                        $(e).focus();
                        $(e).next().text('field required');
                        is_empty = true;
                    } else {
                        $(e).next().text('');
                    }
                });
                if (is_empty) {
                    return;
                }

                $('#my-form').submit();
            });

           
            
        })
    </script>
@endpush
