@extends('admin.index')
@section('title', 'Home Page')

@section('content')

    <style>
        .table {
            /* Your custom styles here */
            box-shadow: rgba(0, 0, 0, 0.02) 0px 1px 3px 0px, rgba(27, 31, 35, 0.15) 0px 0px 0px 1px;
        }

        .table td {
            padding: 20px !important;
        }

        .table td .btn {
            padding: 10px !important;
            font-weight: 500;

            margin-bottom: 5px !important;
        }

        .flex-box {
            border: 2px solid red !important;
            display: flex-start !important;
            justify-content: space-around;
            align-items: baseline !important;
        }

        .fa-solid {

            font-size: 15px !important;
        }
    </style>
    <script></script>


    <div class="container-fluid">

        @if (session('success'))
            <script>
                const Toast = Swal.mixin({
                    toast: true,
                    position: "top-end",
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true,
                    didOpen: (toast) => {
                        toast.onmouseenter = Swal.stopTimer;
                        toast.onmouseleave = Swal.resumeTimer;
                    }
                });
                Toast.fire({
                    icon: "success",
                    title: "{{ session('success') }}"
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





        <div class="row">
            <div class="col-md-12 grid-margin">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between flex-wrap">
                            <div class="d-flex align-items-end flex-wrap">
                                <h2>Slide Table</h2>
                            </div>
                            <div class="d-flex justify-content-between align-items-end flex-wrap">

                                <a href="{{ route('admin.addSlide') }}" class="btn btn-primary text-light">Add Slide</a>
                            </div>
                        </div>
                        <br>
                        @if (Auth()->user()->role !== 'user')
                            <div class="row">
                                <div class="col-md-12 grid-margin stretch-card">
                                    <div class="card">
                                        <div class="card-body dashboard-tabs p-0">

                                            <div class="tab-content py-0 px-0">
                                                <div class="tab-pane fade show active" id="overview" role="tabpanel"
                                                    aria-labelledby="overview-tab">
                                                    <div class="d-flex flex-wrap justify-content-xl-between">

                                                        <div
                                                            class="d-flex border-md-right flex-grow-1 align-items-center justify-content-center p-3 item">
                                                            <i class="mdi mdi-video  me-3 icon-lg text-danger"></i>
                                                            <div class="d-flex flex-column justify-content-around">
                                                                <small class="mb-1 text-muted">Total Slide</small>
                                                                <h5 class="me-2 mb-0">{{ $slideCount }}</h5>
                                                            </div>
                                                        </div>
                                                        @if (Auth()->user()->role === 'master admin')
                                                            <div
                                                                class="d-flex border-md-right flex-grow-1 align-items-center justify-content-center p-3 item">
                                                                <i
                                                                    class=" mdi mdi-account-multiple-outline  me-3 icon-lg text-success"></i>
                                                                <div class="d-flex flex-column justify-content-around">
                                                                    <small class="mb-1 text-muted">Total User</small>
                                                                    <h5 class="me-2 mb-0">{{ $userCount }}</h5>
                                                                </div>
                                                            </div>
                                                        @endif
                                                        <div
                                                            class="d-flex border-md-right flex-grow-1 align-items-center justify-content-center p-3 item">
                                                            <i class=" mdi mdi-lan-pending  me-3 icon-lg text-warning"></i>
                                                            <div class="d-flex flex-column justify-content-around">
                                                                <small class="mb-1 text-muted">Pending Slide</small>
                                                                <h5 class="me-2 mb-0">{{ $slidesPending }}</h5>
                                                            </div>
                                                        </div>
                                                        <div
                                                            class="d-flex py-3 border-md-right flex-grow-1 align-items-center justify-content-center p-3 item">
                                                            <i class="mdi mdi-flag me-3 icon-lg text-danger"></i>
                                                            <div class="d-flex flex-column justify-content-around">
                                                                <small class="mb-1 text-muted">Publish Slide</small>
                                                                <h5 class="me-2 mb-0">{{ $slidesPublish }}</h5>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>


                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                        <div class="table-responsive pt-3">

                            <table class="table bg-light table-bordered " id="dataTable">
                                <thead class="thead-dark" style="text-transform: capitalize;">
                                    <tr>
                                        <th>No </th>
                                        <th>File</th>
                                        @if (Auth()->user()->role !== 'user')
                                            <th>Department</th>
                                        @endif
                                        <th class="text-center">Status</th>
                                        <th class="text-center">Action</th>

                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($slides as $index => $slide)
                                        @if ($slide->added_by_email == Auth()->user()->email)
                                            <tr>

                                                <td>
                                                    {{ $index + 1 }}
                                                </td>

                                                <td>

                                                    <video width="300" height="200" controls preload="metadata" muted>
                                                        <source src="{{ asset('image_upload/' . $slide->file) }}"
                                                            type="video/mp4">
                                                    </video>


                                                </td>
                                                @if (Auth()->user()->role !== 'user')
                                                    <td>{{ $slide->department }}</td>
                                                @endif
                                                <td class="text-uppercase text-center"
                                                    style="color: {{ $slide->status === 'pending' || $slide->status === 'rejected' ? 'red' : 'green' }}">
                                                    {{ $slide->status }}
                                                </td>
                                                <td class="text-center">
                                                    <a class="btn btn-success text-light"
                                                        href="{{ route('slide.edit', ['slide' => $slide]) }}"><i
                                                            class="fa-solid fa-pen-to-square mr-2"></i>Edit</a>
                                                    <!-- Button trigger modal -->



                                                    <button type="button" class="btn btn-danger text-light"
                                                        data-toggle="modal"
                                                        data-target="#exampleModalCenter{{ $slide->id }}">
                                                        <i class="fa-solid fa-trash mr-2"></i>Delete
                                                    </button>
                                                    <!-- Modal -->
                                                    <form method="post"
                                                        action="{{ route('deleteSlide.destroy', ['slide' => $slide]) }}">
                                                        @csrf
                                                        @method('delete')
                                                        <div class="modal fade" id="exampleModalCenter{{ $slide->id }}"
                                                            tabindex="-1" role="dialog"
                                                            aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                                                            <div class="modal-dialog modal-dialog-centered" role="document">
                                                                <div class="modal-content">
                                                                    <div class="modal-header">
                                                                        <h5 class="modal-title" id="exampleModalLongTitle">
                                                                            Are you sure you
                                                                            want to delete this slide?</h5>


                                                                        <button type="button" class="close"
                                                                            data-dismiss="modal" aria-label="Close">
                                                                            <span aria-hidden="true">&times;</span>
                                                                        </button>
                                                                    </div>
                                                                    <div class="modal-body">
                                                                        {{-- Determine the file type --}}
                                                                        @php
                                                                            $extension = pathinfo(
                                                                                $slide->file,
                                                                                PATHINFO_EXTENSION,
                                                                            );
                                                                        @endphp
                                                                        @if ($extension == 'jpg' || $extension == 'jpeg' || $extension == 'png' || $extension == 'gif')
                                                                            {{-- Display the image --}}
                                                                            <img style="width:100%; height: 400px; border-radius: 0px;"
                                                                                src="{{ 'image_upload/' . $slide->file }}"
                                                                                alt="">
                                                                        @elseif ($extension == 'mp4' || $extension == 'avi' || $extension == 'mov' || $extension == 'wmv')
                                                                            {{-- Display the video --}}
                                                                            <video style="width:100%" controls>
                                                                                <source
                                                                                    src="{{ 'image_upload/' . $slide->file }}"
                                                                                    type="video/mp4">
                                                                                Your browser does not support the video tag.
                                                                            </video>

                                                                            <input type="hidden" name="user_add_name"
                                                                                value="{{ Auth()->user()->first_name . ' ' . Auth()->user()->last_name }}">
                                                                            <input type="hidden" name="user_add_email"
                                                                                value="{{ Auth()->user()->email }}">
                                                                            <input type="hidden" name="user_add_activity"
                                                                                value="{{ Auth()->user()->first_name . ' ' . Auth()->user()->last_name . ' Deleted Slide' }}">
                                                                        @else
                                                                            {{-- Display a link to download the document --}}
                                                                            <iframe style="width:100%" class="pdf"
                                                                                src="{{ 'image_upload/' . $slide->file }}"
                                                                                width="400" height="400"></iframe>
                                                                            {{-- <a href="{{'image_upload/'.$slide->file}}" download>{{$slide->file}}</a> --}}
                                                                        @endif

                                                                    </div>
                                                                    <div class="modal-footer text-light text-center">
                                                                        <button type="submit"
                                                                            class="btn btn-danger text-light">Delete</button>
                                                                        <button type="button" class="btn btn-secondary"
                                                                            data-dismiss="modal">Close</button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </form>
                                                </td>
                                            </tr>
                                        @elseif(Auth()->user()->role !== 'user')
                                            <tr class="">
                                                <td>
                                                    {{ $index + 1 }}
                                                </td>
                                                <td>



                                                    <video width="300" height="200" control autoplay muted>
                                                        <source src="{{ 'image_upload/' . $slide->file }}"
                                                            type="video/mp4">

                                                    </video>




                                                </td>
                                                @if (Auth()->user()->role !== 'user')
                                                    <td>{{ $slide->department }}</td>
                                                    <td class="text-uppercase text-center"
                                                        style="color: {{ $slide->status === 'pending' || $slide->status === 'rejected' ? 'red' : 'green' }}">
                                                        {{ $slide->status }}
                                                    </td>
                                                @endif

                                                @if ($slide->status === 'pending')
                                                    <td class="text-center">
                                                        <Button class="btn btn-warning text-light" data-toggle="modal"
                                                            data-target="#exampleModalCenter{{ $slide->id }}"><i
                                                                class="fa-solid fa-eye mr-2"></i>Publish</Button>
                                                        <button type="button" class="btn btn-danger text-light"
                                                            data-toggle="modal"
                                                            data-target="#exampleModalCenter_2{{ $slide->id }}"><i
                                                                class="fa-solid fa-ban mr-2"></i>Reject</button>
                                                        <!-- Modal -->
                                                        <form method="post"
                                                            action="{{ route('slide.reject', ['slide' => $slide]) }}">
                                                            @csrf
                                                            @method('put')
                                                            <div class="modal fade"
                                                                id="exampleModalCenter_2{{ $slide->id }}"
                                                                tabindex="-1" role="dialog"
                                                                aria-labelledby="exampleModalCenterTitle"
                                                                aria-hidden="true">
                                                                <div class="modal-dialog modal-dialog-centered"
                                                                    role="document">
                                                                    <div class="modal-content">
                                                                        <div class="modal-header">
                                                                            <h5 class="modal-title"
                                                                                id="exampleModalLongTitle">Are you sure you
                                                                                want to delete this slide?</h5>


                                                                            <button type="button" class="close"
                                                                                data-dismiss="modal" aria-label="Close">
                                                                                <span aria-hidden="true">&times;</span>
                                                                            </button>
                                                                        </div>
                                                                        <div class="modal-body">
                                                                            {{-- Determine the file type --}}
                                                                            @php
                                                                                $extension = pathinfo(
                                                                                    $slide->file,
                                                                                    PATHINFO_EXTENSION,
                                                                                );
                                                                            @endphp
                                                                            @if ($extension == 'jpg' || $extension == 'jpeg' || $extension == 'png' || $extension == 'gif')
                                                                                {{-- Display the image --}}
                                                                                <img style="width:100%; height: 400px; border-radius: 0px;"
                                                                                    src="{{ 'image_upload/' . $slide->file }}"
                                                                                    alt="">
                                                                            @elseif ($extension == 'mp4' || $extension == 'avi' || $extension == 'mov' || $extension == 'wmv')
                                                                                {{-- Display the video --}}
                                                                                <video style="width:100%" controls>
                                                                                    <source
                                                                                        src="{{ 'image_upload/' . $slide->file }}"
                                                                                        type="video/mp4">
                                                                                    Your browser does not support the video
                                                                                    tag.
                                                                                </video>

                                                                                <input type="hidden" name="user_add_name"
                                                                                    value="{{ Auth()->user()->first_name . ' ' . Auth()->user()->last_name }}">
                                                                                <input type="hidden"
                                                                                    name="user_add_email"
                                                                                    value="{{ Auth()->user()->email }}">
                                                                                <input type="hidden"
                                                                                    name="user_add_activity"
                                                                                    value="{{ Auth()->user()->first_name . ' ' . Auth()->user()->last_name . ' rejected Slide' }}">
                                                                            @else
                                                                                {{-- Display a link to download the document --}}
                                                                                <iframe style="width:100%" class="pdf"
                                                                                    src="{{ 'image_upload/' . $slide->file }}"
                                                                                    width="400"
                                                                                    height="400"></iframe>
                                                                                {{-- <a href="{{'image_upload/'.$slide->file}}" download>{{$slide->file}}</a> --}}
                                                                            @endif

                                                                        </div>
                                                                        <div class="modal-footer text-light">
                                                                            <button type="submit"
                                                                                class="btn btn-danger text-light">Delete</button>
                                                                            <button type="button"
                                                                                class="btn btn-secondary"
                                                                                data-dismiss="modal">Close</button>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </form>

                                                        <form method="post"
                                                            action="{{ route('slide.publishFile', ['slide' => $slide]) }}">
                                                            @csrf
                                                            @method('put') <!-- Use 'put' method -->
                                                            <div class="modal fade"
                                                                id="exampleModalCenter{{ $slide->id }}" tabindex="-1"
                                                                role="dialog" aria-labelledby="exampleModalCenterTitle"
                                                                aria-hidden="true">
                                                                <div class="modal-dialog modal-dialog-centered"
                                                                    role="document">
                                                                    <div class="modal-content">
                                                                        <div class="modal-header">
                                                                            <h5 class="modal-title"
                                                                                id="exampleModalLongTitle">Are you sure you
                                                                                want to publish this file?</h5>


                                                                            <button type="button" class="close"
                                                                                data-dismiss="modal" aria-label="Close">
                                                                                <span aria-hidden="true">&times;</span>
                                                                            </button>
                                                                        </div>
                                                                        <div class="modal-body">
                                                                            <video style="width:100%" controls>
                                                                                <source
                                                                                    src="{{ 'image_upload/' . $slide->file }}"
                                                                                    type="video/mp4">
                                                                                Your browser does not support the video tag.
                                                                            </video>

                                                                            <input type="hidden" name="user_add_name"
                                                                                value="{{ Auth()->user()->first_name . ' ' . Auth()->user()->last_name }}">
                                                                            <input type="hidden" name="user_add_email"
                                                                                value="{{ Auth()->user()->email }}">
                                                                            <input type="hidden" name="user_add_activity"
                                                                                value="{{ Auth()->user()->first_name . ' ' . Auth()->user()->last_name . ' Publish the slide' }}">
                                                                        </div>
                                                                        <div class="modal-footer text-light text-center">
                                                                            <button type="submit"
                                                                                class="btn btn-warning  text-light">Publish</button>
                                                                            <button type="button"
                                                                                class="btn btn-secondary"
                                                                                data-dismiss="modal">Close</button>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </form>
                                                    </td>
                                                @else
                                                    <td class="text-center">
                                                        <a class="btn btn-success text-light"
                                                            href="{{ route('slide.edit', ['slide' => $slide]) }}"><i
                                                                class="fa-solid fa-pen-to-square mr-2"></i>Edit</a>
                                                        <!-- Button trigger modal -->
                                                        <button type="button" class="btn btn-danger text-light"
                                                            data-toggle="modal"
                                                            data-target="#exampleModalCenter{{ $slide->id }}">
                                                            <i class="fa-solid fa-trash mr-2"></i>Delete
                                                        </button>
                                                        <!-- Modal -->
                                                        <form method="post"
                                                            action="{{ route('deleteSlide.destroy', ['slide' => $slide]) }}">
                                                            @csrf
                                                            @method('delete')
                                                            <div class="modal fade"
                                                                id="exampleModalCenter{{ $slide->id }}" tabindex="-1"
                                                                role="dialog" aria-labelledby="exampleModalCenterTitle"
                                                                aria-hidden="true">
                                                                <div class="modal-dialog modal-dialog-centered"
                                                                    role="document">
                                                                    <div class="modal-content">
                                                                        <div class="modal-header">
                                                                            <h5 class="modal-title"
                                                                                id="exampleModalLongTitle">Are you sure you
                                                                                want to delete this slide?</h5>


                                                                            <button type="button" class="close"
                                                                                data-dismiss="modal" aria-label="Close">
                                                                                <span aria-hidden="true">&times;</span>
                                                                            </button>
                                                                        </div>
                                                                        <div class="modal-body">
                                                                            {{-- Determine the file type --}}
                                                                            @php
                                                                                $extension = pathinfo(
                                                                                    $slide->file,
                                                                                    PATHINFO_EXTENSION,
                                                                                );
                                                                            @endphp
                                                                            @if ($extension == 'jpg' || $extension == 'jpeg' || $extension == 'png' || $extension == 'gif')
                                                                                {{-- Display the image --}}
                                                                                <img style="width:100%; height: 400px; border-radius: 0px;"
                                                                                    src="{{ 'image_upload/' . $slide->file }}"
                                                                                    alt="">
                                                                            @elseif ($extension == 'mp4' || $extension == 'avi' || $extension == 'mov' || $extension == 'wmv')
                                                                                {{-- Display the video --}}
                                                                                <video style="width:100%" controls>
                                                                                    <source
                                                                                        src="{{ 'image_upload/' . $slide->file }}"
                                                                                        type="video/mp4">
                                                                                    Your browser does not support the video
                                                                                    tag.
                                                                                </video>

                                                                                <input type="hidden" name="user_add_name"
                                                                                    value="{{ Auth()->user()->first_name . ' ' . Auth()->user()->last_name }}">
                                                                                <input type="hidden"
                                                                                    name="user_add_email"
                                                                                    value="{{ Auth()->user()->email }}">
                                                                                <input type="hidden"
                                                                                    name="user_add_activity"
                                                                                    value="{{ Auth()->user()->first_name . ' ' . Auth()->user()->last_name . ' Deleted Slide' }}">
                                                                            @else
                                                                                {{-- Display a link to download the document --}}
                                                                                <iframe style="width:100%" class="pdf"
                                                                                    src="{{ 'image_upload/' . $slide->file }}"
                                                                                    width="400"
                                                                                    height="400"></iframe>
                                                                                {{-- <a href="{{'image_upload/'.$slide->file}}" download>{{$slide->file}}</a> --}}
                                                                            @endif

                                                                        </div>
                                                                        <div class="modal-footer text-light">
                                                                            <button type="submit"
                                                                                class="btn btn-danger text-light">Delete</button>
                                                                            <button type="button"
                                                                                class="btn btn-secondary"
                                                                                data-dismiss="modal">Close</button>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </form>
                                                    </td>
                                                @endif
                                            </tr>
                                        @endif
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>


    <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js"
        integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous">
    </script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"
        integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous">
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/js/bootstrap.min.js"
        integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous">
    </script>

    {{-- jQuery --}}
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>





    <script>
        $(document).ready(function() {
            $('#dataTable').DataTable();
        });
    </script>

@endsection
