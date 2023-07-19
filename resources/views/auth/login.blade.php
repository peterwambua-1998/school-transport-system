@extends('layouts.master2')


@push('plugin-styles')
<style>
  .my-alerts {
    width: 50vw;
    margin: 0 auto;
  }

  .password-input {
  position: relative;
}

.password-input .eye-icon {
  position: absolute;
  top: 50%;
  right: 10px;
  transform: translateY(-50%);
  cursor: pointer;
}

.password-input .eye-icon ion-icon {
  font-size: 20px;
}

</style>
@endpush

@section('content')


@if (Session::has('errors'))
<div class="my-alerts mt-2">
  <div class="alert alert-danger" role="alert" id="danger">
    <ul>
      @foreach ($errors->all() as $error)
          <li>{{ $error }}</li>
      @endforeach
    </ul>
  </div>
</div>  
@endif

@error('email')
<span class="invalid-feedback" role="alert">
    <strong>{{ $message }}</strong>
</span>
@enderror

@error('password')
<span class="invalid-feedback" role="alert">
    <strong>{{ $message }}</strong>
</span>
@enderror

<div class="page-content d-flex align-items-center justify-content-center">
  
  <div class="row w-100 mx-0 auth-page">
    <div class="col-md-8 col-xl-6 mx-auto">
      <div class="card">
        <div class="row">
          <div class="col-md-4 pe-md-0">
            <div class="auth-side-wrapper" style="background-image: url({{ asset('images/school.jpg') }})">

            </div>
          </div>
          <div class="col-md-8 ps-md-0">
            <div class="auth-form-wrapper px-4 py-5">
              <a href="#" class="noble-ui-logo d-block mb-2">
                <span style="color:#fbbc06;font-weight:bold">m</span><span style="color: green;font-weight:bold">Fika</span>
              </a>
              <h5 class="text-muted fw-normal mb-4">Welcome back! Log in to your account.</h5>
              <form class="forms-sample" method="POST" action="{{ route('login') }}">
                @csrf

                <div class="mb-3">
                  <label for="userEmail" class="form-label">Email address</label>
                  <input type="email" class="form-control" id="userEmail" placeholder="Email" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>
                  @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                </div>
                <div class="mb-3">
                  <label for="userPassword" class="form-label">Password</label>
                  <div class="password-input">
                    <input type="password" class="form-control" placeholder="Password" name="password" required autocomplete="current-password" id="myInput">
                    <span class="eye-icon">
                      <ion-icon name="eye" id="view-password"></ion-icon>
                      <ion-icon name="eye-off-outline" id="hide-password"></ion-icon>
                    </span>
                  </div>
                  
                  @error('password')
                  <span class="invalid-feedback" role="alert">
                      <strong>{{ $message }}</strong>
                  </span>
                  @enderror
                </div>
                <div>
                  <button type="submit" class="btn btn-primary me-2 mb-2 mb-md-0"><ion-icon name="log-in-outline" style="font-size: 16px; position: relative; top: 3px; right: 5px;"></ion-icon> Login</a>
                  {{--
                  <button type="button" class="btn btn-outline-primary btn-icon-text mb-2 mb-md-0">
                    <i class="btn-icon-prepend" data-feather="twitter"></i>
                    Login with twitter
                  </button>
                  --}}
                </div>
                <a href="{{ url('/password/reset') }}" class="d-block mt-3 text-muted">Forgot Password?</a>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

@include('layouts.footer')
@endsection

@push('custom-scripts')
  <script defer>

    $(function() {
      $('#hide-password').hide();
      $('#view-password').on('click',myFunction);
      $('#hide-password').on('click',myFunction);

      function myFunction() {
          var x = document.getElementById("myInput");
          if (x.type === "password") {
              x.type = "text";
              $('#view-password').hide();
              $('#hide-password').show();
          } else {
              x.type = "password";
              $('#view-password').show();
              $('#hide-password').hide();
          }
      }
    })
    
  </script>
@endpush

{{---
@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Login') }}</div>

                <div class="card-body">
                    <form method="POST" action="{{ route('login') }}">
                        @csrf

                        <div class="row mb-3">
                            <label for="email" class="col-md-4 col-form-label text-md-end">{{ __('Email Address') }}</label>

                            <div class="col-md-6">
                                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>

                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="password" class="col-md-4 col-form-label text-md-end">{{ __('Password') }}</label>

                            <div class="col-md-6">
                                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password">

                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6 offset-md-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>

                                    <label class="form-check-label" for="remember">
                                        {{ __('Remember Me') }}
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-0">
                            <div class="col-md-8 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    {{ __('Login') }}
                                </button>

                                @if (Route::has('password.request'))
                                    <a class="btn btn-link" href="{{ route('password.request') }}">
                                        {{ __('Forgot Your Password?') }}
                                    </a>
                                @endif
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
--}}