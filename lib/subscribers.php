<?php
namespace Scardoso\Newsletter;

use Scardoso\Newsletter\Newsletter;

use Kirby\Cms\Model;
use Kirby\Cms\Pages;
use Kirby\Cms\Page;
use Kirby\Exception\Exception;
use Kirby\Toolkit\Str;

class Subscribers extends Pages {

    protected $subscribersPage;

    public function __construct()
    {
        $this->subscribersPage = kirby()->page(option('scardoso.newsletter.subscribers'));
    }

    public function subscribe(array $subscriberData) {
        // create virtual subscriber page
        $virtualSubscriber = new Page([
            'slug' => Str::random(16),
            'template' => 'subscriber',
            'model' => 'subscriber',
            'parent' => $this->subscribersPage,
            'content' => $subscriberData,
        ]);

        // check virtual page for errors
        $errors = $virtualSubscriber->errors();

        // handle errors
        if (sizeOf($errors) > 0) {
            throw new Exception([
                'key' => 'scardoso.fieldsvalidation',
                'httpCode' => 400,
                'details' => $errors,
            ]);
        }

        // check if mail address is already stored
        $exists = $this->subscribersPage->children()->filter(function($subscriber) use ($virtualSubscriber) {
            return $subscriber->email()->toString() == $virtualSubscriber->email()->toString();
        });

        if (sizeOf($exists) > 0) {
            throw new Exception([
                'key' => 'scardoso.existingEntry',
                'httpCode' => 400,
                'details' => [
                    'message' => 'Email address already registered',
                ]
            ]);
        }

        // authenticate
        $kirby = kirby();
        $kirby->impersonate('kirby');

        // create subscriber
        $virtualSubscriber->save();
    }

    public function unsubscribe(array $subscriber): self {

    }

    public function getEmails() 
    {
        $emails = [];

        // Set the uri of your subscriber page in the config
        foreach (kirby()->page(option('scardoso.newsletter.subscribers'))->subscriber()->toStructure() as $e) {
            $to[] = $e->email()->toString();
        }

        return $emails;
    }
}