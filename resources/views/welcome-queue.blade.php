@extends('layouts.index')
@section('title', 'Welcome Page - Queue')

@section('content')

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800;900&display=swap" rel="stylesheet">

    <style>
        :root {
            --wcmc-blue: #0384ce;
            --wcmc-blue-dark: #036298;
            --wcmc-purple: #9363cd;
            --wcmc-green: #94d60a;
            --wcmc-slate: #455360;
            --wcmc-slate-light: #5f666d;
            --wcmc-bg-light: #ebf4fb;
            --wcmc-bg-lighter: #f6fbff;
            --header-h: clamp(64px, 7vh, 88px);
            --panel-w: clamp(300px, 20vw, 420px);
        }

        * {
            box-sizing: border-box;
        }

        body {
            position: relative;
            overflow: hidden;
            padding: 0 !important;
            margin: 0 !important;
            background-color: #06263a !important;
            font-family: 'Nunito', sans-serif;
        }

        .display-wrapper {
            position: relative;
            width: 100vw;
            height: 100vh;
        }

        /* Top brand bar — ties the video and queue panel into one screen */
        .top-bar {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            height: var(--header-h);
            background: #ffffff;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 clamp(16px, 2vw, 32px);
            box-shadow: 0 2px 10px rgba(4, 30, 48, 0.12);
            z-index: 1002;
        }

        .top-bar__brand {
            display: flex;
            align-items: center;
            gap: clamp(10px, 1.2vw, 18px);
            min-width: 0;
        }

        .top-bar__brand img {
            height: clamp(32px, 4vh, 46px);
            width: auto;
            flex-shrink: 0;
        }

        .top-bar__title {
            display: flex;
            flex-direction: column;
            line-height: 1.15;
            white-space: nowrap;
        }

        .top-bar__title strong {
            font-size: clamp(14px, 1.5vw, 19px);
            font-weight: 800;
            color: var(--wcmc-slate);
        }

        .top-bar__title span {
            font-size: clamp(10px, 0.9vw, 12px);
            font-weight: 700;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            color: var(--wcmc-purple);
        }

        .top-bar__clock {
            text-align: right;
            flex-shrink: 0;
        }

        .top-bar__clock .time {
            font-size: clamp(16px, 1.8vw, 24px);
            font-weight: 800;
            color: var(--wcmc-blue);
            line-height: 1.1;
        }

        .top-bar__clock .date {
            font-size: clamp(10px, 0.9vw, 13px);
            font-weight: 700;
            color: var(--wcmc-slate-light);
        }

        .video-main-container {
            position: fixed;
            top: var(--header-h);
            left: 0;
            bottom: 0;
            right: var(--panel-w);
            overflow: hidden;
            background: #000;
        }

        .video-frame {
            position: relative;
            width: 100%;
            height: 100%;
            overflow: hidden;
        }

        #videoPlayer {
            position: absolute;
            inset: 0;
            width: 100% !important;
            height: 100% !important;
            object-fit: fill !important;
            /* padding: 10px !important; */
            /* Ipakita ang buong video */
            background-color: none !important;
            /* Itim na background para sa top/bottom space */
            opacity: 0;
            transition: opacity 0.4s ease;
        }

        .muted-chip {
            position: absolute;
            right: 14px;
            bottom: 22px;
            display: flex;
            align-items: center;
            gap: 6px;
            padding: 6px 12px;
            border-radius: 20px;
            background: rgba(4, 20, 34, 0.55);
            border: 1px solid rgba(147, 99, 205, 0.4);
            backdrop-filter: blur(4px);
            color: #fff;
            font-size: clamp(10px, 0.9vw, 12px);
            font-weight: 700;
            letter-spacing: 0.5px;
            z-index: 3;
        }

        .video-progress {
            position: absolute;
            left: 0;
            right: 0;
            bottom: 0;
            height: 4px;
            background: rgba(255, 255, 255, 0.15);
            z-index: 3;
        }

        .video-progress__fill {
            height: 100%;
            width: 0%;
            background: linear-gradient(90deg, var(--wcmc-green), var(--wcmc-blue), var(--wcmc-purple));
            transition: width 0.2s linear;
        }

        /* Preloader */
        #preloader {
            position: absolute;
            inset: 0;
            background: transparent;
            z-index: 10;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .loader-spinner {
            width: 56px;
            height: 56px;
            border-radius: 50%;
            border: 5px solid rgba(255, 255, 255, 0.25);
            border-top-color: var(--wcmc-green);
            animation: spin 0.9s linear infinite;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        /* Queue side panel */
        .queue-panel {
            position: fixed;
            top: var(--header-h);
            right: 0;
            width: var(--panel-w);
            height: calc(100vh - var(--header-h));
            background: var(--wcmc-bg-lighter);
            color: var(--wcmc-slate);
            display: flex;
            flex-direction: column;
            font-family: 'Nunito', sans-serif;
            box-shadow: -4px 0 12px rgba(0, 0, 0, 0.1);
            z-index: 1001;
        }

        .queue-panel__header {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            padding: clamp(14px, 1.8vw, 20px) 10px;
            background: linear-gradient(135deg, var(--wcmc-blue) 0%, var(--wcmc-purple) 130%);
            color: #fff;
        }

        .queue-panel__header h2 {
            margin: 0;
            font-size: clamp(15px, 1.5vw, 20px);
            font-weight: 800;
            letter-spacing: 2px;
            text-transform: uppercase;
        }

        .live-dot {
            width: 9px;
            height: 9px;
            border-radius: 50%;
            background: var(--wcmc-green);
            box-shadow: 0 0 0 rgba(148, 214, 10, 0.6);
            animation: livePulse 1.6s ease-out infinite;
            flex-shrink: 0;
        }

        @keyframes livePulse {
            0% {
                box-shadow: 0 0 0 0 rgba(148, 214, 10, 0.6);
            }

            70% {
                box-shadow: 0 0 0 8px rgba(148, 214, 10, 0);
            }

            100% {
                box-shadow: 0 0 0 0 rgba(148, 214, 10, 0);
            }
        }

        .queue-panel__body {
            flex: 1 1 auto;
            padding: clamp(14px, 2vw, 20px);
            overflow-y: auto;
        }

        .queue-panel__empty {
            text-align: center;
            color: var(--wcmc-slate-light);
            font-weight: 700;
            font-size: 13px;
            padding: 30px 10px;
            grid-column: 1 / -1;
        }

        .section-summary {
            font-size: clamp(11px, 1vw, 13px);
            font-weight: 800;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            color: var(--wcmc-purple);
            margin-bottom: clamp(10px, 1.4vw, 14px);
        }

        .section-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            grid-auto-rows: 1fr;
            gap: clamp(10px, 1.2vw, 14px);
        }

        .section-card {
            display: flex;
            flex-direction: column;
            background: #fff;
            border-radius: 14px;
            padding: clamp(10px, 1.4vw, 16px) 10px;
            text-align: center;
            box-shadow: 0 4px 14px rgba(4, 60, 90, 0.08);
            border-top: 4px solid var(--wcmc-purple);
            animation: cardIn 0.3s ease;
        }

        @keyframes cardIn {
            from {
                opacity: 0;
                transform: translateY(6px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .section-card.is-priority {
            border-top-color: var(--wcmc-green);
        }

        .section-card__top {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 6px;
            margin-bottom: 4px;
        }

        .section-card__name {
            font-size: clamp(9px, 0.9vw, 11px);
            font-weight: 800;
            letter-spacing: 1px;
            text-transform: uppercase;
            color: var(--wcmc-slate-light);
            text-align: left;
            overflow-wrap: break-word;
            line-height: 1.25;
        }

        .section-card__badge {
            flex-shrink: 0;
            background: var(--wcmc-green);
            color: #123;
            font-size: 8px;
            font-weight: 800;
            letter-spacing: 0.5px;
            padding: 2px 6px;
            border-radius: 8px;
            text-transform: uppercase;
            white-space: nowrap;
        }

        .section-card__number {
            font-size: clamp(28px, 3.4vw, 42px);
            font-weight: 900;
            line-height: 1;
            color: var(--wcmc-blue);
            transition: transform 0.25s ease, color 0.25s ease;
            margin-top: auto;
        }

        .section-card__number.pulse {
            animation: queuePulse 1s ease;
        }

        @keyframes queuePulse {
            0% {
                transform: scale(1);
                color: var(--wcmc-blue);
            }

            40% {
                transform: scale(1.15);
                color: var(--wcmc-green);
            }

            100% {
                transform: scale(1);
                color: var(--wcmc-blue);
            }
        }

        .section-card__window {
            margin-top: 4px;
            font-size: clamp(9px, 0.85vw, 11px);
            font-weight: 700;
            color: var(--wcmc-slate-light);
        }

        .queue-panel__footer {
            text-align: center;
            font-size: clamp(11px, 1vw, 13px);
            font-weight: 600;
            color: var(--wcmc-slate-light);
            padding: 12px;
            border-top: 1px solid rgba(9, 54, 84, 0.08);
        }

        .queue-panel__footer .offline-note {
            color: #c0392b;
            font-weight: 800;
            display: none;
        }

        .queue-panel__footer.is-offline .offline-note {
            display: block;
        }
    </style>

    <div class="top-bar">
        <div class="top-bar__brand">
            <img src="assets/wcmc_logo_1.png" alt="World Citi Medical Center">
            <div class="top-bar__title">
                <strong>World Citi Medical Center</strong>
                <span>Announcement & Information Monitoring</span>
            </div>
        </div>
        <div class="top-bar__clock">
            <div class="time" id="clockTime">--:--</div>
            <div class="date" id="clockDate">-</div>
        </div>
    </div>

    <div class="display-wrapper">
        <div class="video-main-container">
            <div class="video-frame">
                <div id="preloader">
                    <div class="loader-spinner"></div>
                </div>

                <video id="videoPlayer" autoplay muted playsinline preload="metadata"></video>

                <div class="muted-chip">🔇 Muted</div>

                <div class="video-progress">
                    <div class="video-progress__fill" id="videoProgressFill"></div>
                </div>
            </div>
        </div>

        <!-- Queue side panel — live data polled from the AQS API -->
        <div class="queue-panel">
            <div class="queue-panel__header">
                <span class="live-dot"></span>
                <h2>Now Serving</h2>
            </div>

            <div class="queue-panel__body">
                <div class="section-summary" id="sectionSummary">Loading…</div>
                <div class="section-grid" id="sectionGrid">
                    <div class="queue-panel__empty">Loading queue status…</div>
                </div>
            </div>

            <div class="queue-panel__footer" id="queueFooter">
                <span id="lastUpdatedText">Please wait for your number to be called.</span>
                <div class="offline-note">Queue feed unavailable — showing last known data.</div>
            </div>
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

            // Live clock
            function tickClock() {
                var now = new Date();
                $('#clockTime').text(now.toLocaleTimeString('en-PH', {
                    hour: '2-digit',
                    minute: '2-digit',
                    hour12: true
                }));
                $('#clockDate').text(now.toLocaleDateString('en-PH', {
                    weekday: 'long',
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric'
                }));
            }
            tickClock();
            setInterval(tickClock, 1000);

            // Live queue status — polls the hospital's AQS (Automated Queuing System) API.
            // Update this URL if the queuing server's address changes.
            var QUEUE_API_URL = 'http://192.168.0.228:8989/api/aqs/current';
            var QUEUE_POLL_INTERVAL = 6000;
            var lastSeenNumbers = {}; // section -> current_number, used to detect changes for the pulse

            function renderQueueSections(sections) {
                var $grid = $('#sectionGrid');

                if (!sections || sections.length === 0) {
                    $grid.html('<div class="queue-panel__empty">No active queues right now.</div>');
                    $('#sectionSummary').text('0 active sections');
                    return;
                }

                // Clear the initial "Loading…" placeholder once real data arrives.
                $grid.find('.queue-panel__empty').remove();

                $('#sectionSummary').text(sections.length + ' active section' + (sections.length === 1 ? '' : 's'));

                sections.forEach(function(section) {
                    var key = section.section;
                    var $card = $grid.find('[data-section="' + key + '"]');
                    var changed = lastSeenNumbers[key] !== undefined && lastSeenNumbers[key] !== section.current_number;
                    lastSeenNumbers[key] = section.current_number;

                    if ($card.length === 0) {
                        $card = $('<div>')
                            .addClass('section-card')
                            .attr('data-section', key)
                            .appendTo($grid);

                        var $top = $('<div>').addClass('section-card__top').appendTo($card);
                        $top.append($('<span>').addClass('section-card__name'));

                        $card.append($('<div>').addClass('section-card__number'));
                        $card.append($('<div>').addClass('section-card__window'));
                    }

                    $card.toggleClass('is-priority', !!section.is_priority);

                    var $top = $card.find('.section-card__top');
                    $top.find('.section-card__badge').remove();
                    if (section.is_priority) {
                        $top.append($('<span>').addClass('section-card__badge').text('Priority'));
                    }

                    $card.find('.section-card__name').text(section.section);
                    $card.find('.section-card__window').text(section.window ? section.window : '');

                    var $number = $card.find('.section-card__number').text(section.current_number);
                    if (changed) {
                        $number.removeClass('pulse');
                        void $number[0].offsetWidth; // restart animation
                        $number.addClass('pulse');
                    }
                });

                // Drop cards for sections no longer present in the feed.
                var activeKeys = sections.map(function(s) {
                    return s.section;
                });
                $grid.find('.section-card').each(function() {
                    var key = $(this).attr('data-section');
                    if (activeKeys.indexOf(key) === -1) {
                        $(this).remove();
                        delete lastSeenNumbers[key];
                    }
                });
            }

            function fetchQueueStatus() {
                $.ajax({
                    url: QUEUE_API_URL,
                    method: 'GET',
                    dataType: 'json',
                    timeout: 5000
                }).done(function(response) {
                    if (response && response.success) {
                        renderQueueSections(response.data);
                        $('#queueFooter').removeClass('is-offline');
                        $('#lastUpdatedText').text('Last updated: ' + new Date().toLocaleTimeString('en-PH', {
                            hour: '2-digit',
                            minute: '2-digit',
                            second: '2-digit',
                            hour12: true
                        }));
                    }
                }).fail(function(err) {
                    console.error('Queue API unreachable: ', err);
                    $('#queueFooter').addClass('is-offline');
                });
            }

            fetchQueueStatus();
            setInterval(fetchQueueStatus, QUEUE_POLL_INTERVAL);
        });
    </script>

@endsection
