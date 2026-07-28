<?php

return [

    // When set (to a department's slug, e.g. "ims"), the combined "/" and
    // "/welcome-queue" lobby screens — and the /slides/current polling they
    // both use — only show that one department's slides, instead of every
    // department's. Leave unset to keep the combined multi-department feed.
    // Useful when a physical TV/box is permanently dedicated to one
    // department, so its default URL doesn't need to be /display/{slug}.
    'default_department' => env('DEFAULT_DISPLAY_DEPARTMENT'),

];
