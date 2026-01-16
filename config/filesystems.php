<?php

return [

    'disks' => [
        'local'    => [
            'driver' => 'local',
            'root'   => storage_path('app'),
            'throw'  => false,
        ],

        'receipts' => [
            'driver' => 'local',
            'root'   => storage_path('app/receipts'),
            'throw'  => false,
        ],
    ],

];
