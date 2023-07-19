@extends('layouts.app')
@push('plugin-styles')
    <link href="{{ asset('assets/plugins/datatables-net-bs5/dataTables.bootstrap5.css') }}" rel="stylesheet" />
    
@endpush
@section('content')

<div class="row">
    <div class="col-md-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-header">
                <div>
                    <h6 class="card-title">Todays Attendance List</h6>
                </div>
                <label class="form-label" for="">Enter Date To Query Attendance</label>
                <div class="" style="width: 100%;display: flex;">
                    <input type="date" class="form-control inline" id="from">
                    <button class="btn btn-success" id="getdatasss">Submit</button>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped" id="dataTableExample">
                        <thead >
                            <tr>
                                <th>First Name</th>
                                <th>Last Name</th>
                                <th>Attendance</th>
                                <th>Grade</th>
                                <th>Vehicle</th>
                                <th>Driver</th>
                                <th>Date</th>
                                
                            </tr>
                        </thead>
                        <tbody id="mybody">
                            
                            
                        </tbody>
                    
                        
                    </table>
                </div>
            </div>
        </div>

    </div>
</div>


@endsection

@push('plugin-scripts')
  <script src="{{ asset('assets/plugins/datatables-net/jquery.dataTables.js') }}"></script>
  <script src="{{ asset('assets/plugins/datatables-net-bs5/dataTables.bootstrap5.js') }}"></script>
  <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endpush
@push('custom-scripts')
<script src="{{ asset('assets/js/data-table.js') }}"></script>
    <script defer>
        console.log('peter');
        $(document).ready( function () {
            $.ajax({
                type: "GET",
                url: "{{route('schoolattendancedata')}}",
                processData: false,
                contentType: false,
                cache: false,
                error: function(data){
                    console.log(data);
                },
                success: function (response) {
                    $('#mybody').html(response['table']);
                    
                        //$('.total').text('KSH ' + response['total']);
                  
                    $('#vehTable').DataTable({
                        searching: false
                    });
                }
            });

            
            

            function getData() {
                var from = $('#from').val();


                var data = new FormData;
                data.append('_token', '{{ csrf_token() }}');
                data.append('from', from);
                

                $.ajax({
                    type: "POST",
                    url: "{{ route('schoolattendancequery') }}",
                    processData: false,
                    contentType: false,
                    cache: false,
                    data: data,
                    error: function (err) {
                        console.log(err)
                    },
                    success: function (response) {
                        console.log(response);
                        $("#vehTable").dataTable().fnDestroy();
                        $('#mybody').html(response['table']);
                        
                        $('#vehTable').DataTable({
                            searching: false
                        });

                    }
                });
                
            }

        $('#getdatasss').on('click', getData);
        } );

        

    </script>
@endpush