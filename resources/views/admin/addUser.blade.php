@extends('admin.index')
@section('title', 'Edit Slide')


<style>
    .form-group input, .form-group textarea{
        box-shadow: rgba(0, 0, 0, 0.02) 0px 1px 3px 0px, rgba(27, 31, 35, 0.15) 0px 0px 0px 1px;
        outline: 0px !important;
        font-family: Arial, FontAwesome;

        background-color: rgb(243, 243, 243) !important;

        border: 2px solid rgb(243, 243, 243) !important;
    }
    .form-group label {
        margin-bottom: 4px !important;
        font-weight: 500;


    }


</style>

@section('content')

<div class="card">

    @if(Session::has('status') || $errors->has('email') || $errors->has('password'))

        @if(session('status'))



            <script>
                Swal.fire({
                title: "Invalid!",
                text: "{{ session('status') }}",
                icon: "error",
            });
        </script>






        @endif
        @error('email')


            <script>
                Swal.fire({
                title: "Invalid!",
                text: "{{ $message }}",
                icon: "error",
            });
        </script>
        @enderror

        @error('password')
        <script>
            Swal.fire({
            title: "Invalid!",
            text: "{{ $message }}",
            icon: "error",
        });
    </script>
        @enderror

@endif
    <div class="card-body">
        <form class="form" method="post" action="{{ route('admin.addUserPost') }}">
            @csrf

        <h3>Add User</h3>
        <hr>
        <br>
        <div class="row">
            <div class="form-group col-md-4">
                <label for="">First Name</label>
                <input type="text" name="first_name" class="form-control" aria-describedby="emailHelp"   placeholder="&#xf007;  First Name..." value="{{ old('first_name') }}" required>
            </div>

            <div class="form-group col-md-4">
                <label for="">Last Name</label>
                <input type="text" name="last_name" class="form-control" aria-describedby="emailHelp"   placeholder="&#xf007;  Last Name..." value="{{ old('last_name') }}" required>
            </div>

            <div class="form-group col-md-4">
                <label for="">Middle Name</label>
                <input type="text" name="middle_name" class="form-control" aria-describedby="emailHelp"   placeholder="&#xf007;  Middle Name..." value="{{ old('middle_name') }}">
            </div>

            <div class="form-group col-md-12">
                <label for="">Email</label>
            <input type="email" name="email" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp"   placeholder="&#xf0e0;  Email..." value="{{ old('email') }}" required>
            </div>
            <div class="form-group col-md-6">
                <label for="">Password</label>
                <input type="password" name="password" class="form-control" id="exampleInputPassword1" placeholder="&#xf023; Password..." value="{{ old('password') }}" required>
            </div>
            {{-- <div class="form-group col-md-6">
                <label for="">Confirm Password</label>
                <input type="password" name="confirm_password" class="form-control" id="exampleInputPassword1" placeholder="&#xf023; Password...">
            </div> --}}
        </div>
            <br>
            <button type="submit" class="btn btn-primary text-light ">Register User</button>
            <a type="submit" href="{{route('admin.users')}}" class="btn btn-secondary text-light ">Cancel</a>
          </form>
    </div>
</div>



@endsection
