
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
@section('content')

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
                <a href="#" class="noble-ui-logo d-block mb-2">Proj<span>Trac</span></a>
                <h5 class="text-muted fw-normal mb-4">{{ __('Reset Password') }}</h5>
                @if (session('status'))
                    <div class="alert alert-success" role="alert">
                        {{ session('status') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('password.update') }}">
                    @csrf

                    <input type="hidden" name="token" value="{{ $token }}">

                    <div class="mb-3">
                        <label for="email" class="form-label">{{ __('Email Address') }}</label>

                        <div class="">
                            <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" placeholder="johndoe@mail.com" autofocus>

                            @error('email')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">{{ __('Password') }}</label>

                        <div class="password-input">
                            <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="new-password">
                            <span class="eye-icon">
                                <ion-icon name="eye" onclick="myFunction()"></ion-icon>
                            </span>
                        </div>
                            @error('password')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                       
                    </div>

                    <div class="mb-3">
                        <label for="password-confirm" class="form-label">{{ __('Confirm Password') }}</label>

                        <div class="password-input">
                            <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required autocomplete="new-password">
                            <span class="eye-icon">
                                <ion-icon name="eye" onclick="myFunctionTwo()"></ion-icon>
                            </span>
                        </div>
                    </div>
                    <div>
                        <button type="submit" class="btn btn-primary">
                            <ion-icon name="log-in-outline" style="font-size: 16px; position: relative; top: 3px; right: 5px;"></ion-icon> {{ __('Reset Password') }}
                        </button>
                    </div>

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
    function myFunction() {
        var x = document.getElementById("password");
        if (x.type === "password") {
            x.type = "text";
        } else {
            x.type = "password";
        }
    }

    function myFunctionTwo() {
        var x = document.getElementById("password-confirm");
        if (x.type === "password") {
            x.type = "text";
        } else {
            x.type = "password";
        }
    }
  </script>
@endpush

