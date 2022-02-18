<?php

use Scardoso\Newsletter\Newsletter;

use Kirby\Toolkit\I18n;
use Kirby\Cms\Blueprint;
use Kirby\Cms\Page;
use Kirby\Cms\Pages;

load([
    'scardoso\\newsletter\\newsletter' => 'lib/Newsletter.php',
    'scardoso\\newsletter\\subscribers' => 'lib/Subscribers.php',
    'scardoso\\newsletter\\Models\\SubscriberPage' => 'models/SubscriberPage.php',
], __DIR__);

function newsletter(): Newsletter
{
    return new Newsletter();
}

Kirby::plugin('scardoso/newsletter', [
    'options' => [
        'from' => 'tospecify@intheconfig.php',
        'subscribers' => 'subscribers',
        'confirm' => true,
        'confirmationPage' => 'success',
    ],
    'blueprints' => [
        'pages/newsletters' => __DIR__ . '/blueprints/pages/newsletters.yml',
        'pages/newsletter' => __DIR__ . '/blueprints/pages/newsletter.yml',
        'pages/newsletter-sent' => __DIR__ . '/blueprints/pages/newsletter-sent.yml',
        'pages/subscribers' => __DIR__ . '/blueprints/pages/subscribers.yml',
        'pages/subscriber' => __DIR__ . '/blueprints/pages/subscriber.yml',

        'sections/newsletters' => __DIR__ . '/blueprints/sections/newsletters.yml',
        'layouts/newsletter' => __DIR__ . '/blueprints/layouts/newsletter.yml'
    ],
    'snippets' => [
        'subscribe_form' => __DIR__ . '/snippets/subscribe_form.php'
    ],
    'templates' => [
        'newsletter' => __DIR__ . '/templates/newsletter.php',
        'newsletter-sent' => __DIR__ . '/templates/newsletter-sent.php'
    ],
    'pageModels' => [
        'subscriber' => 'Scardoso\\Newsletter\\Models\\SubscriberPage'
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
                'slug' => function() {
                    return $this->model()->slug();
                },
                'id' => function () {
                    return $this->model()->id();
                },
                'subscriberLink' => function () {
                    return kirby()->option('scardoso.newsletter.subscribers');
                }
            ]
        ],
        'newsletterbody' => [
            
        ],
    ],
    'hooks' => [
        'newsletter.send:after' => function ($page) {
            kirby()->impersonate('kirby');
            $page = $page->changeStatus('listed');
            $page = $page->changeTemplate('newsletter-sent');
        },
        'page.duplicate:after' => function ($duplicatePage, $originalPage) {
            if ($duplicatePage->intendedTemplate() == 'newsletter-sent') {
                $duplicatePage->changeTemplate('newsletter');
            }
        }
    ],
    'routes' => [
        [
            'pattern' => '/newsletter/subscribers/add',
            'method' => 'POST',
            'action'  => function () {

                $data = $_POST;
                // $redirect = isset($_POST['redirect']) ? $_POST['redirect'] : 'home';
                $newsletter = newsletter();

                $kirby = kirby();
                $kirby->impersonate('kirby');

                $return = $data;

                try {
                    $newsletter->subscribers()->subscribe($data);
                } 
                catch(Exception $e) {
                    return Response::json($e->getMessage());
                }

                return Response::json('success');
            },
        ],
        [
            'pattern' => '/newsletter/subscribers/confirm/(:any)',
            'method' => 'GET',
            'action'  => function ($uid) {

                // $redirect = option('scardoso.newsletter.confirmationPage');
                $newsletter = newsletter();
                $kirby = kirby();
                $kirby->impersonate('kirby');

                try {
                    $newsletter->subscribers()->confirmSubscription($uid);
                }
                catch(Exception $e) {
                    return Response::json($e->getMessage());
                }

                return Response::json('success');
            },
        ],
        [
            'pattern' => 'unsubscribe/(:any)/(:any)',
            'action' => function($slug, $hash) {
                $subscriber = newsletter()->subscribers()->getSubscriber($slug);

                if (!$subscriber) {
                    return 'Not a valid user';
                }

                if ($hash != $subscriber->hash()) {
                    throw new Error("Hashes don’t match.");
                }

                $subscribers = newsletter()->subscribers();
                $kirby = kirby();
                $kirby->impersonate('kirby');

                $subscribers->unsubscribe($subscriber);

                return 'you have been unsubscribed.';

            }
        ],
    ],
    'api' => [
        'routes' => [
            [
                'pattern' => 'newsletter/newSend/(:any)/(:num)',
                'method' => 'get',
                'action' => function(string $slug, int $test) {
                    // TODO why is this not working ajhslalsjd
                    $nl = newsletter();
                    $page = kirby()->page('newsletters/' . $slug);
                    if (!$page) {
                        throw new Error('page not found');
                    }
                    $result = $nl->newSend(page('newsletters/' . $slug), boolval($test));
                    return json_encode($result);
                }
            ],
            [
                'pattern' => 'newsletter/send/(:any)/(:any)/(:num)',
                'method' => 'get',
                'action'  => function (string $uri_1, string $uri_2, int $test) {
                    $test = $test === 0;
                    $from = kirby()->option('scardoso.newsletter.from');
                    $nl = newsletter();

                    if ($from == '') {
                        throw new Error([
                            'message' => "Please set 'from' property in your config.php",
                            'status' => 400
                        ]);
                    }

                    $newsletter = kirby()->page($uri_1 .'/'. $uri_2);

                    return $nl->newSend($newsletter, $test);

                    // $to = ($test) ? $page->to()->trim()->split(',') : 'multi';
                    // if ($to != '') {
                    //     $subject = $page->subject()->toString();
                    //     $message = $page->message()->kirbytext()->toString();
                    //     $result = $nl->send($from, $to, $subject, $message, $page, $test);
                    // } else {
                    //     $result = [
                    //         'message' => t('scardoso.newsletter.noTestMail'),
                    //         'status' => 400
                    //     ];
                    // }

                    // return json_encode($result);
                },
            ],
        ]   
    ],
    'translations' => [
        'en' => [
            'scardoso.newsletter.t.testRecipients' => 'Test mail recipients',
            'scardoso.newsletter.t.testRecipientsHelpText' => 'It is possible to add multiple test mail recipients by separating email addresses with a comma.',
            'scardoso.newsletter.t.confirmSendNewsletter' => 'Are you sure you want to send the newsletter?',
            'scardoso.newsletter.t.confirmSendTestNewsletter' => 'Are you sure you want to send the test newsletter?',
            'scardoso.newsletter.sendNewsletter' => 'Send now',
            'scardoso.newsletter.viewSubscribers' => 'View subscribers',
            'scardoso.newsletter.sendTestMail' => 'Send a test mail',
            'scardoso.newsletter.scheduleMail' => 'Schedule send',
            'scardoso.newsletter.noTestMail' => 'Please enter a valid email address for sending the test newsletter',

            'error.scardoso.fieldsvalidation' => 'Invalid field content.',
            'error.scardoso.existingEntry' => 'Email address already registered.'
        ],
        'de' => [
            'scardoso.newsletter.t.testRecipients' => 'Test-Email Empfänger',
            'scardoso.newsletter.t.testRecipientsHelpText' => 'Mehrere Adressen könnnen mit einem Komma getrennt werden.',
            'scardoso.newsletter.t.confirmSendNewsletter' => 'Sind Sie sicher, dass Sie den Newsletter versenden möchten?',
            'scardoso.newsletter.t.confirmSendTestNewsletter' => 'Sind Sie sicher, dass Sie den Test-Newsletter versenden möchten?',
            'scardoso.newsletter.sendNewsletter' => 'Newsletter versenden',
            'scardoso.newsletter.viewSubscribers' => 'Abonnenten',
            'scardoso.newsletter.sendTestMail' => 'Test-Email senden',
            'scardoso.newsletter.noTestMail' => 'Bitte eine Email-Adresse für den Test-Newsletter angeben',
        ],
        'fr' => [
            'scardoso.newsletter.t.testRecipients' => 'Adresses de Réception de la newsletter de test',
            'scardoso.newsletter.t.testRecipientsHelpText' => '',
            'scardoso.newsletter.t.confirmSendNewsletter' => 'Êtes vous sûr de vouloir envoyer la Newsletter ?',
            'scardoso.newsletter.t.confirmSendTestNewsletter' => 'Êtes vous sûr de vouloir envoyer la Newsletter de test ?',
            'scardoso.newsletter.sendNewsletter' => 'Envoyer la Newsletter',
            'scardoso.newsletter.viewSubscribers' => 'Voir la liste des abonnés',
            'scardoso.newsletter.sendTestMail' => 'Envoyer un test',
            'scardoso.newsletter.noTestMail' => "Veuillez rentrer une adresse de récéption pour l'envoi du test"
        ]
    ]
]);