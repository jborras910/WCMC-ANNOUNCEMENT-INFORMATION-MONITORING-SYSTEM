@extends('admin.index')
@section('title', 'Pending slides')


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
                        <h2>Pending slides Table</h2>
                    </div>
                    <div class="d-flex justify-content-between align-items-end flex-wrap">


                    </div>
                </div>
                <div class="table-responsive pt-3">
                    <table class="table bg-light table-bordered table-striped" id="dataTable" >
                        <thead class="thead-dark" style="text-transform: capitalize;">
                            <tr>
                                <th>#</th>
                                <th>File</th>
                                <th>Department</th>
                                <th class="text-center">Status</th>
                                <th class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($slides as $index => $slide)
                         <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>
                                {{-- <iframe  src="{{ 'image_upload/'.$slide->file }}" width="300" height="200" allow="autoplay; muted; controls"></iframe> --}}

                                <video width="300" height="200" control autoplay muted>
                                    <source src="{{'image_upload/'.$slide->file}}" type="video/mp4">
                                    Your browser does not support the video tag.
                                </video>
                            </td>
                            <td>{{$slide->department}}</td>
                            <td class="text-uppercase text-center" style="color: {{ $slide->status === 'pending' ? 'red' : 'green' }}">
                                {{ $slide->status }}
                            </td>
                            <td class="text-center">
                                <Button class="btn btn-success text-light" data-toggle="modal" data-target="#exampleModalCenter{{$slide->id}}">Approved</Button>

                                <button type="button" class="btn btn-dark text-light" data-toggle="modal" data-target="#exampleModalCenter_2{{$slide->id}}">Reject</button>
                                <!-- Modal -->
                                <form method="post" action="{{route('slide.reject', ['slide'=> $slide])}}">
                                    @csrf
                                    @method('put')
                                    <div class="modal fade" id="exampleModalCenter_2{{$slide->id}}" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="exampleModalLongTitle">Are you sure you want to reject this slide?</h5>


                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    {{-- Determine the file type --}}
                                                    @php
                                                        $extension = pathinfo($slide->file, PATHINFO_EXTENSION);
                                                    @endphp
                                                        @if ($extension == 'jpg' || $extension == 'jpeg' || $extension == 'png' || $extension == 'gif')
                                                            {{-- Display the image --}}
                                                            <img style="width:100%; height: 400px; border-radius: 0px;" src="{{'image_upload/'.$slide->file}}" alt="">
                                                        @elseif ($extension == 'mp4' || $extension == 'avi' || $extension == 'mov' || $extension == 'wmv')
                                                            {{-- Display the video --}}
                                                            <video style="width:100%" controls>
                                                                <source src="{{'image_upload/'.$slide->file}}" type="video/mp4">
                                                                Your browser does not support the video tag.
                                                            </video>

                                                            <input type="hidden" name="user_add_name" value="{{Auth()->user()->first_name." ".Auth()->user()->last_name}}">
                                                            <input type="hidden" name="user_add_email" value="{{Auth()->user()->email}}">
                                                            <input type="hidden" name="user_add_activity" value="{{Auth()->user()->first_name." ".Auth()->user()->last_name." rejected Slide"}}">
                                                        @else
                                                            {{-- Display a link to download the document --}}
                                                            <iframe style="width:100%" class="pdf" src="{{'image_upload/'.$slide->file}}" width="400" height="400"></iframe>
                                                            {{-- <a href="{{'image_upload/'.$slide->file}}" download>{{$slide->file}}</a> --}}
                                                        @endif

                                                </div>
                                                <div class="modal-footer text-light">
                                                    <button type="submit" class="btn btn-danger text-light" >Reject</button>
                                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>

                                <form method="post" action="{{ route('slide.publishFile', ['slide' => $slide]) }}">
                                    @csrf
                                    @method('put') <!-- Use 'put' method -->
                                    <div class="modal fade" id="exampleModalCenter{{$slide->id}}" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="exampleModalLongTitle">Are you sure you want to Post this file?</h5>


                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    <video style="width:100%" controls>
                                                        <source src="{{'image_upload/'.$slide->file}}" type="video/mp4">
                                                        Your browser does not support the video tag.
                                                    </video>


                                                    <input type="hidden" name="user_add_name" value="{{Auth()->user()->first_name." ".Auth()->user()->last_name}}">
                                                    <input type="hidden" name="user_add_email" value="{{Auth()->user()->email}}">
                                                    <input type="hidden" name="user_add_activity" value="{{Auth()->user()->first_name." ".Auth()->user()->last_name." Publish the slide"}}">







                                                </div>
                                                <div class="modal-footer text-light text-center">
                                                    <button type="submit" class="btn btn-success text-light" >Publish</button>
                                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
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
