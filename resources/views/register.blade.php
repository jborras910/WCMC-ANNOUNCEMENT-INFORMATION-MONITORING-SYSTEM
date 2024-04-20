@extends('layouts.index')
@section('title', 'register')


<style>
    body{
        background-color: #f8f9fa !important;
    }
    .form{
        box-shadow: rgba(0, 0, 0, 0.24) 0px 3px 8px;
        padding: 30px;
        width: 35%;
        margin: 5% auto !important;
        border-radius: 10px !important;
        border-top: 3px solid #4285f4 !important;
        background-color: #fff !important;
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
</style>


@section('content')
<div class="container-fluid">
   <div class="container">
    <form class="form" method="post" action="{{ route('registration.post') }}">
        @csrf
        <div class="header">
            <img class="logo" src="assets/wcmc_logo_1.png" alt="">
        </div>

        <div class="form-group">
            <input type="text" name="first_name" class="form-control" aria-describedby="emailHelp"   placeholder="&#xf0e0;  First Name...">
        </div>

        <div class="form-group">
            <input type="text" name="last_name" class="form-control" aria-describedby="emailHelp"   placeholder="&#xf0e0;  Last Name...">
        </div>

        <div class="form-group">
            <input type="text" name="middle_name" class="form-control" aria-describedby="emailHelp"   placeholder="&#xf0e0;  Middle Name...">
        </div>

        <div class="form-group">
          <input type="email" name="email" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp"   placeholder="&#xf0e0;  Email...">
        </div>
        <div class="form-group">
          <input type="password" name="password" class="form-control" id="exampleInputPassword1" placeholder="&#xf023; Password...">
        </div>
     

        <button type="submit" class="btn btn-primary">Sign In</button>
      </form>
   </div>
</div>



@endsection