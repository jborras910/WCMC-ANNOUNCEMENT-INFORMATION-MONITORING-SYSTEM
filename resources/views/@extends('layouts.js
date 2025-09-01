@extends('layouts.index')
@section('title', 'Welcome Page')

@section('content')

    <style>
        body {
            position: relative;
            overflow: hidden;
            padding: 0 !important;
            margin: 0 !important;
            background-color: black !important;
        }

        .logo {
            position: fixed !important;
            width: 200px !important;
            left: 10px;
            top: 10px;
            padding-top: 30px !important;
            padding-left: 60px !important;
            z-index: 999 !important;
        }

        video {
            width: 100%;
            height: 100vh;
        }
    </style>

    <img class="logo" src="assets/wcmc_logo_1.png" alt="">

    @if ($slides->isEmpty())
        <script>
            // Reload the page after a short delay
            setTimeout(function() {
                location.reload();
            }, 2000); // Adjust the delay time as needed
        </script>
    @else
        <video id="videoPlayer" autoplay muted></video>
    @endif

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script type='text/javascript'>
        $(document).ready(function() {
            var videoSource = [];
            @foreach ($slides as $slide)
                videoSource.push("{{ asset('image_upload/' . $slide->file) }}");
            @endforeach

            if (videoSource.length === 0) {
                console.error('No video sources found');
                return;
            }

            var i = 0;
            var videoCount = videoSource.length;

            function videoPlay(videoNum) {
                console.log('Playing video: ' + videoSource[videoNum]);
                var videoPlayer = document.getElementById("videoPlayer");
                videoPlayer.src = videoSource[videoNum];
                videoPlayer.load();

                // Set a timeout to handle loading failures
                let videoLoadTimeout = setTimeout(function() {
                    console.error('Video load timeout: ' + videoSource[videoNum]);
                    i++;
                    if (i === videoCount) {
                        i = 0;
                    }
                    videoPlay(i); // Play the next video
                }, 5000); // 5-second timeout for loading the video

                videoPlayer.play().then(() => {
                    console.log('Video started successfully');
                    clearTimeout(videoLoadTimeout); // Clear timeout on success
                }).catch(err => {
                    console.error('Error starting video: ', err);
                });
            }

            // Handle video play errors
            document.getElementById('videoPlayer').addEventListener('error', function() {
                console.error('Video load error: ' + videoSource[i]);
                i++;
                if (i === videoCount) {
                    i = 0;
                }
                videoPlay(i); // Try the next video
            });

            document.getElementById('videoPlayer').addEventListener('ended', function() {
                i++;
                if (i === videoCount) {
                    i = 0;
                }
                videoPlay(i);
            }, false);

            videoPlay(0); // Play the first video
        });
    </script>

@endsection
