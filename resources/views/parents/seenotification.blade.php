@extends('layouts.app')
@section('css')
<link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.3.2/css/buttons.bootstrap4.min.css">
<script src="https://js.pusher.com/7.2/pusher.min.js"></script>
<script>
        function notifyMe(messsage) {
            if (!("Notification" in window)) {
                // Check if the browser supports notifications
                alert("This browser does not support desktop notification");
            } else if (Notification.permission === "granted") {
                // Check whether notification permissions have already been granted;
                // if so, create a notification
                const notification = new Notification(messsage);
                // …
            } else if (Notification.permission !== "denied") {
                // We need to ask the user for permission
                Notification.requestPermission().then((permission) => {
                    // If the user accepts, let's create a notification
                    if (permission === "granted") {
                        const notification = new Notification(messsage);
                        
                    }
                });
            }
        }
        Pusher.logToConsole = true;


        var pusher = new Pusher('05d822d3f46eb0987d53', {
            cluster: 'ap2',
            encrypted: true
        });

        var channel = pusher.subscribe('notifications-schoolapp');

        channel.bind('App\\Events\\NewMessageNotification', function(data) {
            if(data.user_id == "{{Auth::user()->id}}") {
                var messageTemplate = `
                <li class="message unread"  style="background: #e5e7eb  ">
                                            <div class="name">
                                                <div class="actions">
                                                    <span class="action"><i class="fa fa-square-o"></i></span>
                                                    <span class="action"><i class="fa fa-star-o"></i></span>
                                                </div>
                                                <div class="header">
                                                    <span class="from">from: {{ $settings->company_name ?? 'company name' }}</span>
                                                </div>
                                                <div class="title">
                                                    ${data.header}
                                                </div>
                                                <div class="description">
                                                    ${data.body}
                                                </div>
                                            

                                                
                                                

                                            </div> 
                                                
                                            
                                        </li>
                
                `;

                $('#my-append').prepend(messageTemplate);

                notifyMe('new notifaction from school transport');
            }
        });
</script>
<style>
.email-app {
    display: flex;
    flex-direction: row;
    background: #fff;
    border: 1px solid #e1e6ef;
}

.email-app nav {
    flex: 0 0 200px;
    padding: 1rem;
    border-right: 1px solid #e1e6ef;
}

.email-app nav .btn-block {
    margin-bottom: 15px;
}

.email-app nav .nav {
    flex-direction: column;
}

.email-app nav .nav .nav-item {
    position: relative;
}

.email-app nav .nav .nav-item .nav-link,
.email-app nav .nav .nav-item .navbar .dropdown-toggle,
.navbar .email-app nav .nav .nav-item .dropdown-toggle {
    color: #151b1e;
    border-bottom: 1px solid #e1e6ef;
}

.email-app nav .nav .nav-item .nav-link i,
.email-app nav .nav .nav-item .navbar .dropdown-toggle i,
.navbar .email-app nav .nav .nav-item .dropdown-toggle i {
    width: 20px;
    margin: 0 10px 0 0;
    font-size: 14px;
    text-align: center;
}

.email-app nav .nav .nav-item .nav-link .badge,
.email-app nav .nav .nav-item .navbar .dropdown-toggle .badge,
.navbar .email-app nav .nav .nav-item .dropdown-toggle .badge {
    float: right;
    margin-top: 4px;
    margin-left: 10px;
}

.email-app main {
    min-width: 0;
    flex: 1;
    padding: 1rem;
}

.email-app .inbox .toolbar {
    padding-bottom: 1rem;
    border-bottom: 1px solid #e1e6ef;
}

.email-app .inbox .messages {
    padding: 0;
    list-style: none;
}

.email-app .inbox .message {
    position: relative;
    padding: 1rem 1rem 1rem 2rem;
    cursor: pointer;
    border-bottom: 1px solid #e1e6ef;
}

.email-app .inbox .message:hover {
    background: #f9f9fa;
}

