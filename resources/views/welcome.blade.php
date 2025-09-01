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
            width: 100vw !important;
            height: 100vh !important;
        }

        /* Preloader style */
        #preloader {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: black;
            z-index: 1000;
            display: flex;
            align-items: center;
            justify-content: center;
        }
    </style>

    <!-- Preloader Overlay -->
    <div id="preloader"></div>

    <img class="logo" src="assets/wcmc_logo_1.png" alt="">


    <video id="videoPlayer" autoplay muted playsinline preload="metadata"></video>




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
                var videoPlayer = document.getElementById("videoPlayer");
                videoPlayer.src = videoSource[videoNum];

                videoPlayer.play().then(() => {
                    $('#preloader').hide();
                }).catch(err => {
                    console.error('Autoplay blocked: ', err);
                });
            }



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
