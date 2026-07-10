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
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 16px;
        }

        .loader-spinner {
            width: 56px;
            height: 56px;
            border-radius: 50%;
            border: 5px solid rgba(255, 255, 255, 0.25);
            border-top-color: var(--wcmc-green);
            animation: spin 0.9s linear infinite;
        }

        .preloader-label {
            color: #fff;
            font-weight: 700;
            font-size: 14px;
            letter-spacing: 0.5px;
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

        /* Queue feed unreachable — swap the pulsing "live" dot for a still, muted
           one so the header itself signals staleness without disrupting layout. */
        .live-dot.is-offline {
            background: #b0b8bf;
            box-shadow: none;
            animation: none;
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

        /* Skeleton placeholder cards — shown before the first real queue data
           arrives (or while reconnecting with nothing cached yet) instead of a
           bare "Loading…" text. */
        .skeleton-card {
            border-top-color: #e3eaf1;
        }

        .skeleton-bar {
            display: block;
            border-radius: 6px;
            background: linear-gradient(90deg, #e7eef5 25%, #f3f8fc 37%, #e7eef5 63%);
            background-size: 400% 100%;
            animation: skeletonShimmer 1.4s ease-in-out infinite;
        }

        .skeleton-bar--name {
            width: 65%;
            height: 9px;
        }

        .skeleton-bar--number {
            width: 45%;
            height: clamp(24px, 2.8vw, 34px);
            margin-top: 10px;
        }

        .skeleton-bar--window {
            width: 38%;
            height: 8px;
            margin-top: 10px;
        }

        @keyframes skeletonShimmer {
            0% {
                background-position: 100% 50%;
            }

            100% {
                background-position: 0 50%;
            }
        }

        /* Prominent reconnecting banner — shown whenever the AQS feed is
           unreachable, even while last-known numbers are still on screen below
           it, so staleness is obvious at a glance and not just a footnote. */
        .reconnect-banner {
            display: none;
            align-items: center;
            justify-content: center;
            gap: 8px;
            background: rgba(192, 57, 43, 0.12);
            color: #c0392b;
            font-weight: 800;
            font-size: clamp(10px, 0.95vw, 12px);
            letter-spacing: 0.3px;
            text-align: center;
            padding: 8px 10px;
            border-radius: 10px;
            margin-bottom: clamp(10px, 1.4vw, 14px);
        }

        .reconnect-banner.is-visible {
            display: flex;
        }

        .reconnect-banner__dot {
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background: #c0392b;
            flex-shrink: 0;
            animation: livePulse 1.6s ease-out infinite;
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
            <img src="assets/icon 1.png" alt="World Citi Medical Center">
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

                {{-- <div class="muted-chip">🔇 Muted</div> --}}

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
                <div class="reconnect-banner" id="reconnectBanner">
                    <span class="reconnect-banner__dot"></span>
                    <span>Reconnecting to queue system…</span>
                </div>
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
            var consecutiveFailures = 0;

            // The lobby TVs are on WiFi only, which drops mid-stream. Instead of
            // letting <video> stream over the network on every loop, we fetch each
            // clip once into a local Blob and play from that in-memory copy — once
            // downloaded, playback no longer depends on the connection staying up.
            var blobUrlCache = new Map(); // source URL -> local object URL
            var pendingFetches = new Map(); // source URL -> in-flight download promise
            var CACHE_NAME = 'wcmc-signage-video-cache-v1';
            var hasCacheStorage = ('caches' in
                window); // needs HTTPS/localhost; degrade gracefully on plain HTTP LAN
            var FETCH_TIMEOUT_MS = 15000;
            var MAX_ATTEMPTS = 3;

            function delay(ms) {
                return new Promise(function(resolve) {
                    setTimeout(resolve, ms);
                });
            }

            function fetchWithTimeout(url, timeoutMs) {
                var controller = new AbortController();
                var timer = setTimeout(function() {
                    controller.abort();
                }, timeoutMs);
                return fetch(url, {
                    signal: controller.signal
                }).finally(function() {
                    clearTimeout(timer);
                });
            }

            async function downloadVideo(url) {
                var lastErr = null;
                for (var attempt = 1; attempt <= MAX_ATTEMPTS; attempt++) {
                    try {
                        var response = await fetchWithTimeout(url, FETCH_TIMEOUT_MS);
                        if (!response.ok) throw new Error('HTTP ' + response.status);

                        if (hasCacheStorage) {
                            try {
                                var cache = await caches.open(CACHE_NAME);
                                await cache.put(url, response.clone());
                            } catch (e) {
                                console.warn('Cache Storage write failed for', url, e);
                            }
                        }

                        var blob = await response.blob();
                        var objectUrl = URL.createObjectURL(blob);
                        blobUrlCache.set(url, objectUrl);
                        return objectUrl;
                    } catch (err) {
                        lastErr = err;
                        console.warn('Video download attempt ' + attempt + ' failed for ' + url, err);
                        if (attempt < MAX_ATTEMPTS) {
                            await delay(1000 * Math.pow(2, attempt - 1)); // 1s, 2s, 4s backoff
                        }
                    }
                }
                throw lastErr;
            }

            function getVideoUrl(url) {
                if (blobUrlCache.has(url)) {
                    return Promise.resolve(blobUrlCache.get(url));
                }
                if (pendingFetches.has(url)) {
                    return pendingFetches.get(url);
                }

                var promise = (async function() {
                    if (hasCacheStorage) {
                        try {
                            var cache = await caches.open(CACHE_NAME);
                            var cached = await cache.match(url);
                            if (cached) {
                                var blob = await cached.blob();
                                var objectUrl = URL.createObjectURL(blob);
                                blobUrlCache.set(url, objectUrl);
                                return objectUrl;
                            }
                        } catch (e) {
                            console.warn('Cache Storage read failed for', url, e);
                        }
                    }
                    return downloadVideo(url);
                })();

                pendingFetches.set(url, promise);
                promise.finally(function() {
                    pendingFetches.delete(url);
                });
                return promise;
            }

            function preloadNext(videoNum) {
                var nextIndex = (videoNum + 1) % videoCount;
                getVideoUrl(videoSource[nextIndex]).catch(function(err) {
                    console.warn('Preload failed for', videoSource[nextIndex], err);
                });
            }

            // A broken/missing file or a stuck stall must not freeze the display
            // forever — skip to the next slide instead. If every slide fails (the
            // whole connection is down), keep retrying on a timer instead of giving
            // up — the lobby TVs are WiFi-only and unreachable remotes shouldn't be
            // the only way to recover once the network comes back.
            var retryAllTimer = null;

            function handleFailureAndAdvance() {
                consecutiveFailures++;
                if (consecutiveFailures >= videoCount) {
                    console.error('All video sources failed to load. Retrying automatically...');
                    $('#preloader').html(
                        '<div class="loader-spinner"></div><div class="preloader-label">Reconnecting…</div>'
                    ).fadeIn(200);
                    scheduleRetryAll();
                    return;
                }
                playNext();
            }

            function scheduleRetryAll() {
                if (retryAllTimer) return;
                retryAllTimer = setTimeout(function() {
                    retryAllTimer = null;
                    consecutiveFailures = 0;
                    videoPlay(i);
                }, 10000);
            }

            // Fires as soon as the browser regains network connectivity — try
            // immediately instead of waiting out the rest of the retry interval.
            window.addEventListener('online', function() {
                if (!retryAllTimer) return;
                clearTimeout(retryAllTimer);
                retryAllTimer = null;
                consecutiveFailures = 0;
                videoPlay(i);
            });

            function playNext() {
                i++;
                if (i === videoCount) {
                    i = 0;
                }
                videoPlay(i);
            }

            function videoPlay(videoNum) {
                var videoPlayer = document.getElementById("videoPlayer");
                var isFirstPlay = !videoPlayer.src;

                var swap = async function() {
                    $('#videoProgressFill').css('width', '0%');

                    var objectUrl;
                    try {
                        objectUrl = await getVideoUrl(videoSource[videoNum]);
                    } catch (err) {
                        console.error('Failed to load video after retries: ', videoSource[videoNum], err);
                        handleFailureAndAdvance();
                        return;
                    }

                    consecutiveFailures = 0;
                    videoPlayer.src = objectUrl;
                    videoPlayer.play().then(() => {
                        $('#preloader').fadeOut(200);
                        videoPlayer.style.opacity = 1;
                        preloadNext(videoNum);
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
                playNext();
            }, false);

            // Playback is blob-backed, so it shouldn't stall on the network once
            // loaded — this is just a safety net against a stuck decode/render.
            var stallTimer = null;

            function clearStallTimer() {
                if (stallTimer) {
                    clearTimeout(stallTimer);
                    stallTimer = null;
                }
            }

            document.getElementById('videoPlayer').addEventListener('waiting', function() {
                if (stallTimer) return;
                stallTimer = setTimeout(function() {
                    console.warn('Video stalled, skipping to next slide.');
                    stallTimer = null;
                    handleFailureAndAdvance();
                }, 8000);
            }, false);

            document.getElementById('videoPlayer').addEventListener('playing', clearStallTimer, false);

            document.getElementById('videoPlayer').addEventListener('error', function() {
                console.error('Video playback error for: ', videoSource[i]);
                clearStallTimer();
                handleFailureAndAdvance();
            }, false);

            document.getElementById('videoPlayer').addEventListener('timeupdate', function() {
                clearStallTimer();
                if (this.duration) {
                    var pct = (this.currentTime / this.duration) * 100;
                    document.getElementById('videoProgressFill').style.width = pct + '%';
                }
            }, false);

            videoPlay(0); // Play the first video

            // The video list above is only what the server handed us on page
            // load — a slide added, edited, or (re)approved afterwards would
            // otherwise never show up until someone physically reloads the TV.
            // Poll for the current list instead and swap it in live.
            var SLIDE_LIST_URL = '{{ route('slides.current') }}';
            var SLIDE_LIST_POLL_INTERVAL = 60000;

            function refreshSlideList() {
                fetch(SLIDE_LIST_URL, {
                        cache: 'no-store'
                    })
                    .then(function(res) {
                        return res.ok ? res.json() : Promise.reject(new Error('HTTP ' + res.status));
                    })
                    .then(function(data) {
                        var freshUrls = (data.videos || []).map(function(v) {
                            return v.url;
                        });
                        // Don't blank out a working display over a transient empty/bad response.
                        if (freshUrls.length === 0) return;

                        var changed = freshUrls.length !== videoSource.length ||
                            freshUrls.some(function(url, idx) {
                                return url !== videoSource[idx];
                            });
                        if (!changed) return;

                        // Drop cached blobs for videos no longer in the list so memory
                        // doesn't grow unbounded over days/weeks of uptime.
                        var freshSet = {};
                        freshUrls.forEach(function(u) {
                            freshSet[u] = true;
                        });
                        blobUrlCache.forEach(function(objectUrl, url) {
                            if (!freshSet[url]) {
                                URL.revokeObjectURL(objectUrl);
                                blobUrlCache.delete(url);
                            }
                        });

                        videoSource = freshUrls;
                        videoCount = videoSource.length;
                        if (i >= videoCount) i = 0;
                        console.log('Slide list updated — ' + videoCount + ' video(s) now available.');
                    })
                    .catch(function(err) {
                        console.warn('Could not refresh slide list: ', err);
                    });
            }

            setInterval(refreshSlideList, SLIDE_LIST_POLL_INTERVAL);

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
            var lastSeenNumbers = {}; // section -> queue_code, used to detect changes for the pulse

            function renderQueueSkeleton(count) {
                var $grid = $('#sectionGrid');
                var card = '<div class="section-card skeleton-card">' +
                    '<div class="section-card__top"><span class="skeleton-bar skeleton-bar--name"></span></div>' +
                    '<span class="skeleton-bar skeleton-bar--number"></span>' +
                    '<span class="skeleton-bar skeleton-bar--window"></span>' +
                    '</div>';
                $grid.html(card.repeat(count));
            }

            function renderQueueSections(sections) {
                var $grid = $('#sectionGrid');

                if (!sections || sections.length === 0) {
                    $grid.html('<div class="queue-panel__empty">No active queues right now.</div>');
                    $('#sectionSummary').text('0 active sections');
                    return;
                }

                // Clear the initial "Loading…" placeholder / skeleton cards once real data arrives.
                $grid.find('.queue-panel__empty, .skeleton-card').remove();

                $('#sectionSummary').text(sections.length + ' active section' + (sections.length === 1 ? '' : 's'));

                sections.forEach(function(section) {
                    var key = section.section;
                    var $card = $grid.find('[data-section="' + key + '"]');
                    var changed = lastSeenNumbers[key] !== undefined && lastSeenNumbers[key] !== section
                        .queue_code;
                    lastSeenNumbers[key] = section.queue_code;

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

                    var $number = $card.find('.section-card__number').text(section.queue_code);
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

            // While the AQS feed is unreachable, swap the section cards for
            // skeleton placeholders rather than leaving potentially-stale numbers
            // on screen, plus signal staleness through the reconnect banner +
            // muted live-dot + footer note. Recovery is automatic: the poll below
            // just keeps retrying every QUEUE_POLL_INTERVAL and clears the
            // offline state the moment a good response comes back in.
            function markQueueOffline() {
                $('#queueFooter').addClass('is-offline');
                $('.live-dot').addClass('is-offline');
                $('#reconnectBanner').addClass('is-visible');

                // Swap whatever's on screen — including last-known real numbers —
                // for skeleton placeholders while the feed is down, so nobody acts
                // on a queue number that might no longer be accurate. Guarded so
                // we don't re-render every failed poll once skeletons are already showing.
                var $grid = $('#sectionGrid');
                if ($grid.find('.skeleton-card').length === 0) {
                    renderQueueSkeleton(4);
                    $('#sectionSummary').text('Loading…');
                }
            }

            function markQueueOnline() {
                $('#queueFooter').removeClass('is-offline');
                $('.live-dot').removeClass('is-offline');
                $('#reconnectBanner').removeClass('is-visible');
                $('#lastUpdatedText').text('Last updated: ' + new Date().toLocaleTimeString('en-PH', {
                    hour: '2-digit',
                    minute: '2-digit',
                    second: '2-digit',
                    hour12: true
                }));
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
                        markQueueOnline();
                    } else {
                        console.error('Queue API returned an unsuccessful response: ', response);
                        markQueueOffline();
                    }
                }).fail(function(err) {
                    console.error('Queue API unreachable: ', err);
                    markQueueOffline();
                });
            }

            renderQueueSkeleton(4); // show placeholders immediately, before the first response lands
            fetchQueueStatus();
            setInterval(fetchQueueStatus, QUEUE_POLL_INTERVAL);
        });
    </script>

@endsection
