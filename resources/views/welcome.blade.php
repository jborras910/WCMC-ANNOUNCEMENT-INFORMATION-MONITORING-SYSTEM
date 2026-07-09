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
            border-top-color: #94d60a;
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
            var consecutiveFailures = 0;

            // The lobby TVs are on WiFi only, which drops mid-stream. Instead of
            // letting <video> stream over the network on every loop, we fetch each
            // clip once into a local Blob and play from that in-memory copy — once
            // downloaded, playback no longer depends on the connection staying up.
            var blobUrlCache = new Map(); // source URL -> local object URL
            var pendingFetches = new Map(); // source URL -> in-flight download promise
            var CACHE_NAME = 'wcmc-signage-video-cache-v1';
            var hasCacheStorage = ('caches' in window); // needs HTTPS/localhost; degrade gracefully on plain HTTP LAN
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
        });
    </script>

@endsection
