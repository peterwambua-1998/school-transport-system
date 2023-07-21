<nav class="navbar">
  
  <a href="#" class="sidebar-toggler">
    <i data-feather="menu"></i>
  </a>
  <div class="navbar-content">
    <form class="search-form">
      <div class="input-group">
        @php
            $seg = request()->segments();
            $segment = $seg[0];
            $segone = $seg[1] ?? null;
            $segthree = $seg[2] ?? null;
        @endphp   
        @if ($segment == 'routes' && $segthree == 'edit')
            <h4>Update Route Details</h4>
        @endif
        @if ($segment == 'routes' && $segone == null)
            <h4>Routes</h4>
        @endif
        @if ($segment == 'staff'&& $segone == 'edit')
            <h4>Edit Staff Details</h4>
        @endif
        @if ($segment == 'students') {{-- snging --}}
            <h4>Students</h4>
        @endif
        @if ($segment == 'parents')
            <h4>Parents</h4>
        @endif
      </div>
    </form>
    <ul class="navbar-nav">
      <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" href="#" id="languageDropdown" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          <img src="{{ url('assets/images/flags/us.svg') }}" class="wd-20 me-1" title="us" alt="us"> <span class="ms-1 me-1 d-none d-md-inline-block">English</span>
        </a>
        <div class="dropdown-menu" aria-labelledby="languageDropdown">
          <a href="javascript:;" class="dropdown-item py-2"> <img src="{{ url('assets/images/flags/us.svg') }}" class="wd-20 me-1" title="us" alt="us"> <span class="ms-1"> English </span></a>
          <a href="javascript:;" class="dropdown-item py-2"> <img src="{{ url('assets/images/flags/fr.svg') }}" class="wd-20 me-1" title="fr" alt="fr"> <span class="ms-1"> French </span></a>
          <a href="javascript:;" class="dropdown-item py-2"> <img src="{{ url('assets/images/flags/de.svg') }}" class="wd-20 me-1" title="de" alt="de"> <span class="ms-1"> German </span></a>
          <a href="javascript:;" class="dropdown-item py-2"> <img src="{{ url('assets/images/flags/pt.svg') }}" class="wd-20 me-1" title="pt" alt="pt"> <span class="ms-1"> Portuguese </span></a>
          <a href="javascript:;" class="dropdown-item py-2"> <img src="{{ url('assets/images/flags/es.svg') }}" class="wd-20 me-1" title="es" alt="es"> <span class="ms-1"> Spanish </span></a>
        </div>
      </li>
      
      
      <li class="nav-item dropdown">
        <?php
          $user = Auth::user();
          $notifs = $user->unreadNotifications->take(5);
        ?>
        <a class="nav-link dropdown-toggle" href="#" id="notificationDropdown" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          <i data-feather="bell"></i>
          <div class="indicator">
            <div class="circle"></div>
          </div>
        </a>
        <div class="dropdown-menu p-0" aria-labelledby="notificationDropdown">
          <div class="px-3 py-2 d-flex align-items-center justify-content-between border-bottom">
            <p>{{count($notifs)}} New Notifications</p>
            <a href="{{route('delete_user_notifications')}}" class="text-muted">Clear all</a>
          </div>
          <div class="p-1">
                
           @foreach ($notifs as $item)
           <a href="{{url('/notification/seenotify')}}" class="dropdown-item d-flex align-items-center py-2">
            <div class="wd-30 ht-30 d-flex align-items-center justify-content-center bg-primary rounded-circle me-3">
              <i class="icon-sm text-white" data-feather="alert-circle"></i>
            </div>
            <div class="flex-grow-1 me-2">
              @if ($item)
              
              <p>{{Str::limit($item->data['body'], 20)}}</p>
              <p class="tx-12 text-muted">{{ $item->created_at->format('d-m-Y H:i') }}</p>
            
            @endif
              
            </div>	
          </a>
           @endforeach
            
            
            
            
          </div>
          <div class="px-3 py-2 d-flex align-items-center justify-content-center border-top">
            <a href="{{url('/notification/seenotify')}}">View all</a>
          </div>
        </div>
      </li>
      <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" href="#" id="profileDropdown" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          @if (Auth::user()->image)
            <!-- Profile picture image-->
            <img class="wd-30 ht-30 rounded-circle" src="{{ asset('store/'.Auth::user()->image) }}" alt="">
          @else
            @if (Auth::user()->gender == 'male')
            <img class="wd-30 ht-30 rounded-circle" src="{{ url('https://cdn-icons-png.flaticon.com/512/9875/9875255.png') }}" alt="profile">
            @else
            <img class="wd-30 ht-30 rounded-circle" src="{{ url('https://cdn-icons-png.flaticon.com/512/9875/9875392.png') }}" alt="profile">
            @endif
          @endif
        </a>
        <div class="dropdown-menu p-0" aria-labelledby="profileDropdown">
          <div class="d-flex flex-column align-items-center border-bottom px-5 py-3">
            <div class="mb-3">
              @if (Auth::user()->image)
                <!-- Profile picture image-->
                <img class="wd-80 ht-80 rounded-circle" src="{{ asset('store/'.Auth::user()->image) }}" alt="">
              @else
              @if (Auth::user()->gender == 'male')
              <img class="wd-80 ht-80 rounded-circle" src="{{ url('https://cdn-icons-png.flaticon.com/512/9875/9875255.png') }}" alt="">
              @else
              <img class="wd-80 ht-80 rounded-circle" src="{{ url('https://cdn-icons-png.flaticon.com/512/9875/9875392.png') }}" alt="">
              @endif
                
              @endif
            </div>
            <div class="text-center">
              <p class="tx-16 fw-bolder">{{ Auth::user()->name}}</p>
              <p class="tx-12 text-muted">{{Auth::user()->email}}</p>
            </div>
          </div>
          <ul class="list-unstyled p-1">
            <li class="dropdown-item py-2">
              <a href="{{ route('profile_page', Crypt::encrypt(Auth::user()->id)) }}" class="text-body ms-0">
                <i class="me-2 icon-md" data-feather="user"></i>
                <span>Profile</span>
              </a>
            </li>
            <li class="dropdown-item py-2">
              <a href="{{route('profile_show', Crypt::encrypt(Auth::user()->id))}}" class="text-body ms-0">
                <i class="me-2 icon-md" data-feather="edit"></i>
                <span>Edit Profile</span>
              </a>
            </li>
            <li class="dropdown-item py-2">
              <a href="{{ route('logout') }}" class="text-body ms-0"  onclick="event.preventDefault();
              document.getElementById('logout-form').submit();">
                <i class="me-2 icon-md" data-feather="log-out"></i>
                <span>Log Out</span>
              </a>

              <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                  @csrf
              </form>
            </li>
          </ul>
        </div>
      </li>
    </ul>
  </div>
</nav>