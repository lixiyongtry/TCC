<?php

return [
    'consul' => [
        'address' => '127.0.0.1',
        'port'    => 8500,
        'register' => [
            'ID'                =>'order_1',
            'Name'              =>'order',
            'Tags'              =>['primary'],
            'Address'           =>'106.52.210.201',
            'Port'              =>9802,
            'Check'             => [
                'tcp'      => '106.52.210.201:9802',
                'interval' => '10s',
                'timeout'  => '2s',
            ],
            'Weights'=>[
                'passing'=>5,
                'warning'=>1
            ]
        ],
        'discovery' => [
            'dc' => 'dc1',
            'tag'=>'primary'
        ]
    ],
];