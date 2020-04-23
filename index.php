<?php

use Kirby\Cms\Blueprint;
use Kirby\Cms\Page;
use Kirby\Cms\Pages;

@include_once __DIR__ . '/lib/newsletter.php';

Kirby::plugin('scardoso/newsletter', [
    'options' => [
        'from' => 'tospecify@intheconfig.php'
    ],
    'blueprints' => [
        'pages/newsletter' => __DIR__ . '/blueprints/pages/newsletter.yml',
        'pages/newsletters' => __DIR__ . '/blueprints/pages/newsletters.yml',
        'pages/subscribers' => __DIR__ . '/blueprints/pages/subscribers.yml',
        'sections/newsletters' => __DIR__ . '/blueprints/sections/newsletter.yml'
    ],
    'snippets' => [
        'newsletter_form' => __DIR__ . '/snippets/newsletter_form.php'
    ],
    'fields' => [
        'testbtn' => [
            'props' => [
                'data' => function (string $data = null) {
                    return \Kirby\Toolkit\I18n::translate($data, $data);
                },
                'pageURI' => function () {
                    return $this->model()->uri();
                },
                'id' => function () {
                    return $this->model()->id();
                },
                'subscriberLink' => function () {
                    return kirby()->option('subscriper.page.uri');
                }
            ]
        ],
    ],
    'api' => [
        'routes' => [
            [
                'pattern' => 'newsletter/send/(:any)/(:any)/(:num)',
                'action'  => function (string $uri_1, string $uri_2, int $test) {
                    $test = $test === 1;
                    $from = kirby()->option('scardoso.newsletter.from');

                    if ($from !== '') {
                        $page = kirby()->page($uri_1 .'/'. $uri_2);
                        $subject = $page->subject()->toString();
                        $message = $page->message()->kirbytext()->toString();
                        $result = Newsletter::send($from, $to='', $subject, $message, $page, $test);
                    } else {
                        $result = [
                            'message' => "Please set 'from' property in your config.php",
                            'status' => 400
                        ];
                    }

                    return json_encode($result);
                },
                'method' => 'get'
            ],
            [
                'pattern' => 'newsletter/subscriber/add',
                'action'  => function () {
                    $kirby = kirby();
                    $kirby->impersonate('kirby');

                    $kirby->page('subscriber')->createChild([
                        'content'  => [
                            'subscriber' => [
                                'email' => $_POST['email']
                            ]
                        ]
                    ]);
                },
                'method' => 'post'
            ],
        ]   
    ]
]);