<nav class="sidebar ">
  <div class="sidebar-header">
    <a href="#" class="sidebar-brand">
      <span style="color:#fbbc06;font-weight:bold">m</span><span style="color: green;font-weight:bold">Fika</span>
    </a>
    <div id="close-sidebar" class="sidebar-toggler not-active">
      <span></span>
      <span></span>
      <span></span>
    </div>
  </div>
  @php
      $userRole = Auth::user()->role;
  @endphp
  <div class="sidebar-body">
    <ul class="nav">
      {{--- admin dashboard ---}}
      @if (Auth::user()->user_type == 'office staff' || Auth::user()->user_type == 'supervisor'  || Auth::user()->user_type == 'head teacher'||  Auth::user()->user_type == 'admin' || Auth::user()->user_type == 'director')
        <li class="nav-item nav-category">Main</li>
        
        <li class="nav-item {{ active_class(['home']) }}">
          <a href="{{ url('home') }}" class="nav-link">
            <ion-icon class="link-icon" name="home-outline" ></ion-icon>
            <span class="link-title">Dashboard</span>
          </a>
        </li>

      @endif


    
      @if (Auth::user()->user_type == 'office staff' || Auth::user()->user_type == 'admin' || Auth::user()->user_type == 'supervisor'  || Auth::user()->user_type == 'head teacher')
        <li class="nav-item {{ active_class(['students']) }} {{ active_class(['students/*']) }} {{ active_class(['allocation/*']) }} {{ active_class(['allocation-dropoff/*']) }}">
          <a href="{{ route('students.index') }}" class="nav-link">
            <ion-icon class="link-icon" name="accessibility-outline"></ion-icon>
            <span class="link-title">Students</span>
          </a>
        </li>

        <li class="nav-item {{ active_class(['parents']) }} {{active_class(['parents/create'])}} {{active_class(['parents/*/edit'])}}">
          <a href="{{ route('parents.index') }}" class="nav-link">
            <ion-icon class="link-icon" name="people-outline"></ion-icon>
            <span class="link-title">Parents</span>
          </a>
        </li>
        
        <li class="nav-item {{ active_class(['vehicles/*']) }} {{ active_class(['edit-fence/*']) }} {{ active_class(['trips/*']) }} {{ active_class(['vehicles']) }} {{ active_class(['routes']) }} {{ active_class(['routes/*']) }} {{ active_class(['show-geo-fence/*']) }} {{ active_class(['polyline-edit/*']) }}  {{ active_class(['zones']) }} {{ active_class(['zones/*']) }} {{ active_class(['zone/geofence/*']) }} {{ active_class(['zone/edit-geofence/*']) }} {{ active_class(['tracker/*']) }}">
          <a class="nav-link" data-bs-toggle="collapse" href="#vehicles" role="button" aria-expanded="{{ is_active_route(['trips/*']) }} {{ is_active_route(['vehicles']) }} {{ is_active_route(['polyline-edit/*']) }} {{ is_active_route(['edit-fence/*']) }} {{ is_active_route(['show-geo-fence/*']) }} {{ is_active_route(['vehicles/*']) }} {{ is_active_route(['routes/*']) }} {{ is_active_route(['routes']) }} {{ is_active_route(['zones']) }} {{ is_active_route(['zones/*']) }} {{ is_active_route(['zone/geofence/*']) }} {{ is_active_route(['zone/edit-geofence/*']) }} {{ is_active_route(['tracker/*']) }} " aria-controls="vehicles">
            <ion-icon class="link-icon" name="bus-outline"></ion-icon>
            <span class="link-title">Vehicles </span>
            <i class="link-arrow" data-feather="chevron-down"></i>
          </a>
          <div class="collapse {{ show_class(['vehicles']) }} {{ show_class(['edit-fence/*']) }} {{ show_class(['trips/*']) }} {{ show_class(['show-geo-fence/*']) }} {{ show_class(['polyline-edit/*']) }} {{ show_class(['vehicles/*']) }} {{ show_class(['routes']) }} {{ show_class(['routes/*']) }}  {{ show_class(['zones']) }} {{ show_class(['zones/*']) }} {{ show_class(['zone/geofence/*']) }} {{ show_class(['zone/edit-geofence/*']) }} {{ show_class(['tracker/*']) }}" id="vehicles">
            <ul class="nav sub-menu">
              <li class="nav-item">
                <a href="{{ route('vehicles.index') }}" class="nav-link {{ active_class(['vehicles/*']) }}{{ active_class(['edit-fence/*']) }} {{ active_class(['trips/*']) }} {{ active_class(['vehicles']) }} {{ active_class(['tracker/*']) }}">Buses</a>
              </li>
              <li class="nav-item">
                <a href="{{ route('routes.index') }}"  class="nav-link {{ active_class(['routes/*']) }} {{ active_class(['routes']) }} {{ active_class(['show-geo-fence/*']) }} {{ active_class(['polyline-edit/*']) }}">Routes</a>
              </li>
              <li class="nav-item">
                <a href="{{ route('zones.index') }}"  class="nav-link {{ active_class(['zones/*']) }} {{ active_class(['zones']) }} {{ active_class(['zone/geofence/*']) }} {{ active_class(['zone/edit-geofence/*']) }}">Zones</a>
              </li>
              
            </ul>
          </div>
        </li>
        

        <li class="nav-item {{ active_class(['term_events']) }} {{ active_class(['schooltripst/showmytrips/show/*']) }} {{ active_class(['schooltripst/add-students/*']) }} {{ active_class(['term_events/*']) }} {{ active_class(['schooltrips']) }} {{ active_class(['schooltrips/*']) }} {{ active_class(['driver-myschooltrips/schooltrips/schooltripshow/*']) }} {{ active_class(['school-trip/*']) }} {{ active_class(['term_holiday']) }} {{ active_class(['term_holiday/*']) }} {{ active_class(['term']) }} {{ active_class(['term/*']) }} {{ active_class(['school-fees']) }} {{ active_class(['school-fees/create/*']) }} {{ active_class(['school-fees/*']) }}">
          <a class="nav-link" data-bs-toggle="collapse" href="#term" role="button" aria-expanded="{{ is_active_route(['schooltripst/add-students/*']) }}{{ is_active_route(['schooltripst/showmytrips/show/']) }} {{ is_active_route(['term_events']) }} {{ is_active_route(['term_events/*']) }} {{ is_active_route(['schooltrips']) }} {{ is_active_route(['schooltrips/*']) }} {{ is_active_route(['driver-myschooltrips/schooltrips/schooltripshow/*']) }} {{ is_active_route(['school-trip/*']) }} {{ is_active_route(['term_holiday']) }} {{ is_active_route(['term_holiday/*']) }} {{ is_active_route(['term']) }} {{ is_active_route(['term/*']) }} {{ is_active_route(['school-fees']) }} {{ is_active_route(['school-fees/create/*']) }} {{ is_active_route(['school-fees/*']) }}" aria-controls="term">
            <ion-icon class="link-icon" name="calendar-outline"></ion-icon>
            <span class="link-title">School Term</span>
            <i class="link-arrow" data-feather="chevron-down"></i>
          </a>
          {{-- schooltripst/add-students/ --}}
          <div class="collapse {{ show_class(['term_events']) }} {{ show_class(['schooltripst/add-students/*']) }} {{ show_class(['schooltripst/showmytrips/show/*']) }} {{ show_class(['term_events/*']) }} {{ show_class(['schooltrips']) }} {{ show_class(['schooltrips/*']) }} {{ show_class(['driver-myschooltrips/schooltrips/schooltripshow/*']) }} {{ show_class(['school-trip/*']) }} {{ show_class(['term_holiday']) }} {{ show_class(['term_holiday/*']) }} {{ show_class(['term']) }} {{ show_class(['term/*']) }} {{ show_class(['school-fees']) }} {{ show_class(['school-fees/create/*']) }} {{ show_class(['school-fees/*']) }}" id="term">
            <ul class="nav sub-menu">
              <li class="nav-item">
                <a href="{{ route('term.index') }}" class="nav-link {{ active_class(['term']) }} {{ active_class(['term/*']) }}">School Term</a>
              </li>
              <li class="nav-item">
                <a href="{{ route('school-fees.index') }}"  class="nav-link {{ active_class(['school-fees']) }} {{ active_class(['school-fees/create/*']) }} {{ active_class(['school-fees/*']) }}">School Fees</a>
              </li>
              <li class="nav-item">
                <a href="{{ route('term_holiday.index') }}"  class="nav-link {{ active_class(['term_holiday']) }} {{ active_class(['term_holiday/*']) }}">Term Holidays</a>
              </li>
              <li class="nav-item">
                <a href="{{ route('schooltrips.index') }}"  class="nav-link {{ active_class(['schooltrips']) }} {{ active_class(['schooltripst/add-students/*']) }} {{ active_class(['schooltrips/*']) }} {{ active_class(['schooltripst/showmytrips/show/*']) }} {{ active_class(['driver-myschooltrips/schooltrips/schooltripshow/*']) }} {{ active_class(['school-trip/*']) }}">School Trips</a>
              </li>
              <li class="nav-item">
                <a href="{{ route('term_events.index') }}"  class="nav-link {{ active_class(['term_events']) }} {{ active_class(['term_events/*']) }}">Term Events</a>
              </li>
            </ul>
          </div>
        </li>

        <li class="nav-item {{ active_class(['grades']) }} {{ active_class(['group/view']) }} {{ active_class(['grade/view']) }} {{ active_class(['stream/view']) }} {{ active_class(['group/view/edit/*']) }} {{ active_class(['grade/view/edit/*']) }} {{ active_class(['stream/view/edit/*']) }}">
          <a href="{{ route('grades_page') }}" class="nav-link">
            <ion-icon class="link-icon" name="business-outline"></ion-icon>
            <span class="link-title">
              @if ($tr)
              {{ucfirst($tr->plural) ?? 'Grades'}}
              @else
              Grades
              @endif
            </span>
          </a>
        </li>

        <li class="nav-item {{ active_class(['reviews/show']) }} {{ active_class(['attendances/*']) }}">
          <a class="nav-link" data-bs-toggle="collapse" href="#attendance" role="button" aria-expanded="{{ is_active_route(['reviews/show']) }} {{ is_active_route(['attendances/*']) }}" aria-controls="attendance">
            <ion-icon class="link-icon" name="person-outline"></ion-icon>
            <span class="link-title">Attendance</span>
            <i class="link-arrow" data-feather="chevron-down"></i>
          </a>
          <div class="collapse {{ show_class(['reviews/show']) }} {{ show_class(['attendances/*']) }}" id="attendance">
            <ul class="nav sub-menu">
              <li class="nav-item">
                <a href="{{ route('attr_list') }}" class="nav-link {{ active_class(['attendances/list']) }}">Todays checklist</a>
              </li>
              <li class="nav-item">
                <a href="{{ route('absenttodaystd') }}" class="nav-link {{ active_class(['attendances/absent-today']) }}">Absent Today</a>
              </li>
              <li class="nav-item">
                <a href="{{ route('review_show') }}" class="nav-link {{ active_class(['reviews/show']) }}">Reviews</a>
              </li>
            </ul>
          </div>
        </li>


        <li class="nav-item {{ active_class(['claims/*']) }}  {{ active_class(['insurance']) }} {{ active_class(['license']) }} {{ active_class(['license/*']) }} {{ active_class(['insurance/*']) }} {{ active_class(['inspection']) }} {{ active_class(['inspection/*']) }}">
          <a class="nav-link" data-bs-toggle="collapse" href="#compliance" role="button" aria-expanded="{{ is_active_route(['claims/*']) }} {{ is_active_route(['license/*']) }} {{ is_active_route(['license/*']) }} {{ is_active_route(['insurance']) }} {{ is_active_route(['insurance/*']) }} {{ is_active_route(['inspection']) }} {{ is_active_route(['inspection/*']) }}" aria-controls="compliance">
            <ion-icon class="link-icon" name="document-text-outline"></ion-icon>
            <span class="link-title">Compliance</span>
            <i class="link-arrow" data-feather="chevron-down"></i>
          </a>
          <div class="collapse {{ show_class(['inspection']) }} {{ show_class(['license']) }} {{ show_class(['license/*']) }} {{ show_class(['inspection/*']) }} {{ show_class(['claims/*']) }} {{ show_class(['insurance']) }} {{ show_class(['insurance/*']) }}" id="compliance">
            <ul class="nav sub-menu">
              <li class="nav-item">
                <a href="{{ route('insurance.index') }}"  class="nav-link {{ active_class(['insurance']) }} {{ active_class(['insurance/*']) }} {{ active_class(['claims/*']) }}">Insurance</a>
              </li>
              <li class="nav-item">
                <a href="{{ route('license.index') }}"  class="nav-link {{ active_class(['license']) }} {{ active_class(['license/*']) }}">Driver License</a>
              </li>
              <li class="nav-item">
                <a href="{{ route('inspection.index') }}"  class="nav-link {{ active_class(['inspection']) }} {{ active_class(['inspection/*']) }}">Inspection</a>
              </li>
            </ul>
          </div>
        </li>

        <li class="nav-item {{ active_class(['staff']) }} {{active_class(['staff/*/edit'])}} {{active_class(['staff/*'])}}">
          <a href="{{ route('staff_index') }}" class="nav-link">
            <ion-icon class="link-icon" name="bag-handle-outline"></ion-icon>
            <span class="link-title">Staff</span>
          </a>
        </li>


        <li class="nav-item {{ active_class(['maintenance']) }}  {{ active_class(['maintenance/*']) }} {{ active_class(['warranty']) }} {{ active_class(['warranty/*']) }} {{ active_class(['warranty-claims/*']) }} {{ active_class(['garage']) }} {{ active_class(['garage/*']) }}">
          <a class="nav-link" data-bs-toggle="collapse" href="#maintenance" role="button" aria-expanded="{{ is_active_route(['maintenance']) }} {{ is_active_route(['maintenance/*']) }} {{ is_active_route(['garage']) }} {{ is_active_route(['garage/*']) }} {{ is_active_route(['warranty']) }} {{ is_active_route(['warranty/*']) }} {{ is_active_route(['warranty-claims/*']) }} " aria-controls="maintenance">
            <ion-icon class="link-icon" name="build-outline"></ion-icon>
            <span class="link-title">Maintenance</span>
            <i class="link-arrow" data-feather="chevron-down"></i>
          </a>
          <div class="collapse {{ show_class(['maintenance']) }} {{ show_class(['maintenance/*']) }} {{ show_class(['garage']) }} {{ show_class(['garage/*']) }} {{ show_class(['warranty']) }} {{ show_class(['warranty/*']) }} {{ show_class(['warranty-claims/*']) }}" id="maintenance">
            <ul class="nav sub-menu">
              <li class="nav-item">
                <a href="{{ route('maintenance_index') }}"  class="nav-link {{ active_class(['maintenance']) }} {{ active_class(['maintenance/*']) }}">Maintenance</a>
              </li>
              <li class="nav-item">
                <a href="{{ route('garage.index') }}" class="nav-link {{ active_class(['garage']) }} {{ active_class(['garage/*']) }}">Garage</a>
              </li>
              <li class="nav-item ">
                <a href="{{ route('warranty.index') }}" class="nav-link {{ active_class(['warranty']) }} {{ active_class(['warranty/*']) }} {{ active_class(['warranty-claims/*']) }}">Warranty</a>
              </li>
            </ul>
          </div>
        </li>


        <li class="nav-item {{ active_class(['incidents']) }} {{ active_class(['incidents/*']) }} {{ active_class(['offence']) }} {{ active_class(['offence/*']) }}">
          <a class="nav-link" data-bs-toggle="collapse" href="#incidents" role="button" aria-expanded="  {{ is_active_route(['incidents']) }} {{ is_active_route(['incidents/*']) }} {{ is_active_route(['offence']) }} {{ is_active_route(['offence/*']) }}" aria-controls="incidents">
            <ion-icon class="link-icon" name="bandage-outline"></ion-icon>
            <span class="link-title">Occurrences</span>
            <i class="link-arrow" data-feather="chevron-down"></i>
          </a>
          <div class="collapse {{ show_class(['incidents']) }} {{ show_class(['incidents/*']) }} {{ show_class(['offence']) }} {{ show_class(['offence/*']) }}" id="incidents">
            <ul class="nav sub-menu">
              <li class="nav-item">
                <a href="{{ route('incidents.index') }}" class="nav-link {{ active_class(['incidents']) }} {{ active_class(['incidents/*']) }}">Incidents</a>
              </li>
              <li class="nav-item">
                <a href="{{ route('offence.index') }}" class="nav-link {{ active_class(['offence']) }} {{ active_class(['offence/*']) }}">Offences</a>
              </li>
            </ul>
          </div>
        </li>

        

       

        <li class="nav-item {{ active_class(['notification/*']) }}">
          <a href="{{ route('notification_get') }}" class="nav-link">
            <ion-icon class="link-icon" name="notifications-outline"></ion-icon>
            <span class="link-title">Notifications</span>
          </a>
        </li>

  

       

        
       

      @endif

      @if (Auth::user()->user_type == 'admin' || Auth::user()->user_type == 'supervisor')
      <li class="nav-item {{ active_class(['settings/*']) }} {{ active_class(['terminology/create']) }} {{ active_class('notification-settings/create') }} {{ active_class('sms-settings') }}" >
        <a class="nav-link" data-bs-toggle="collapse" href="#settings" role="button" aria-expanded="{{ is_active_route(['sms-settings']) }} {{ is_active_route(['terminology/create']) }} {{ is_active_route('notification-settings/create') }} {{ is_active_route(['settings/*']) }}" aria-controls="email">
          <ion-icon class="link-icon" name="settings-outline"></ion-icon>
          <span class="link-title">Settings</span>
          <i class="link-arrow" data-feather="chevron-down"></i>
        </a>
        <div class="collapse {{ show_class(['terminology/create']) }} {{ show_class(['settings/*']) }} {{ show_class(['sms-settings']) }} {{ show_class('notification-settings/create') }}" id="settings">
          <ul class="nav sub-menu">
            <li class="nav-item">
              <a href="{{ route('settings_create') }}" class="nav-link {{ active_class(['settings/create']) }}">System</a>
            </li>
           
            <li class="nav-item">
              <a href="{{ route('get_app_links') }}" class="nav-link {{ active_class(['settings/app-link']) }}">App Links</a>
            </li>
            <li class="nav-item">
              <a href="{{ url('settings/payment') }}" class="nav-link {{ active_class(['settings/payment']) }}">Payment</a>
            </li>
            <li class="nav-item">
              <a href="{{ url('notification-settings/create') }}" class="nav-link {{ active_class(['notification-settings/create']) }}">Notification</a>
            </li>
            <li class="nav-item">
              <a href="{{ url('terminology/create') }}" class="nav-link {{ active_class(['terminology/create']) }}">Terminologies</a>
            </li>
            <li class="nav-item">
              <a href="{{ route('sms_settings') }}" class="nav-link {{ active_class(['sms-settings']) }}">Sms</a>
            </li>
          </ul>
        </div>
      </li>

      @endif

      {{-- Reports 
      <li class="nav-item {{ active_class(['settings/*']) }}">
        <a class="nav-link" data-bs-toggle="collapse" href="#reports" role="button" aria-expanded="{{ is_active_route(['terminology/create']) }}" aria-controls="reports">
          <ion-icon class="link-icon" name="settings-outline"></ion-icon>
          <span class="link-title">Report</span>
          <i class="link-arrow" data-feather="chevron-down"></i>
        </a>
        <div class="collapse {{ show_class(['terminology/create']) }}" id="reports">
          <ul class="nav sub-menu">
            <li class="nav-item">
              <a href="{{ route('trip_report') }}" class="nav-link {{ active_class(['settings/create']) }}">Trips</a>
            </li>
           
            
          </ul>
        </div>
      </li>
      --}}

      @if (Auth::user()->user_type == 'teacher' )
      <li class="nav-item {{ active_class(['schoolattendance/create']) }}">
        <a href="{{ route('schoolattcreate') }}" class="nav-link">
          <ion-icon class="link-icon" name="document-text-outline"></ion-icon>
          <span class="link-title">Mark Attendance</span>
        </a>
      </li>

      <li class="nav-item {{ active_class(['school-attendance']) }}">
        <a href="{{ route('school-attendance.index') }}" class="nav-link">
          <ion-icon class="link-icon" name="clipboard-outline"></ion-icon>
          <span class="link-title">Attendance List</span>
        </a>
      </li>

      <li class="nav-item {{ active_class(['schooltripst/*']) }}">
        <a href="{{ route('teachertrips') }}" class="nav-link">
          <ion-icon class="link-icon" name="bicycle-outline"></ion-icon>
          <span class="link-title">School Trips</span>
        </a>
      </li>

      <li class="nav-item {{ active_class(['event']) }}">
        <a href="{{ route('event') }}" class="nav-link">
          <ion-icon class="link-icon" name="sparkles-outline"></ion-icon>
          <span class="link-title">Events</span>
        </a>
      </li>
      @endif
      
    </ul>
  </div>
</nav>