.email-app .inbox .message .actions {
    position: absolute;
    left: 0;
    display: flex;
    flex-direction: column;
}

.email-app .inbox .message .actions .action {
    width: 2rem;
    margin-bottom: 0.5rem;
    color: #c0cadd;
    text-align: center;
}

.email-app .inbox .message a {
    color: #000;
}

.email-app .inbox .message a:hover {
    text-decoration: none;
}

.email-app .inbox .message.unread .header,
.email-app .inbox .message.unread .title {
    font-weight: bold;
    color: #0071f3;
}

.email-app .inbox .message .header {
    display: flex;
    flex-direction: row;
    margin-bottom: 0.5rem;
}

.email-app .inbox .message .header .date {
    margin-left: auto;
}

.email-app .inbox .message .title {
    margin-bottom: 0.5rem;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.email-app .inbox .message .description {
    font-size: 12px;
}

.email-app .message .toolbar {
    padding-bottom: 1rem;
    border-bottom: 1px solid #e1e6ef;
}

.email-app .message .details .title {
    padding: 1rem 0;
    font-weight: bold;
}

.email-app .message .details .header {
    display: flex;
    padding: 1rem 0;
    margin: 1rem 0;
    border-top: 1px solid #e1e6ef;
    border-bottom: 1px solid #e1e6ef;
}

.email-app .message .details .header .avatar {
    width: 40px;
    height: 40px;
    margin-right: 1rem;
}

.email-app .message .details .header .from {
    font-size: 12px;
    color: #9faecb;
    align-self: center;
}

.email-app .message .details .header .from span {
    display: block;
    font-weight: bold;
}

.email-app .message .details .header .date {
    margin-left: auto;
}

.email-app .message .details .attachments {
    padding: 1rem 0;
    margin-bottom: 1rem;
    border-top: 3px solid #f9f9fa;
    border-bottom: 3px solid #f9f9fa;
}

.email-app .message .details .attachments .attachment {
    display: flex;
    margin: 0.5rem 0;
    font-size: 12px;
    align-self: center;
}

.email-app .message .details .attachments .attachment .badge {
    margin: 0 0.5rem;
    line-height: inherit;
}

.email-app .message .details .attachments .attachment .menu {
    margin-left: auto;
}

.email-app .message .details .attachments .attachment .menu a {
    padding: 0 0.5rem;
    font-size: 14px;
    color: #e1e6ef;
}

.card-header {
    border-top: 1px solid rgba(0,0,0,.125);
        border-radius: 0.25rem;
        background: #fff;
        border-left: 1px solid rgba(0,0,0,.125);
        border-right: 1px solid rgba(0,0,0,.125);
        margin-bottom: 20px;
}

.pagination {
    margin-top: 20px !important;
}

.page {
    
    background: #0071f3;
    color: #fff;
    padding-left: 20px;
    padding-right: 20px;
    padding-top: 10px;
    padding-bottom: 10px;
    border: 1px solid rgba(0,0,0,.125);
    border-radius: 0.25rem;
    align-items: center;
    text-align: center;
    margin-left: 10px;
    
}

.vihcleGrid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        justify-content: end;
        padding-left: 20px;
        padding-right: 3%;
    }

    .mark {
        font-size: 10px;
        background: rgb(1, 155, 1);
        color: #fff;
        padding: 10px;
        border-radius: 5px;
    }

    .my-btn {
        font-size: 10px;
        margin-top: 10px;
        display: none;
    }

    
@media (max-width: 768px) {
    .email-app {
        flex-direction: column;
    }
    .email-app nav {
        flex: 0 0 100%;
    }
    .mark {
        display: none;
    }

    .my-btn {
        display: block;
    }
}

@media (max-width: 575px) {
    .email-app .message .header {
        flex-flow: row wrap;
    }
    .email-app .message .header .date {
        flex: 0 0 100%;
    }
    
    .mark {
        display: none;
    }
}

