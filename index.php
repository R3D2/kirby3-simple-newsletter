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
                'pattern' => 'newsletter/test/(:any)/(:any)',
                'action'  => function (string $uri_1, string $uri_2) {
                    $page = kirby()->page($uri_1 .'/'. $uri_2);
                    
                    $from = 'info@concerts-classiques-gryon.ch';
                    $to = $page->sender()->toString();
                    $subject = $page->subject()->toString();
                    $message = $page->message()->kirbytext()->toString();

                    $result = Newsletter::send($from, $to, $subject, $message, $bcc=false, $page);
                    return json_encode($result);
                },
                'method' => 'get'
            ],
            [
                'pattern' => 'newsletter/send/(:any)/(:any)',
                'action'  => function (string $uri_1, string $uri_2) {
                    $page = kirby()->page($uri_1 .'/'. $uri_2);

                    $from = option('scardoso.newsletter.from');
                    $subject = $page->subject()->toString();
                    $message = $page->message()->kirbytext()->toString();
                    
                    $result = Newsletter::send($from, $to='', $subject, $message, $bcc=true, $page);
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