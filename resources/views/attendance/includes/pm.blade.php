<table class="table table-bordered table-striped" id="dataTableExample2" data-ordering="false">
    <thead>
        <tr>
            <th>#</th>
            <th>First Name</th>
            <th>Last Name</th>
            <th>Grade</th>
            <th>Attendance</th>
            <th>Vehicle</th>
            <th>Driver</th>
            <th>Date</th>
        </tr>
    </thead>
    <tbody>
        <?php $number = 1; ?>
        @foreach ($attendancePm as $item)
            @php
                $vehicle = App\Models\Vehicle::where('id', '=', $item->vehicle_id)->first();
                $student = App\Models\Student::where('id', '=', $item->student_id)->first();
                $driver = App\Models\User::where('id', '=', $item->driver_id)->first();
            @endphp
            <tr>
                <td>{{ $number }}</td>
                <?php $number++; ?>
                <td>{{ $student->first_name }}</td>
                <td>{{ $student->last_name }}</td>
                <td>{{ $student->grade }}</td>
                <td>
                    @if ($item->present == "false")
                        absent
                    @else
                        present
                    @endif
                </td>
                <td>{{ $vehicle->title }} {{ $vehicle->plate_num }}</td>
                <td>{{ $vehicle->driver->name }}</td>
                <td>{{ $item->created_at }}</td>
            </tr>
        @endforeach
        
    </tbody>

    
</table>
