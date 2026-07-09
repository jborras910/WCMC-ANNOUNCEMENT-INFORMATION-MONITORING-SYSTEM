@extends('layouts.index')
@section('title', 'Welcome Page')

@section('content')

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@600;700;800;900&display=swap" rel="stylesheet">

    <style>
        body {
            position: relative;
            overflow: hidden;
            padding: 0 !important;
            margin: 0 !important;
            background-color: #000 !important;
            font-family: 'Nunito', sans-serif;
        }

        .video-main-container {
            position: fixed;
            inset: 0;
            overflow: hidden;
            background: #000;
        }

        .logo-badge {
            position: fixed !important;
            left: 18px;
            top: 18px;
            width: 150px !important;
            height: auto !important;
            box-sizing: border-box;
            padding: 10px;

            z-index: 999 !important;
        }

        #videoPlayer {
            position: absolute;
            inset: 0;
            width: 100% !important;
            height: 100% !important;
            object-fit: cover;
            opacity: 0;
            transition: opacity 0.4s ease;
        }

        .muted-chip {
            position: fixed;
            right: 18px;
            bottom: 18px;
            display: flex;
            align-items: center;
            gap: 6px;
            padding: 6px 12px;
            border-radius: 20px;
            background: rgba(4, 20, 34, 0.55);
            backdrop-filter: blur(4px);
            color: #fff;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 0.5px;
            z-index: 999;
        }

        .video-progress {
            position: fixed;
            left: 0;
            right: 0;
            bottom: 0;
            height: 4px;
            background: rgba(255, 255, 255, 0.15);
            z-index: 999;
        }

        .video-progress__fill {
            height: 100%;
            width: 0%;
            background: linear-gradient(90deg, #94d60a, #0384ce, #9363cd);
            transition: width 0.2s linear;
        }

        /* Preloader style */
        #preloader {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: #000;
            z-index: 1000;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .loader-spinner {
            width: 56px;
            height: 56px;
            border-radius: 50%;
            border: 5px solid rgba(255, 255, 255, 0.25);
            border-top-color: #94d60a;
            animation: spin 0.9s linear infinite;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }
    </style>

    <div class="video-main-container">
        <!-- Preloader Overlay -->
        <div id="preloader">
            <div class="loader-spinner"></div>
        </div>

        <img class="logo-badge" src="assets/wcmc_logo_1.png" alt="World Citi Medical Center">

        <video id="videoPlayer" autoplay muted playsinline preload="metadata"></video>

        {{-- <div class="muted-chip">🔇 Muted</div> --}}

        <div class="video-progress">
            <div class="video-progress__fill" id="videoProgressFill"></div>
        </div>
    </div>

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
                var isFirstPlay = !videoPlayer.src;
                var swap = function() {
                    $('#videoProgressFill').css('width', '0%');
                    videoPlayer.src = videoSource[videoNum];
                    videoPlayer.play().then(() => {
                        $('#preloader').fadeOut(200);
                        videoPlayer.style.opacity = 1;
                    }).catch(err => {
                        console.error('Autoplay blocked: ', err);
                    });
                };

                if (isFirstPlay) {
                    swap();
                } else {
                    videoPlayer.style.opacity = 0;
                    setTimeout(swap, 400);
                }
            }

            document.getElementById('videoPlayer').addEventListener('ended', function() {
                i++;
                if (i === videoCount) {
                    i = 0;
                }
                videoPlay(i);
            }, false);

            document.getElementById('videoPlayer').addEventListener('timeupdate', function() {
                if (this.duration) {
                    var pct = (this.currentTime / this.duration) * 100;
                    document.getElementById('videoProgressFill').style.width = pct + '%';
                }
            }, false);

            videoPlay(0); // Play the first video
        });
    </script>

@endsection
