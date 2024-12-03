<?php

return [
    // ================= admin menu =================
    'main' => [
        'M1' => [
            'name' => '회원 관리',
            'route' => null,
            'param' => [],
            'url' => 'javascript:void(0);',
            'dev' => false,
            'pass' => false,
        ],
        'M2' => [
            'name' => '교육 관리',
            'route' => null,
            'param' => [],
            'url' => 'javascript:void(0);',
            'dev' => false,
            'pass' => false,
        ],
        'M3' => [
            'name' => '강의 관리',
            'route' => null,
            'param' => [],
            'url' => 'javascript:void(0);',
            'dev' => false,
            'pass' => false,
        ],
        'M4' => [
            'name' => '학술자료 관리',
            'route' => null,
            'param' => [],
            'url' => 'javascript:void(0);',
            'dev' => false,
            'pass' => false,
        ],

        'mail' => [
            'name' => '메일 발송',
            'route' => null,
            'param' => [],
            'url' => 'javascript:void(0);',
            'dev' => false,
            'pass' => false,
        ],

        'stat' => [
            'name' => '통계 관리',
            'route' => null,
            'param' => [],
            'url' => 'javascript:void(0);',
            'dev' => false,
            'pass' => false,
        ],
    ],

    'sub' => [
        'M1' => [
            'S1' => [
                'name' => '회원 관리',
                'route' => 'member',
                'param' => [],
                'url' => null,
                'dev' => false,
                'pass' => false,
            ]
        ],

        'M2' => [
            'S1' => [
                'name' => '교육 관리',
                'route' => 'education',
                'param' => [],
                'url' => null,
                'dev' => false,
                'pass' => false,
            ]
        ],
        'M3' => [
            'S1' => [
                'name' => '강의 관리',
                'route' => 'lecture',
                'param' => [],
                'url' => null,
                'dev' => false,
                'pass' => false,
            ]
        ],
        'M4' => [
            'S1' => [
                'name' => '자료 관리',
                'route' => 'workshop',
                'param' => [],
                'url' => null,
                'dev' => false,
                'pass' => false,
            ],
            'S2' => [
                'name' => '자료 열람로그',
                'route' => 'workshop.log',
                'param' => [],
                'url' => null,
                'dev' => false,
                'pass' => false,
            ],
        ],

        'mail' => [
            'S1' => [
                'name' => '메일 관리',
                'route' => 'mail',
                'param' => [],
                'url' => null,
                'dev' => false,
                'pass' => false,
            ],

            'S2' => [
                'name' => '주소록 관리',
                'route' => 'mail.address',
                'param' => [],
                'url' => null,
                'dev' => false,
                'pass' => false,
            ],
        ],

        'stat' => [
            'S1' => [
                'name' => '접속 통계',
                'route' => 'stat',
                'param' => [],
                'url' => null,
                'dev' => false,
                'pass' => false,
            ],

            'S2' => [
                'name' => '접속 경로',
                'route' => 'stat.referer',
                'param' => [],
                'url' => null,
                'dev' => false,
                'pass' => false,
            ],
        ],
    ]
];