.top-navigation {
        padding-left: 15px;
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
        font-weight: bold;
    }
</style>
@endsection
@section('content')

<div class="container">
<div class="top-navigation" style="background: #e2e8f0">
    <p>Notifications</p>
</div>
</div>





        
    
          
               
        
                    <div class="container bootdey">

                        
                        <div class="email-app mb-4">
                           
                            <main class="inbox" id="test-list">
                                <input type="text" class="search form-control mb-2" placeholder="Search Notifications" />
                        
                                <ul class="messages list" id="my-append">
                                    @foreach ($pNofitications as $notification)
                                    @if ($notification->type == 'App\Notifications\StopNotification')
                                    <li class="message unread" @if ($notification->read_at == null) style="background: #e5e7eb  " @endif>
                                        <div class="name">
                                            <div class="actions">
                                                <span class="action"><i class="fa fa-square-o"></i></span>
                                                <span class="action"><i class="fa fa-star-o"></i></span>
                                            </div>
                                            <div class="header">
                                                <span class="from">from: {{ $settings->company_name ?? 'company name' }}</span>
                                                <span class="date">
                                                <span class="fa fa-paper-clip"></span>{{ $notification->created_at->format('d/m/Y H:i') }}</span>
                                            </div>
                                            <div class="title">
                                                Vehicle Concluded Trip
                                            </div>
                                            <div class="description">
                                                {{ $notification->data['msg'] }}
                                            </div>
                                            <div style="float: right; top: -40px; position: relative; ">
                                                <a class="btn mark" href="{{ route('notification_read', $notification->id) }}">mark as read</a>
                                                
                                            </div>

                                            
                                            <button class="my-btn">mark as read</button>

                                        </div> 
                                            
                                        
                                    </li>
                                    @endif
                                    @if ($notification->type == 'App\Notifications\SchoolTripDepatureNotification')
                                    <li class="message unread" @if ($notification->read_at == null) style="background: #e5e7eb  " @endif>
                                        <div class="name">
                                            <div class="actions">
                                                <span class="action"><i class="fa fa-square-o"></i></span>
                                                <span class="action"><i class="fa fa-star-o"></i></span>
                                            </div>
                                            <div class="header">
                                                <span class="from">from: {{ $settings->company_name ?? 'company name' }}</span>
                                                <span class="date">
                                                <span class="fa fa-paper-clip"></span>{{ $notification->created_at->format('d/m/Y H:i') }}</span>
                                            </div>
                                            <div class="title">
                                                School Trip Departure
                                            </div>
                                            <div class="description">
                                                {{ $notification->data['msg'] }}
                                            </div>
                                            <div style="float: right; top: -40px; position: relative; ">
                                                <a class="btn mark" href="{{ route('notification_read', $notification->id) }}">mark as read</a>
                                                
                                            </div>

                                            
                                            <button class="my-btn">mark as read</button>

                                        </div> 
                                            
                                        
                                    </li>
                                    @endif

                                    @if ($notification->type == 'App\Notifications\ToParent')
                                        
                                    
                                    <li class="message unread" @if ($notification->read_at == null) style="background: #e5e7eb  " @endif>
                                        
                                            <div class="actions">
                                                <span class="action"><i class="fa fa-square-o"></i></span>
                                                <span class="action"><i class="fa fa-star-o"></i></span>
                                            </div>
                                            <div class="header">
                                                <span class="from">from: {{ $settings->company_name ?? 'company name' }}</span>
                                                <span class="date">
                                                <span class="fa fa-paper-clip"></span>{{ $notification->created_at->format('d/m/Y H:i') }}</span>
                                            </div>
                                            <div class="title">
                                                {{ $notification->data['haeder'] }}
                                            </div>
                                            <div class="description">
                                                {{ $notification->data['body'] }}
                                            </div>
                                            <div style="float: right; top: -40px; position: relative; ">
                                                <a class="btn mark" href="{{ route('pnotification_read', $notification->id) }}">mark as read</a>
                                                
                                            </div>
                                            <button class="my-btn">mark as read</button>
                                        
                                    </li>

                                    @endif


                                    @if ($notification->type == 'App\Notifications\StudentAttend')
                                        
                                    
                                    <li class="message unread" @if ($notification->read_at == null) style="background: #e5e7eb  " @endif>
                                        
                                            <div class="actions">
                                                <span class="action"><i class="fa fa-square-o"></i></span>
                                                <span class="action"><i class="fa fa-star-o"></i></span>
                                            </div>
                                            <div class="header">
                                                <span class="from">from: {{ $settings->company_name ?? 'company name' }}</span>
                                                <span class="date">
                                                <span class="fa fa-paper-clip"></span>{{ $notification->created_at->format('d/m/Y H:i') }}</span>
                                            </div>
                                            <div class="title">
                                                {{ $notification->data['msg'] }}
                                            </div>
                                            
                                            <div style="float: right; top: -40px; position: relative; ">
                                                <a class="btn mark" href="{{ route('pnotification_read', $notification->id) }}">mark as read</a>
                                                
                                            </div>
                                            <button class="my-btn">mark as read</button>
                                        
                                    </li>

                                    @endif
                                    @if ($notification->type == 'App\Notifications\BusLate')
                                        
                                    
                                    <li class="message unread" @if ($notification->read_at == null) style="background: #e5e7eb  " @endif>
                                        
                                            <div class="actions">
                                                <span class="action"><i class="fa fa-square-o"></i></span>
                                                <span class="action"><i class="fa fa-star-o"></i></span>
                                            </div>
                                            <div class="header">
                                                <span class="from">from: {{ $settings->company_name ?? 'company name' }}</span>
                                                <span class="date">
                                                <span class="fa fa-paper-clip"></span>{{ $notification->created_at->format('d/m/Y H:i') }}</span>
                                            </div>
                                            <div class="title">
                                                Late Bus
                                                
                                            </div>
                                            <div class="description">
                                                {{ $notification->data['msg'] }}
                                            </div>
                                            
                                            <div style="float: right; top: -40px; position: relative; ">
                                                <a class="btn mark" href="{{ route('pnotification_read', $notification->id) }}">mark as read</a>
                                                
                                            </div>
                                            <button class="my-btn">mark as read</button>
                                        
                                    </li>

                                    @endif
                                    @if ($notification->type == 'App\Notifications\StartNotification')
                                    <li class="message unread" @if ($notification->read_at == null) style="background: #e5e7eb  " @endif>
                                        <div class="name">
                                            <div class="actions">
                                                <span class="action"><i class="fa fa-square-o"></i></span>
                                                <span class="action"><i class="fa fa-star-o"></i></span>
                                            </div>
                                            <div class="header">
                                                <span class="from">from: {{ $settings->company_name ?? 'company name' }}</span>
                                                <span class="date">
                                                <span class="fa fa-paper-clip"></span>{{ $notification->created_at->format('d/m/Y H:i') }}</span>
                                            </div>
                                            <div class="title">
                                                Vehicle Start
                                            </div>
                                            <div class="description">
                                                {{ $notification->data['msg'] }}
                                            </div>
                                            <div style="float: right; top: -40px; position: relative; ">
                                                <a class="btn mark" href="{{ route('notification_read', $notification->id) }}">mark as read</a>
                                                
                                            </div>

                                            
                                            <button class="my-btn">mark as read</button>

                                        </div> 
                                            
                                        
                                    </li>
                                    @endif
                                    @if ($notification->type == 'App\Notifications\HereNotification')
                                    <li class="message unread" @if ($notification->read_at == null) style="background: #e5e7eb  " @endif>
                                        <div class="name">
                                            <div class="actions">
                                                <span class="action"><i class="fa fa-square-o"></i></span>
                                                <span class="action"><i class="fa fa-star-o"></i></span>
                                            </div>
                                            <div class="header">
                                                <span class="from">from: {{ $settings->company_name ?? 'company name' }}</span>
                                                <span class="date">
                                                <span class="fa fa-paper-clip"></span>{{ $notification->created_at->format('d/m/Y H:i') }}</span>
                                            </div>
                                            <div class="title">
                                                Alert
                                            </div>
                                            <div class="description">
                                                {{ $notification->data['msg'] }}
                                            </div>
                                            <div style="float: right; top: -40px; position: relative; ">
                                                <a class="btn mark" href="{{ route('notification_read', $notification->id) }}">mark as read</a>
                                                
                                            </div>

                                            
                                            <button class="my-btn">mark as read</button>

                                        </div> 
                                            
                                        
                                    </li>
                                    @endif
                                    @if ($notification->type == 'App\Notifications\SchoolTripReachedDestNotification')
                                    <li class="message unread" @if ($notification->read_at == null) style="background: #e5e7eb " @endif>
                                        <div class="name">
                                            <div class="actions">
                                                <span class="action"><i class="fa fa-square-o"></i></span>
                                                <span class="action"><i class="fa fa-star-o"></i></span>
                                            </div>
                                            <div class="header">
                                                <span class="from">from: {{ $settings->company_name ?? 'company name' }}</span>
                                                <span class="date">
                                                <span class="fa fa-paper-clip"></span>{{ $notification->created_at->format('d/m/Y H:i') }}</span>
                                            </div>
                                            <div class="title">
                                                School Trip
                                            </div>
                                            <div class="description">
                                                {{ $notification->data['msg'] }}
                                            </div>
                                            <div style="float: right; top: -40px; position: relative; ">
                                                <a class="btn mark" href="{{ route('notification_read', $notification->id) }}">mark as read</a>
                                                
                                            </div>

                                            
                                            <button class="my-btn">mark as read</button>

                                        </div> 
                                            
                                        
                                    </li>
                                    @endif
                                    @if ($notification->type == 'App\Notifications\SchoolTripGoingBackNotification')
                                    <li class="message unread" @if ($notification->read_at == null) style="background: #e5e7eb  " @endif>
                                        <div class="name">
                                            <div class="actions">
                                                <span class="action"><i class="fa fa-square-o"></i></span>
                                                <span class="action"><i class="fa fa-star-o"></i></span>
                                            </div>
                                            <div class="header">
                                                <span class="from">from: {{ $settings->company_name ?? 'company name' }}</span>
                                                <span class="date">
                                                <span class="fa fa-paper-clip"></span>{{ $notification->created_at->format('d/m/Y H:i') }}</span>
                                            </div>
                                            <div class="title">
                                                School Trip
                                            </div>
                                            <div class="description">
                                                {{ $notification->data['msg'] }}
                                            </div>
                                            <div style="float: right; top: -40px; position: relative; ">
                                                <a class="btn mark" href="{{ route('notification_read', $notification->id) }}">mark as read</a>
                                                
                                            </div>

                                            
                                            <button class="my-btn">mark as read</button>

                                        </div> 
                                            
                                        
                                    </li>
                                    @endif
                                    @if ($notification->type == 'App\Notifications\SchoolTripReachedSchoolNotification')
                                    <li class="message unread" @if ($notification->read_at == null) style="background: #e5e7eb  " @endif>
                                        <div class="name">
                                            <div class="actions">
                                                <span class="action"><i class="fa fa-square-o"></i></span>
                                                <span class="action"><i class="fa fa-star-o"></i></span>
                                            </div>
                                            <div class="header">
                                                <span class="from">from: {{ $settings->company_name ?? 'company name' }}</span>
                                                <span class="date">
                                                <span class="fa fa-paper-clip"></span>{{ $notification->created_at->format('d/m/Y H:i') }}</span>
                                            </div>
                                            <div class="title">
                                                School Trip
                                            </div>
                                            <div class="description">
                                                {{ $notification->data['msg'] }}
                                            </div>
                                            <div style="float: right; top: -40px; position: relative; ">
                                                <a class="btn mark" href="{{ route('notification_read', $notification->id) }}">mark as read</a>
                                                
                                            </div>

                                            
                                            <button class="my-btn">mark as read</button>

                                        </div> 
                                            
                                        
                                    </li>
                                    @endif
                                    @endforeach
                                </ul>
                                <ul class="pagination"></ul>
                            </main>
                        </div>
                        
                    
                        
                        </div>
                


