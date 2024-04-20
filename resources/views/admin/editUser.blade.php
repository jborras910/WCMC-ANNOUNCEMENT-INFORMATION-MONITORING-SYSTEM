@extends('admin.index')
@section('title', 'Home Page')


@section('content')


<style>
    .page-header{
        box-shadow: rgba(50, 50, 93, 0.25) 0px 2px 5px -1px, rgba(0, 0, 0, 0.3) 0px 1px 3px -1px;
        padding: 20px 10px !important;
        background-color: #fff !important;
        border-radius: 5px !important;
    }

    .form-group input, .form-group textarea, .form-group .form-select{
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






<div class="row">
    @if(session('success'))
    <script>
        Swal.fire({
        title: "Success!",
        text: "{{ session('success') }}",
        icon: "success",
    });
</script>
@elseif(session('error'))
    <script>
        Swal.fire({
            title: "Error!",
            text: "{{ session('error') }}",
            icon: "error",
        });
    </script>
@endif
    <div class="col-md-12 grid-margin">
        {{-- <h3 class="page-header"><i class="fa-solid fa-user mr-2"></i>Users Table</h3> --}}
        <div class="card">
            <div class="card-body">


                    <form class="form" method="post" action="{{route('admin.updateUserPost', ['user' => $user])}}">
                        @csrf
                        @method('put')

                <h3>Edit User</h3>
                <hr>
                <br>
                <div class="row">
                    <div class="form-group col-md-12">
                        <label for="">User Status</label>
                        <select class="form-select" name="status" aria-label="Default select example">
                            <option value="{{$user->status}}"  selected>{{$user->status}}</option>
                            <option value="Active">Active</option>
                            <option value="Inactive">Inactive</option>
                          </select>
                    </div>

                    <div class="form-group col-md-4">
                        <label for="">First Name</label>
                        <input type="text" name="first_name" value="{{$user->first_name}}" class="form-control" aria-describedby="emailHelp"   placeholder="&#xf007;  First Name..." required>
                    </div>

                    <div class="form-group col-md-4">
                        <label for="">Last Name</label>
                        <input type="text" name="last_name" value="{{$user->last_name}}" class="form-control" aria-describedby="emailHelp"   placeholder="&#xf007;  Last Name..." required>
                    </div>

                    <div class="form-group col-md-4">
                        <label for="">Middle Name</label>
                        <input type="text" name="middle_name" value="{{$user->middle_name}}" class="form-control" aria-describedby="emailHelp"   placeholder="&#xf007;  Middle Name...">
                    </div>

                    <div class="form-group col-md-12">
                        <label for="">Email</label>
                    <input type="email" name="email" class="form-control" value="{{$user->email}}" id="exampleInputEmail1" aria-describedby="emailHelp"   placeholder="&#xf0e0;  Email..." required>
                    </div>
                    <div class="form-group col-md-6">
                        <label for="">New Password</label>
                        <input type="text" name="password" class="form-control"  id="exampleInputPassword1" placeholder="&#xf023; Password..." >
                    </div>



                </div>
                    <br>
                    <button type="submit" class="btn btn-primary text-light ">Saved</button>
                    <a type="submit" href="{{route('admin.users')}}" class="btn btn-secondary text-light ">Cancel</a>
                  </form>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>

{{-- jQuery --}}
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>





<script>
    $(document).ready(function() {
        $('#dataTable').DataTable();
    });
</script>


@endsection
