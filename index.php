<?php

use Kirby\Toolkit\I18n;
use Kirby\Cms\Blueprint;
use Kirby\Cms\Page;
use Kirby\Cms\Pages;

@include_once __DIR__ . '/lib/newsletter.php';

Kirby::plugin('scardoso/newsletter', [
    'options' => [
        'from' => 'tospecify@intheconfig.php',
        'subscribers' => 'subscribers'
    ],
    'blueprints' => [
        'pages/newsletter' => __DIR__ . '/blueprints/pages/newsletter.yml',
        'pages/newsletters' => __DIR__ . '/blueprints/pages/newsletters.yml',
        'pages/subscribers' => __DIR__ . '/blueprints/pages/subscribers.yml',
        'sections/newsletters' => __DIR__ . '/blueprints/sections/newsletters.yml'
    ],
    'snippets' => [
        'newsletter_form' => __DIR__ . '/snippets/newsletter_form.php'
    ],
    'fields' => [
        'newsletter' => [
            'props' => [
                'data' => function (string $data = null) {
                    return I18n::translate($data, $data);
                },
                'pageURI' => function () {
                    return $this->model()->uri();
                },
                'id' => function () {
                    return $this->model()->id();
                },
                'subscriberLink' => function () {
                    return kirby()->option('scardoso.newsletter.subscribers');
                }
            ]
        ],
    ],
    'api' => [
        'routes' => [
            [
                'pattern' => 'newsletter/send/(:any)/(:any)/(:num)',
                'action'  => function (string $uri_1, string $uri_2, int $test) {
                    $test = $test === 0;
                    $from = kirby()->option('scardoso.newsletter.from');

                    if ($from !== '') {
                        $page = kirby()->page($uri_1 .'/'. $uri_2);
                        $to = ($test) ? $page->to()->trim()->split(',') : 'multi';
                        if ($to != '') {
                            $subject = $page->subject()->toString();
                            $message = $page->message()->kirbytext()->toString();
                            $result = Newsletter::send($from, $to, $subject, $message, $page, $test);
                        } else {
                            $result = [
                                'message' => t('scardoso.newsletter.noTestMail'),
                                'status' => 400
                            ];
                        }
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
    ],
    'translations' => [
        'en' => [
            'scardoso.newsletter.t.testRecipients' => 'Test mail recipients',
            'scardoso.newsletter.t.testRecipientsHelpText' => 'It is possible to add multiple test mail recipients by separating email addresses with a comma.',
            'scardoso.newsletter.sendNewsletter' => 'Send newsletter',
            'scardoso.newsletter.viewSubscribers' => 'View subscribers',
            'scardoso.newsletter.sendTestMail' => 'Send a test mail',
            'scardoso.newsletter.noTestMail' => 'Please enter a valid email address for sending the test newsletter',
        ],
        'de' => [
            'scardoso.newsletter.t.testRecipients' => 'Test-Email Empfänger',
            'scardoso.newsletter.t.testRecipientsHelpText' => 'Mehrere Adressen könnnen mit einem Komma getrennt werden.',
            'scardoso.newsletter.sendNewsletter' => 'Newsletter versenden',
            'scardoso.newsletter.viewSubscribers' => 'Abonnenten',
            'scardoso.newsletter.sendTestMail' => 'Test-Email senden',
            'scardoso.newsletter.noTestMail' => 'Bitte eine Email-Adresse für den Test-Newsletter angeben',
        ],
        'fr' => [
            'scardoso.newsletter.t.testRecipients' => 'Adresses de Réception de la newsletter de test',
            'scardoso.newsletter.sendNewsletter' => 'Envoyer la Newsletter',
            'scardoso.newsletter.viewSubscribers' => 'Voir la liste des abonnés',
            'scardoso.newsletter.sendTestMail' => 'Envoyer un test',
            'scardoso.newsletter.noTestMail' => "Veuillez rentrer une adresse de récéption pour l'envoi du test"
        ]
    ]
]);