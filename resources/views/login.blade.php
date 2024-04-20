@extends('layouts.index')
@section('title', 'Login')


<style>
@-webkit-keyframes shake-horizontal {
    0%,
    100% {
        -webkit-transform: translateX(0);
                transform: translateX(0);
    }
    10%,
    30%,
    50%,
    70% {
        -webkit-transform: translateX(-10px);
                transform: translateX(-10px);
    }
    20%,
    40%,
    60% {
        -webkit-transform: translateX(10px);
                transform: translateX(10px);
    }
    80% {
        -webkit-transform: translateX(8px);
                transform: translateX(8px);
    }
    90% {
        -webkit-transform: translateX(-8px);
                transform: translateX(-8px);
    }
    }
    @keyframes shake-horizontal {
    0%,
    100% {
        -webkit-transform: translateX(0);
                transform: translateX(0);
    }
    10%,
    30%,
    50%,
    70% {
        -webkit-transform: translateX(-10px);
                transform: translateX(-10px);
    }
    20%,
    40%,
    60% {
        -webkit-transform: translateX(10px);
                transform: translateX(10px);
    }
    80% {
        -webkit-transform: translateX(8px);
                transform: translateX(8px);
    }
    90% {
        -webkit-transform: translateX(-8px);
                transform: translateX(-8px);
    }
    }
    body{
        background-color: #f8f9fa !important;
    }
    .form{
        box-shadow: rgba(0, 0, 0, 0.24) 0px 3px 8px;
        padding: 30px;
        width: 35%;
        margin: 10% auto !important;
        border-radius: 10px !important;
        border-top: 3px solid #4285f4 !important;
        background-color: #fff !important;
    }
    .form-group {
        position: relative;
    }
    .form-group .password-toggle{
        color: grey;
        position: absolute;
        cursor: pointer;
        top: 18;
        right: 35;
    }
    .form-group input{
        box-shadow: rgba(60, 64, 67, 0.3) 0px 1px 2px 0px, rgba(60, 64, 67, 0.15) 0px 1px 3px 1px;
        outline: 0px !important;
        font-family: Arial, FontAwesome;
        padding: 25px 10px !important;

    }

    .header{
        text-align: center;
    }
    .logo{
        width: 200px !important;
        margin-bottom: 25px !important;
    }
    .form .btn{

        width: 100%;
        background-color: #4285f4;
        border: none !important;
        font-weight: 600;
        text-transform: capitalize;
        margin-bottom: 20px !important;
    }

    .form .btn:hover{
        background-color: #6610f2;
    }
    .is-invalid {
    border-color: #dc3545 !important; /* Red color */

    }

    .invalid-feedback {
        color: #dc3545; /* Red color */
    }

    .alert-danger{
        -webkit-animation: shake-horizontal 0.8s cubic-bezier(0.455, 0.030, 0.515, 0.955) both;
	        animation: shake-horizontal 0.8s cubic-bezier(0.455, 0.030, 0.515, 0.955) both;
    }




</style>


@section('content')
<div class="container-fluid">
   <div class="container">
    <form class="form" method="post" action="{{ route('login.post') }}">
        @csrf
        <div class="header">
            <img class="logo" src="assets/wcmc_logo_1.png" alt="">
        </div>
        @if(Session::has('status') || $errors->has('email') || $errors->has('password'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                @if(session('status'))
                    {{ session('status') }}
                @endif
                @error('email')
                    {{ $message }}
                @enderror

                @error('password')
                    {{ $message }}
                @enderror
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif
        <div class="form-group">
            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" id="exampleInputEmail1" aria-describedby="emailHelp" placeholder="&#xf0e0;  Email..." value="{{ old('email') }}">
            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <div class="form-group">
            <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" id="exampleInputPassword1" placeholder="&#xf023; Password..." value="{{ old('password') }}">
            <span class="password-toggle" onclick="togglePasswordVisibility()">
                <i class="fa fa-eye" id="password-toggle-icon"></i>
            </span>

            @error('password')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>


        {{-- <div class="form-check m-2">
            <input type="checkbox" class="form-check-input" id="rememberMeCheckbox">
            <label class="form-check-label" for="rememberMeCheckbox">Remember me</label>
        </div> --}}
        <br>
        <button type="submit" class="btn btn-primary">Sign In</button>
      </form>

   </div>
</div>
<script>
    function togglePasswordVisibility() {
    var passwordInput = document.getElementById("exampleInputPassword1");
    var toggleIcon = document.getElementById("password-toggle-icon");

    if (passwordInput.type === "password") {
        passwordInput.type = "text";
        toggleIcon.classList.remove("fa-eye");
        toggleIcon.classList.add("fa-eye-slash");
    } else {
        passwordInput.type = "password";
        toggleIcon.classList.remove("fa-eye-slash");
        toggleIcon.classList.add("fa-eye");
    }
}















</script>





@endsection
