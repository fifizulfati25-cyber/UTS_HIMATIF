<?php

return [
    'accounts_path' => env('HIMATIF_ACCOUNTS_PATH', storage_path('app/private/himatif/accounts.json')),
    'meetings_path' => env('HIMATIF_MEETINGS_PATH', storage_path('app/private/himatif/meetings.json')),
    'attendance_path' => env('HIMATIF_ATTENDANCE_PATH', storage_path('app/private/himatif/attendance.json')),
];
