@extends('layouts.app')
@section('css')
<link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.3.2/css/buttons.bootstrap4.min.css">
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
</style>
@endsection
@section('content')


<div class="vihcleGrid">

    <div>
        <p style="font-size: 16px;"><span style="font-weight:300;">Unread</span> - <span style="font-weight:500;">Notification</span></p>
    </div>
    
    
</div>



        
    
          
               
        
                    <div class="container bootdey">

                        @if ($numOfNotifications >= 1)
                        <div class="email-app mb-4">
                           
                            <main class="inbox">
                                
                        
                                <ul class="messages">
                                    @foreach ($notifications as $notification)

                                    @if ($notification->type == 'App\Notifications\InvoicePaid')
                                        
                                    
                                    <li class="message unread">
                                        <a href="#">
                                            <div class="actions">
                                                <span class="action"><i class="fa fa-square-o"></i></span>
                                                <span class="action"><i class="fa fa-star-o"></i></span>
                                            </div>
                                            <div class="header">
                                                <span class="from">from: system name</span>
                                                <span class="date">
                                                <span class="fa fa-paper-clip"></span>{{ $notification->created_at->format('d/m/Y H:i') }}</span>
                                            </div>
                                            <div class="title">
                                                Invoice Paid
                                            </div>
                                            <div class="description">
                                                @php
                                        
                                                $name = 'deleted';

                                                $user = App\Models\User::where('id', '=', $notification->data['user'])->first();

                                                if ($user) {
                                                    $name = $user->name;
                                                }
                                            @endphp
                                            Paid invoice from {{ $name }}, amount {{ $notification->data['amount'] }}
                                            </div>
                                            <div style="float: right; top: -40px; position: relative; ">
                                                <a class="btn mark" href="{{ route('notification_read', $notification->id) }}">mark as read</a>
                                                
                                            </div>
                                            <button class="my-btn">mark as read</button>
                                        </a>
                                    </li>
                                    @endif

                                    @if ($notification->type == 'App\Notifications\VehicleOutOfFence')
                                        
                                    
                                    <li class="message unread">
                                        <a href="#">
                                            <div class="actions">
                                                <span class="action"><i class="fa fa-square-o"></i></span>
                                                <span class="action"><i class="fa fa-star-o"></i></span>
                                            </div>
                                            <div class="header">
                                                <span class="from">from: system name</span>
                                                <span class="date">
                                                <span class="fa fa-paper-clip"></span>{{ $notification->created_at->format('d/m/Y H:i') }}</span>
                                            </div>
                                            <div class="title">
                                                Invoice Paid
                                            </div>
                                            <div class="description">
                                                {{ $notification->data['msg'] }} 
                                            </div>
                                            <div style="float: right; top: -40px; position: relative; ">
                                                <a class="btn mark" href="{{ route('notification_read', $notification->id) }}">mark as read</a>
                                                
                                            </div>
                                            <button class="my-btn">mark as read</button>
                                        </a>
                                    </li>
                                    @endif
                                    @endforeach
                                </ul>
                            </main>
                        </div>
                        
                            
                        @else
                            <h2>NO NEW NOTIFICATIONS</h2>
                        @endif
                        
                        </div>
                


@endsection


@section('js')
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script defer>
        $(document).ready( function () {
           

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