@endsection


@section('js')
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/list.js/1.5.0/list.min.js"></script>
<!--
<script src="https://js.pusher.com/7.0/pusher.min.js"></script>
<script>
    Pusher.logToConsole = true;

var pusher = new Pusher('05d822d3f46eb0987d53', {
    cluster: 'ap2',
 
});

var channel = pusher.subscribe('toparentnotification');

channel.bind('pusher:subscription_succeeded', function(members) {
    console.log('successfully subscribed!');
});



channel.bind('NewNotification', function(data) {
    console.log(JSON.stringify(data));
});

channel.bind('pusher:error', function(err) {
    console.log(err);
});
</script>

-->
    <script defer>
        
        /*
        {{---
        Echo.channel('notifications-schoolapp').listen('NewNotification', (notification) => {

            
            if(notification.parent_id == "{{Auth::user()->id}}") {
                var messageTemplate = `
                <li class="message unread"  style="background: #e5e7eb  ">
                                            <div class="name">
                                                <div class="actions">
                                                    <span class="action"><i class="fa fa-square-o"></i></span>
                                                    <span class="action"><i class="fa fa-star-o"></i></span>
                                                </div>
                                                <div class="header">
                                                    <span class="from">from: {{ $settings->company_name ?? 'company name' }}</span>
                                                    <span class="date">
                                                    <span class="fa fa-paper-clip"></span>{{ $notification->created_at->format('d/m/Y H:i') }}</span>
                                                </div>
                                                <div class="title">
                                                    ${notification.header}
                                                </div>
                                                <div class="description">
                                                    ${notification.body}
                                                </div>
                                            

                                                
                                                

                                            </div> 
                                                
                                            
                                        </li>
                
                `;

                $('#my-append').prepend(messageTemplate);

                notifyMe('new notifaction from school transport');
            }
        });
        --}}
        */
        $(document).ready( function () {
            
            
            /*
            
            Pusher.logToConsole = true;

            var pusher = new Pusher('05d822d3f46eb0987d53', {
                cluster: 'ap2',
                wsHost: '127.0.0.1',
                wsPort: 6001,
                forceTLS: false
            });

            var channel = pusher.subscribe('tooo');

            channel.bind('pusher:subscription_succeeded', function(members) {
                console.log('successfully subscribed!');
            });

            

            channel.bind('App\Events\NewNotification', function(data) {
                console.log(JSON.stringify(data));
            });

            */
            
            var monkeyList = new List('test-list', {
                valueNames: ['actions', 'header', 'title', 'description'],
                page: 7,
                pagination: true
            })
            

            if("{{ Session::has('success') }}") {
                Swal.fire({
                    position: 'top-end',
                    icon: 'success',
                    title: '{{ Session::get("success") }}',
                    showConfirmButton: false,
                    timer: 1500
                });
                
            } else if ("{{ Session::has('unsuccess') }}") {
                Swal.fire({
                    position: 'top-end',
                    icon: 'error',
                    title: '{{ Session::get("unsuccess") }}',
                    showConfirmButton: false,
                    timer: 2500
                });
                
            }else if ("{{ Session::has('errors') }}") {
                Swal.fire({
                    position: 'top-end',
                    icon: 'error',
                    title: '{{ Session::get("errors") }}',
                    showConfirmButton: false,
                    timer: 2500
                });
            }
        } );
    </script>
@endsection