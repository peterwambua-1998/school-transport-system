@extends('layouts.app')
@push('plugin-styles')
  <link href="{{ asset('assets/plugins/select2/select2.min.css') }}" rel="stylesheet" />
  <style>
    .list {
      list-style: none;
      margin: 0 !important;
      padding: 0 !important;
    }
    .pagination .active .page {
      background: #daa505;
    }
    .page {
      background: #fbbc06;
      color: #000;
      padding-left: 10px;
      padding-right: 10px;
      padding-top: 5px;
      padding-bottom: 5px;
      border-radius: 0.375rem;
      margin-left: 1px;
      font-weight: 500;
    }
    .page:hover {
      color: #000;
      background: #daa505;
    }
  </style>
@endpush
@section('content')

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

<div class="row inbox-wrapper">
  <div class="col-lg-12">
    <div class="card">
      <div class="card-body">
        <div class="row">
          <div class="col-lg-3 border-end-lg">
            <div class="d-flex align-items-center justify-content-between">
              <button class="navbar-toggle btn btn-icon border d-block d-lg-none" data-bs-target=".email-aside-nav" data-bs-toggle="collapse" type="button">
                <span class="icon"><i data-feather="chevron-down"></i></span>
              </button>
              <div class="order-first">
                <h4>Notification Service</h4>
                <p class="text-muted">{{Auth::user()->name }}</p>
              </div>
            </div>
            <div class="d-grid my-3">
              <a class="btn btn-primary" href="{{route('delete_user_notifications')}}">Mark All As Read</a>
            </div>
            <div class="email-aside-nav collapse">
              <ul class="nav flex-column">
                <li class="nav-item active" id="home-tab" data-bs-toggle="tab" data-bs-target="#home" role="tab" aria-controls="home" aria-selected="false">
                  <a class="nav-link d-flex align-items-center" href="#">
                    <i data-feather="inbox" class="icon-lg me-2"></i>
                    Inbox
                    <span class="badge bg-danger fw-bolder ms-auto">{{count($notifications)}}
                  </a>
                </li>
                <li class="nav-item" id="contact-tab" data-bs-toggle="tab" data-bs-target="#contact" role="tab" aria-controls="contact" aria-selected="true">
                  <a class="nav-link d-flex align-items-center" href="#">
                    <i data-feather="mail" class="icon-lg me-2"></i>
                    Send Notification
                  </a>
                </li>
                
                </li>
                
            
              </ul>
              
            </div>
          </div>
          <div class="col-lg-9 tab-content">
            <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
              <div id="test-list">
                <div class="p-3 border-bottom">
                  <div class="row align-items-center">
                    <div class="col-lg-6">
                      <div class="d-flex align-items-end mb-2 mb-md-0">
                        <i data-feather="inbox" class="text-muted me-2"></i>
                        <h4 class="me-1">Inbox</h4>
                        <span class="text-muted">({{count($notifications)}} new notifications)</span>
                      </div>
                    </div>
                    <div class="col-lg-6">
                      <div class="input-group">
                        <input class="form-control search" type="text" placeholder="Search notification...">
                        <button class="btn btn-light btn-icon" type="button" id="button-search-addon"><i data-feather="search"></i></button>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="p-3 border-bottom d-flex align-items-center justify-content-between flex-wrap">
                  
                  <div class="d-flex align-items-center justify-content-end flex-grow-1">
                    <span class="me-2"></span>
                    <div class="btn-group">
                      <ul class="pagination"></ul>
                    </div>
                  </div>
                </div>
                <div class="email-list">
                  <ul class="list">
                    @foreach ($notifications as $notification)
                    <!-- email list item -->
                    @if ($notification->type == 'App\Notifications\StartNotification')
                      <li>
                        <div class="email-list-item @if(!$notification->read_at) email-list-item--unread @endif">
                          <div class="email-list-actions">
                            <div class="form-check">
                              <input type="checkbox" class="form-check-input">
                            </div>
                            <a class="favorite" href="javascript:;"><span><i data-feather="star"></i></span></a>
                          </div>
                          <a href="#" class="email-list-detail">
                            <div class="content">
                              <span class="from">Trip Commenced</span>
                              <div class="description"> 
                                <p style="color: #7987a1; font-size: 0.8rem">{{ $notification->data['body'] }}</p>
                              </div>
                              
                            </div>
                            <span class="date">
                              {{ $notification->created_at->format('d-M-Y H:i') }}
                            </span>
                          </a>
                        </div>
                      </li>
                    @endif

                    @if ($notification->type == 'App\Notifications\BusLate')
                    <li>
                      <div class="email-list-item @if(!$notification->read_at) email-list-item--unread @endif">
                        <div class="email-list-actions">
                          <div class="form-check">
                            <input type="checkbox" class="form-check-input">
                          </div>
                          <a class="favorite" href="javascript:;"><span><i data-feather="star"></i></span></a>
                        </div>
                        <a href="#" class="email-list-detail">
                          <div class="content">
                            <span class="from">Bus late</span>
                            <div class="description"> 
                              <p style="color: #7987a1; font-size: 0.8rem">{{ $notification->data['body'] }}</p>
                            </div>
                            
                          </div>
                          <span class="date">
                            {{ $notification->created_at->format('d-M-Y H:i') }}
                          </span>
                        </a>
                      </div>
                    </li>
                    @endif

                    @if ($notification->type == 'App\Notifications\DlExpredNotification')
                    <li>
                      <div class="email-list-item @if(!$notification->read_at) email-list-item--unread @endif">
                        <div class="email-list-actions">
                          <div class="form-check">
                            <input type="checkbox" class="form-check-input">
                          </div>
                          <a class="favorite" href="javascript:;"><span><i data-feather="star"></i></span></a>
                        </div>
                        <a href="#" class="email-list-detail">
                          <div class="content">
                            <span class="from">Expired Driving License</span>
                            <div class="description"> 
                              <p style="color: #7987a1; font-size: 0.8rem">{{ $notification->data['body'] }}</p>
                            </div>
                            
                          </div>
                          <span class="date">
                            {{ $notification->created_at->format('d-M-Y H:i') }}
                          </span>
                        </a>
                      </div>
                    </li>
                    @endif

                    @if ($notification->type == 'App\Notifications\InspectionDateNotification')
                    <li>
                      <div class="email-list-item @if(!$notification->read_at) email-list-item--unread @endif">
                        <div class="email-list-actions">
                          <div class="form-check">
                            <input type="checkbox" class="form-check-input">
                          </div>
                          <a class="favorite" href="javascript:;"><span><i data-feather="star"></i></span></a>
                        </div>
                        <a href="#" class="email-list-detail">
                          <div class="content">
                            <span class="from">Vehicle Inspection</span>
                            <div class="description"> 
                              <p style="color: #7987a1; font-size: 0.8rem">{{ $notification->data['body'] }}</p>
                            </div>
                            
                          </div>
                          <span class="date">
                            {{ $notification->created_at->format('d-M-Y H:i') }}
                          </span>
                        </a>
                      </div>
                    </li>
                    @endif

                    @if ($notification->type == 'App\Notifications\InsuraceExpiredNotification')
                    <li>
                      <div class="email-list-item @if(!$notification->read_at) email-list-item--unread @endif">
                        <div class="email-list-actions">
                          <div class="form-check">
                            <input type="checkbox" class="form-check-input">
                          </div>
                          <a class="favorite" href="javascript:;"><span><i data-feather="star"></i></span></a>
                        </div>
                        <a href="#" class="email-list-detail">
                          <div class="content">
                            <span class="from">Expired Insurance</span>
                            <div class="description"> 
                              <p style="color: #7987a1; font-size: 0.8rem">{{ $notification->data['body'] }}</p>
                            </div>
                            
                          </div>
                          <span class="date">
                            {{ $notification->created_at->format('d-M-Y H:i') }}
                          </span>
                        </a>
                      </div>
                    </li>
                    @endif

                    @if ($notification->type == 'App\Notifications\InsuraceExpiredNotification')
                    <li>
                      <div class="email-list-item @if(!$notification->read_at) email-list-item--unread @endif">
                        <div class="email-list-actions">
                          <div class="form-check">
                            <input type="checkbox" class="form-check-input">
                          </div>
                          <a class="favorite" href="javascript:;"><span><i data-feather="star"></i></span></a>
                        </div>
                        <a href="#" class="email-list-detail">
                          <div class="content">
                            <span class="from">Expired Insurance</span>
                            <div class="description"> 
                              <p style="color: #7987a1; font-size: 0.8rem">{{ $notification->data['body'] }}</p>
                            </div>
                            
                          </div>
                          <span class="date">
                            {{ $notification->created_at->format('d-M-Y H:i') }}
                          </span>
                        </a>
                      </div>
                    </li>
                    @endif

                    @if ($notification->type == 'App\Notifications\SchoolTripArrivedNotification')
                    <li>
                      <div class="email-list-item @if(!$notification->read_at) email-list-item--unread @endif">
                        <div class="email-list-actions">
                          <div class="form-check">
                            <input type="checkbox" class="form-check-input">
                          </div>
                          <a class="favorite" href="javascript:;"><span><i data-feather="star"></i></span></a>
                        </div>
                        <a href="#" class="email-list-detail">
                          <div class="content">
                            <span class="from">Arrived At School Trip Destination</span>
                            <div class="description"> 
                              <p style="color: #7987a1; font-size: 0.8rem">{{ $notification->data['body'] }}</p>
                            </div>
                            
                          </div>
                          <span class="date">
                            {{ $notification->created_at->format('d-M-Y H:i') }}
                          </span>
                        </a>
                      </div>
                    </li>
                    @endif

                    @if ($notification->type == 'App\Notifications\SchoolTripDepatureNotification')
                    <li>
                      <div class="email-list-item @if(!$notification->read_at) email-list-item--unread @endif">
                        <div class="email-list-actions">
                          <div class="form-check">
                            <input type="checkbox" class="form-check-input">
                          </div>
                          <a class="favorite" href="javascript:;"><span><i data-feather="star"></i></span></a>
                        </div>
                        <a href="#" class="email-list-detail">
                          <div class="content">
                            <span class="from">Departed From School Trip</span>
                            <div class="description"> 
                              <p style="color: #7987a1; font-size: 0.8rem">{{ $notification->data['body'] }}</p>
                            </div>
                            
                          </div>
                          <span class="date">
                            {{ $notification->created_at->format('d-M-Y H:i') }}
                          </span>
                        </a>
                      </div>
                    </li>
                    @endif

                    @if ($notification->type == 'App\Notifications\SchoolTripGoingBackNotification')
                    <li>
                      <div class="email-list-item @if(!$notification->read_at) email-list-item--unread @endif">
                        <div class="email-list-actions">
                          <div class="form-check">
                            <input type="checkbox" class="form-check-input">
                          </div>
                          <a class="favorite" href="javascript:;"><span><i data-feather="star"></i></span></a>
                        </div>
                        <a href="#" class="email-list-detail">
                          <div class="content">
                            <span class="from">Going Back To School From School Trip</span>
                            <div class="description"> 
                              <p style="color: #7987a1; font-size: 0.8rem">{{ $notification->data['body'] }}</p>
                            </div>
                            
                          </div>
                          <span class="date">
                            {{ $notification->created_at->format('d-M-Y H:i') }}
                          </span>
                        </a>
                      </div>
                    </li>
                    @endif

                    @if ($notification->type == 'App\Notifications\SchoolTripReachedDestNotification')
                    <li>
                      <div class="email-list-item @if(!$notification->read_at) email-list-item--unread @endif">
                        <div class="email-list-actions">
                          <div class="form-check">
                            <input type="checkbox" class="form-check-input">
                          </div>
                          <a class="favorite" href="javascript:;"><span><i data-feather="star"></i></span></a>
                        </div>
                        <a href="#" class="email-list-detail">
                          <div class="content">
                            <span class="from">Reached School Trip Destination</span>
                            <div class="description"> 
                              <p style="color: #7987a1; font-size: 0.8rem">{{ $notification->data['body'] }}</p>
                            </div>
                            
                          </div>
                          <span class="date">
                            {{ $notification->created_at->format('d-M-Y H:i') }}
                          </span>
                        </a>
                      </div>
                    </li>
                    @endif

                    @if ($notification->type == 'App\Notifications\StopNotification')
                    <li>
                      <div class="email-list-item @if(!$notification->read_at) email-list-item--unread @endif">
                        <div class="email-list-actions">
                          <div class="form-check">
                            <input type="checkbox" class="form-check-input">
                          </div>
                          <a class="favorite" href="javascript:;"><span><i data-feather="star"></i></span></a>
                        </div>
                        <a href="#" class="email-list-detail">
                          <div class="content">
                            <span class="from">Vehicle Stopped</span>
                            <div class="description"> 
                              <p style="color: #7987a1; font-size: 0.8rem">{{ $notification->data['body'] }}</p>
                            </div>
                            
                          </div>
                          <span class="date">
                            {{ $notification->created_at->format('d-M-Y H:i') }}
                          </span>
                        </a>
                      </div>
                    </li>
                    @endif

                    @if ($notification->type == 'App\Notifications\VehicleOutOfFence')
                    <li>
                      <div class="email-list-item @if(!$notification->read_at) email-list-item--unread @endif">
                        <div class="email-list-actions">
                          <div class="form-check">
                            <input type="checkbox" class="form-check-input">
                          </div>
                          <a class="favorite" href="javascript:;"><span><i data-feather="star"></i></span></a>
                        </div>
                        <a href="#" class="email-list-detail">
                          <div class="content">
                            <span class="from">Vehicle Outside Geo Fence</span>
                            <div class="description"> 
                              <p style="color: #7987a1; font-size: 0.8rem">{{ $notification->data['body'] }}</p>
                            </div>
                            
                          </div>
                          <span class="date">
                            {{ $notification->created_at->format('d-M-Y H:i') }}
                          </span>
                        </a>
                      </div>
                    </li>
                    @endif

                    @if ($notification->type == 'App\Notifications\VehicleOutOfSchool')
                    <li>
                      <div class="email-list-item @if(!$notification->read_at) email-list-item--unread @endif">
                        <div class="email-list-actions">
                          <div class="form-check">
                            <input type="checkbox" class="form-check-input">
                          </div>
                          <a class="favorite" href="javascript:;"><span><i data-feather="star"></i></span></a>
                        </div>
                        <a href="#" class="email-list-detail">
                          <div class="content">
                            <span class="from">Vehicle Outside School</span>
                            <div class="description"> 
                              <p style="color: #7987a1; font-size: 0.8rem">{{ $notification->data['body'] }}</p>
                            </div>
                            
                          </div>
                          <span class="date">
                            {{ $notification->created_at->format('d-M-Y H:i') }}
                          </span>
                        </a>
                      </div>
                    </li>
                    @endif

                    @if ($notification->type == 'App\Notifications\WarrantyExpiredNotification')
                    <li>
                      <div class="email-list-item @if(!$notification->read_at) email-list-item--unread @endif">
                        <div class="email-list-actions">
                          <div class="form-check">
                            <input type="checkbox" class="form-check-input">
                          </div>
                          <a class="favorite" href="javascript:;"><span><i data-feather="star"></i></span></a>
                        </div>
                        <a href="#" class="email-list-detail">
                          <div class="content">
                            <span class="from">Expired Vehicle Warranty</span>
                            <div class="description"> 
                              <p style="color: #7987a1; font-size: 0.8rem">{{ $notification->data['body'] }}</p>
                            </div>
                            
                          </div>
                          <span class="date">
                            {{ $notification->created_at->format('d-M-Y H:i') }}
                          </span>
                        </a>
                      </div>
                    </li>
                    @endif
                 
                    @endforeach
                  </ul>
    
                </div>
              </div>
            </div>
  
            <div class="tab-pane fade " id="contact" role="tabpanel" aria-labelledby="contact-tab">
              <form action="{{route('pnotification_send')}}" method="post">
                @csrf
              <div>
                <div class="d-flex align-items-center p-3 border-bottom tx-16">
                  <span data-feather="edit" class="icon-md me-2"></span>
                  New message
                </div>
              </div>
              <div class="p-3 pb-0">
                <div class="to">
                  <div class="row mb-3">
                    <label class="col-md-2 col-form-label">To:</label>
                    <div class="col-md-10">
                      <select class="js-example-basic-single form-select" name="parent_id" style="width: 100%">
                        <option>select...</option>
                        @foreach ($users as $user)
                        <option value="{{$user->id}}">{{$user->name}}</option>
                        @endforeach
                      </select>
                    </div>
                  </div>
                </div>
                <div class="subject">
                  <div class="row mb-3">
                    <label class="col-md-2 col-form-label">Subject</label>
                    <div class="col-md-10">
                      <input class="form-control" type="text" name="msg_header">
                    </div>
                  </div>
                </div>
                
              </div>
              
                <div class="px-3">
                  <div class="col-md-12">
                    <div class="mb-3">
                      <label class="form-label visually-hidden" for="easyMdeEditor">Descriptions </label>
                      <textarea class="form-control" name="msg_body" id="easyMdeEditor" rows="5"></textarea>
                    </div>
                  </div>
                  <div>
                    <div class="col-md-12">
                      <button class="btn btn-success me-1 mb-1" type="submit"> Send</button>
                    </div>
                  </div>
                </div>
              
              </form>
            </div>
         
          </div>
          
        </div>
        
      </div>
    </div>
  </div>
</div>
@endsection

@push('plugin-scripts')
  <script src="{{ asset('assets/plugins/select2/select2.min.js') }}"></script>
  <script src="//cdnjs.cloudflare.com/ajax/libs/list.js/1.5.0/list.min.js"></script>
@endpush

@push('custom-scripts')

<script>
  $(function() {
    if ($(".js-example-basic-single").length) {
      $(".js-example-basic-single").select2();
    }

    var monkeyList = new List('test-list', {
      valueNames: ['description', 'from'],
      page: 10,
      pagination: true
    });

  })
</script>
@endpush
