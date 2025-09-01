@extends('admin.index')
@section('title', 'Add Slide')

<!-- include summernote css/js -->
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.js"></script>


<style>
    .form-group input,
    .form-group textarea {
        box-shadow: rgba(0, 0, 0, 0.02) 0px 1px 3px 0px, rgba(27, 31, 35, 0.15) 0px 0px 0px 1px;
    }

    #imagePreview {
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

            <!-- Modal -->
            <div class="modal fade" id="userGuideModalCenter" tabindex="-1" role="dialog"
                aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLongTitle">Steps for converting file to mpeg4</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <li>Step 1: Open your PowerPoint</li>
                            <li>Step 2: Paste Image,Documents, or PDF File</li>
                            <li>Step 3: Save as mpeg4 (.mp4)</li>
                            <li>Step 4: Upload the File</li>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-primary btn-block text-light"
                                data-dismiss="modal">Done</button>

                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-between align-self-stretch">
                <h3>Add new slide</h3>
                <button class="btn btn btn-info " data-toggle="modal" data-target="#userGuideModalCenter">User
                    Guide</button>
            </div>
            <hr>

            <div class="slide-container" id="add_Image_slide">


                <form class="mt-5" method="post" action="{{ route('addVideoslide.post') }}"
                    enctype="multipart/form-data">
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
                    <video id="videoPreview" width="320" height="240" controls style="display:none">
                        Your browser does not support the video tag.
                    </video>
                    <div class="row mt-4">
                        <label for="">Only .mp4 file is accepted</label>
                        <div class="form-group col-md-12">
                            <input name="file_name" class="form-control" type="file" id="formFile" accept="video/*"
                                required onchange="previewVideo(event)">
                            <input type="hidden" name="added_by_email" value="{{ Auth()->user()->email }}">

                            <input type="hidden" name="department" value="{{ Auth()->user()->department }}">

                            <input type="hidden" name="user_add_name"
                                value="{{ Auth()->user()->first_name . ' ' . Auth()->user()->last_name }}">
                            <input type="hidden" name="user_add_email" value="{{ Auth()->user()->email }}">
                            <input type="hidden" name="user_add_activity"
                                value="{{ Auth()->user()->first_name . ' ' . Auth()->user()->last_name . ' Added Slide' }}">
                        </div>
                    </div>
                    <button type="submit" id="submitButton" class="btn btn-primary text-light">Submit</button>
                    <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary text-light">Back</a>
                </form>

                <script>
                    function previewVideo(event) {
                        // Check file size
                        const file = event.target.files[0];
                        const maxSize = 100 * 1024 * 1024; // 40MB in bytes
                        if (file.size > maxSize) {
                            // alert('File size exceeds 40MB limit.');
                            Swal.fire({
                                title: "Invalid!",
                                text: "File size exceeds 40MB limit.",
                                icon: "error",
                            });
                            event.target.value = ''; // Clear the file input
                            return; // Exit the function early
                        }

                        // If the file size is within the limit, proceed with previewing the video
                        var video = document.getElementById('videoPreview');
                        video.innerHTML = ''; // Clear any existing source elements

                        var source = document.createElement('source');
                        source.setAttribute('src', URL.createObjectURL(file));
                        source.setAttribute('type', 'video/mp4');
                        video.appendChild(source);
                        video.load(); // Load the new source
                        video.style.display = 'block';
                    }
                </script>
            </div>
            <div class="slide-container" id="add_video_slide" style="display: none;">

            </div>
            <div class="slide-container" id="add_document_slide" style="display: none;">
                <form class="mt-5" method="post" enctype="multipart/form-data">
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
                    <img id="imagePreview" src="#" alt="Image Preview">
                    <div class="row">
                        <label for="">Add document Slide</label>
                        <div class="form-group col-md-12">
                            <input name="file_name" class="form-control" type="file" id="formFile"
                                accept="application/pdf,.doc,.docx,.ppt,.pptx,.xls,.xlsx,.txt,.csv,.zip"
                                onchange="previewFile(event)" required>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary text-light">Submit</button>
                </form>
            </div>
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

        // Function to show the selected slide and hide others
        function showSlide(slideId, buttonText) {
            // Hide all slide containers
            var slideContainers = document.getElementsByClassName('slide-container');
            for (var i = 0; i < slideContainers.length; i++) {
                slideContainers[i].style.display = 'none';
            }

            // Show the selected slide container
            document.getElementById(slideId).style.display = 'block';

            // Change the button text
            document.getElementById('dropdownMenuButton').innerText = buttonText;
        }

        // Show the default active slide on page load
        window.onload = function() {
            showSlide('add_Image_slide', 'Add Image Slide');
        };
    </script>


    <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js"
        integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous">
    </script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"
        integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous">
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/js/bootstrap.min.js"
        integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous">
    </script>

@endsection
