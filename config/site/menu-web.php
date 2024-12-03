<?php

return [
    // ================= web menu =================
    'main' => [
        'M1' => [
            'name' => '온라인 강의',
            'route' => 'education',
            'param' => [],
            'url' => null,
            'dev' => false,
            'continue' => false,
        ],
        'M2' => [
            'name' => '학술자료실',
            'route' => 'workshop',
            'param' => [],
            'url' => null,
            'dev' => false,
            'continue' => false,
        ],
        'M3' => [
            'name' => '마이페이지',
            'route' => 'mypage.education',
            'param' => [],
            'url' => null,
            'dev' => false,
            'continue' => false,
        ],
        'M4' => [
            'name' => '지원센터',
            'route' => 'board',
            'param' => ['code'=>'notice'],
            'url' => null,
            'dev' => false,
            'continue' => false,
        ],
    ],

    'sub' => [
        'M3' => [
            'S1' => [
                'name' => '온라인 강의실',
                'route' => 'mypage.education',
                'param' => [],
                'url' => null,
                'dev' => false,
                'continue' => false,
            ],
            'S2' => [
                'name' => '나의 자료실',
                'route' => 'mypage.interest_workshop',
                'param' => [],
                'url' => null,
                'dev' => false,
                'continue' => false,
            ],
            'S3' => [
                'name' => '개인정보',
                'route' => 'mypage.myInfo',
                'param' => [],
                'url' => null,
                'dev' => false,
                'continue' => false,
            ],
        ],
        'M4' => [
            'S1' => [
                'name' => '공지사항',
                'route' => 'board',
                'param' => ['code'=>'notice'],
                'url' => null,
                'dev' => false,
                'continue' => false,
            ],
            'S2' => [
                'name' => '이용가이드',
                'route' => 'board',
                'param' => ['code'=>'guide'],
                'url' => null,
                'dev' => false,
                'continue' => false,
            ],
            'S3' => [
                'name' => 'FAQ',
                'route' => 'board',
                'param' => ['code'=>'faq'],
                'url' => null,
                'dev' => false,
                'continue' => false,
            ],
        ],

    ],

    'low' => [
        'M3' => [
            'S1' => [
                'SS1' => [
                    'name' => '교육수강',
                    'route' => 'mypage.education',
                    'param' => [],
                    'url' => null,
                    'dev' => false,
                    'continue' => false,
                ],
                'SS2' => [
                    'name' => '교육신청/취소',
                    'route' => 'mypage.list',
                    'param' => [],
                    'url' => null,
                    'dev' => false,
                    'continue' => false,
                ],
                'SS3' => [
                    'name' => '이수증 출력',
                    'route' => 'mypage.certi',
                    'param' => [],
                    'url' => null,
                    'dev' => false,
                    'continue' => false,
                ],
                'SS4' => [
                    'name' => '관심교육',
                    'route' => 'mypage.interest_edu',
                    'param' => [],
                    'url' => null,
                    'dev' => false,
                    'continue' => false,
                ],
            ],
            'S2' => [
                'SS1' => [
                    'name' => '관심자료',
                    'route' => 'mypage.interest_workshop',
                    'param' => [],
                    'url' => null,
                    'dev' => false,
                    'continue' => false,
                ],
                'SS2' => [
                    'name' => '자료열람기록',
                    'route' => 'mypage.workshop_log',
                    'param' => [],
                    'url' => null,
                    'dev' => false,
                    'continue' => false,
                ],
            ],
        ],

    ],
];
