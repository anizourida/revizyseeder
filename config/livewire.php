<?php

return [
    'temporary_file_upload' => [
        'disk' => 'local',        // Default: null
        'rules' => 'file|max:102400', // 100MB instead of 12MB
        'directory' => 'livewire-tmp',
        'middleware' => ['web'],
        'preview_mimes' => [
            'png', 'gif', 'bmp', 'svg', 'wav', 'mp4',
            'mov', 'avi', 'wmv', 'mp3', 'm4a',
            'jpg', 'jpeg', 'mpga', 'webp',
        ],
        'max_upload_time' => 10, // 10 minutes
    ],
];
