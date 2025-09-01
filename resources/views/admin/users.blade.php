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
    .bg-success-on{
        background-color: rgb(77, 238, 77) !important;

    }
    .bg-danger-on{
        background-color: rgba(240, 24, 24, 0.406) !important;
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
                <div class="d-flex justify-content-between flex-wrap">
                    <div class="d-flex align-items-end flex-wrap">
                        <h2>Users Table</h2>
                    </div>
                    <div class="d-flex justify-content-between align-items-end flex-wrap">
                        <a href="{{route('admin.addUser')}}" class="btn btn-primary text-light mr-1">Add User</a>
                        {{-- <a href="" style="background-color: #1D6F42;" class="btn  text-light">Export to excell</a> --}}
                    </div>
                </div>
                <div class="table-responsive pt-3">
                    <table class="table bg-light table-bordered table-striped" id="dataTable" >
                        <thead class="thead-dark" style="text-transform: uppercase;">
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Username</th>
                                <th class="text-center">Status</th>
                                {{-- <th>User Status</th>
                                <th>Role</th> --}}
                                <th class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($users as $index => $user)

                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td style="text-transform: capitalize;">{{$user->first_name." ".$user->middle_name." ".$user->last_name}}</td>
                                <td>{{$user->username}}</td>

                                <td class="text-center text-light"><p style="width: 55%; margin: 0px auto; padding:6px;" class="@if($user->status === 'Active') bg-success-on @else bg-danger-on @endif">{{$user->status}}</p></td>

                                {{-- <td>{{$user->status}}</td>
                                <td>{{$user->role}}</td> --}}
                                <td class="text-center">
                                    <div class="dropdown">
                                        <button class="btn btn-success text-light dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <i style="font-size: 15px;" class="fa-solid fa-gear mr-2"></i>Action
                                        </button>
                                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                          <a class="dropdown-item" href="{{route('user.edit', ['user'=> $user])}}"><i class="fa-solid fa-gear mr-2"></i>Edit</a>
                                          <a type="button" data-toggle="modal" data-target="#exampleModalCenter{{$user->id}}" class="dropdown-item" href="#"><i class="fa-solid fa-trash mr-2"></i>Delete</a>

                                        </div>
                                      </div>
                                    {{--
                                    <a href="{{route('user.edit', ['user'=> $user])}}" class="btn btn-success text-light"><i class="fa-solid fa-pen-to-square mr-2"></i>Edit</a>
                                    <button type="button" class="btn btn-danger text-light" data-toggle="modal" data-target="#exampleModalCenter{{$user->id}}">
                                        <i class="fa-solid fa-trash mr-2"></i>Delete
                                    </button> --}}
                                    <!-- Modal -->
                                    <form method="post" action="{{route('deleteUser.destroy', ['user'=> $user])}}">
                                        @csrf
                                        @method('delete')
                                            <div class="modal fade" id="exampleModalCenter{{$user->id}}" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-centered" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                    <h5 class="modal-title text-danger" id="exampleModalLongTitle">Warning!!!</h5>
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <h5>Are you sure you want to delete <p class="text-capitalize text-danger">{{$user->first_name." ".$user->last_name}}</p></h5>
                                                    </div>
                                                    <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                                    <button type="submit" class="btn btn-danger text-light">Delete</button>
                                                    </div>
                                                </div>
                                                </div>
                                            </div>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
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
