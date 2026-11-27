<?php

return [
    'enabled' => env('IXPM_OPTICS_ENABLED', false),
    'switch_id' => env('IXPM_OPTICS_SWITCH_ID', 0),

    // Optional comma-separated list of enabled switch IDs.
    'switch_ids' => env('IXPM_OPTICS_SWITCH_IDS', ''),

];
