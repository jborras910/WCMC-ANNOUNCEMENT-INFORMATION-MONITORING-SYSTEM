@extends('admin.index')
@section('title', 'Edit Slide')


<style>
    .form-group input, .form-group textarea{
        box-shadow: rgba(0, 0, 0, 0.02) 0px 1px 3px 0px, rgba(27, 31, 35, 0.15) 0px 0px 0px 1px;
    }
    #imagePreview{
        display: none;
        max-width: 300px;
        height: 300px;
        margin-bottom: 20px;
        box-shadow: rgba(0, 0, 0, 0.16) 0px 3px 6px, rgba(0, 0, 0, 0.23) 0px 3px 6px;
    }
</style>

@section('content')
<div class="card">
    <div class="card-body">
        {{-- <div class="d-flex justify-content-between flex-wrap">
            <div class="d-flex align-items-end flex-wrap">
            </div>
            <div class="d-flex justify-content-between align-items-end flex-wrap">
                <a href="{{route('admin.dashboard')}}" class="btn btn-danger text-light">Back</a>
            </div>
        </div> --}}

        @php
        $extension = pathinfo($slide->file, PATHINFO_EXTENSION);
        @endphp
        {{-- Display the file --}}

        @if ($extension == 'jpg' || $extension == 'jpeg' || $extension == 'png' || $extension == 'gif')
        <form class="form" method="post" action="{{route('slide.update', ['slide' => $slide])}}" enctype="multipart/form-data">
            @csrf
            @method('put')
            @if ($errors->any())

            <script>
                @foreach ($errors->all() as $error)
                    swal({
                        title: "Error!",
                        text: "{{ $error == 'The file name must be an image.' ? 'Please upload an image file.' : $error }}",
                        icon: "error",
                    });
                @endforeach
            </script>
            @endif

            @csrf
            <img id="" src="{{ asset('image_upload/'.$slide->file) }}" style="width: 300px; height: 300px; margin-bottom: 20px;" alt="{{'image_upload/'.$slide->file}}">
            <div class="row">
                <div class="form-group col-md-6">
                    <input name="new_file_name" class="form-control" type="file" id="formFile"  accept="image/*" onchange="previewImage(event)" >
                    <input type="hidden" name="current_file" value="{{$slide->file}}">
                </div>
                <div class="form-group col-md-12">
                    <input name="title" type="text" value="{{$slide->title}}" class="form-control" placeholder="Enter Title..." required>
                </div>
                <div class="form-group col-md-12">
                    <textarea name="description" class="form-control" placeholder="Enter Description..." rows="3" required>{{$slide->description}}</textarea>

                </div>
            </div>
            <button type="submit" class="btn btn-primary text-light">Submit</button>
        </form>
        @elseif ($extension == 'mp4' || $extension == 'avi' || $extension == 'mov' || $extension == 'wmv')

        <form class="form" method="post" action="{{route('slide.updateVideo', ['slide' => $slide])}}" enctype="multipart/form-data">
            @if ($errors->any())
                <script>
                    @foreach ($errors->all() as $error)
                        swal({
                            title: "Error!",
                            text: "{{ $error == 'The file name must be an image.' ? 'Please upload a video file.' : $error }}",
                            icon: "error",
                        });
                    @endforeach
                </script>
            @endif
            @csrf
            @method('put')
            <div class="form-group">
                <video id="videoPreview" width="400" height="300" controls>
                    <source src="{{asset('image_upload/'.$slide->file)}}" type="video/mp4">
                    <source src="{{asset('image_upload/'.$slide->file)}}" type="video/ogg">
                    Your browser does not support the video tag.
                </video>
                <input type="hidden" name="user_add_name" value="{{Auth()->user()->first_name." ".Auth()->user()->last_name}}">
                <input type="hidden" name="user_add_email" value="{{Auth()->user()->email}}">
                <input type="hidden" name="user_add_activity" value="{{Auth()->user()->first_name." ".Auth()->user()->last_name." Edit the Slide"}}">
            </div>
            <div class="form-group">
                <label for="">Upload the new video file here</label>
                <input name="new_file_name" required class="form-control" type="file" id="formFile" accept="video/*" onchange="previewVideo(event)" >
                <input type="hidden" name="current_file" value="{{$slide->file}}">
            </div>

            <button type="submit" class="btn btn-primary text-light">Submit</button>
            <a href="{{route('admin.dashboard')}}" class="btn btn-secondary text-light">Back</a>
        </form>

        <script>
            function previewVideo(event) {
                var video = document.getElementById('videoPreview');
                video.innerHTML = ''; // Clear any existing source elements

                var source = document.createElement('source');
                source.setAttribute('src', URL.createObjectURL(event.target.files[0]));
                source.setAttribute('type', 'video/mp4');
                video.appendChild(source);
                video.load(); // Load the new source
            }
        </script>





        @else
        <h1>Document</h1>
        @endif
    </div>
</div>

<script>
    function previewImage(event) {
        var input = event.target;
        var preview = document.getElementById('imagePreview');

        if (input.files && input.files[0]) {
            var reader = new FileReader();

            reader.onload = function(e) {
                preview.src = e.target.result;
                preview.style.display = 'block';
            }

            reader.readAsDataURL(input.files[0]);
        }
    }
    </script>

@endsection
