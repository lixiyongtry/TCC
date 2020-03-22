<?php

/*
 * This file is part of Swoft.
 * (c) Swoft <group@swoft.org>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
return [
    'consul' => [
        'address' => '127.0.0.1',
        'port'    => 8500,
        'register' => [
            'ID'                =>'payAccount_1',
            'Name'              =>'payAccount',
            'Tags'              =>['primary'],
            'Address'           =>'106.52.210.201',
            'Port'              =>9806,
            'Weight'            =>[
                'passing'=>6,
                'warning'=>1
            ],
            'Check'             => [
                'tcp'      => '106.52.210.201:9806',
                'interval' => '5s',
                'timeout'  => '2s',
            ]
        ],
        'discovery' => [
            'dc' => 'dc1',
            'passing' => false
        ]
    ],
];