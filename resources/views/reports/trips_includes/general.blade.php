
<div class="table-responsive">
    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>#</th>
                <th>Trip Time</th>
                <th>No of Students</th>
                <th>Complaints</th>
                <th>Distance Covered</th>
            </tr>
        </thead>
        <tbody>
            <?php $number = 1; ?>
            @foreach ($vehicle->trips as $trip)
            <tr>
                <td>{{$number}}</td>
                <?php $number++; ?>
                <td>{{ucfirst($trip->time)}} ({{date_format(date_create($trip->time_from),'h:i A' )}} - {{date_format(date_create($trip->time_to),'h:i A' )}})</td>
                <td>{{$trip->num_of_students}}</td>
                <td>{{$trip->incidents}}</td>
                <td>{{($trip->distance) * 0.001}} Km</td>
            </tr>
            @endforeach

        </tbody>
        
        
    </table>
</div>