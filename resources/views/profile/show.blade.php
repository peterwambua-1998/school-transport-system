@extends('layouts.app')
@section('css')

<style>

</style>
@endsection
@section('content')

<nav class="page-breadcrumb" style="display:flex">
    <ol class="breadcrumb" style="width: 85%">
      <li class="breadcrumb-item"><a href="#">Profile</a></li>
    </ol>
  
    <div style="width: 15%">
        <a class="btn btn-warning" style="float: right;border-radius:5px" href="{{ route('profile_page',  Crypt::encrypt(Auth::user()->id)) }}"><ion-icon style="position: relative; top:3px; right: 5px; color: #000;font-size:16px;" name="close-circle-outline"></ion-icon> Cancel</a>
    </div>
</nav>

<div class="container">
    @if (session('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
    </div>
@endif
    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif
</div>
<form action="{{ route('profile_update', Crypt::encrypt($userProfile->id)) }}" method="post" >
    @csrf
<div class="container rounded bg-white mb-5">
    <div class="row">
        
        <div class="col-md-12 border-right">
            <div class="p-3 py-5">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="text-right">Update Profile</h5>
                </div>
                <hr>

                <div class="row mt-3">
                    
                   
                    <div class="col-md-6">
                        <label class="form-label" for="">Email</label>
                        <input id="current-password" type="email"  class="form-control" name="email" autocomplete="false" value="{{$userProfile->email}}">
                    </div>
                    
                    <div class="col-md-6">
                        <label class="form-label" for="new-password">Phone Number</label>
                        <input id="new-password" type="text" class="form-control" name="phone_num"  autocomplete="off" value="{{$userProfile->phone_num}}">
                    </div>

                    
                </div>
                
                <div class="mt-5 text-center"><button class="btn btn-success profile-button btn-block" type="submit"><ion-icon style="position: relative; top:2px; right: 5px; color: #fff;" name="save"></ion-icon> Update Profile</button></div>
            </div>
        </div>
        
    </div>
</div>
</div>
</div>
</form> 

@endsection


@section('js')
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    
    <script defer>
        function myFunction() {
                var x = document.getElementById("myInput");
                if (x.type === "password") {
                    x.type = "text";
                } else {
                    x.type = "password";
                }
            }
        $(document).ready( function () {

            
            

            


            
            
        } );



    </script>
@endsection