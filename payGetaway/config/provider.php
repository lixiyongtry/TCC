<?php

return [
    'consul' => [
        'address' => '127.0.0.1',
        'port'    => 8500,
        'register' => [
            'ID'                =>'payxxx',
            'Name'              =>'payxxx',
            'Tags'              =>['primary'],
            'Address'           =>'106.52.210.201',
            'Port'              =>9805,
            'Check'             => [
                'tcp'      => '106.52.210.201:9801',
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
            'passing'=>true,
            'tag'=>'primary'
        ]
    ],
];