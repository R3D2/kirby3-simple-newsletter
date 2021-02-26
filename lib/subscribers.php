<?php
namespace Scardoso\Newsletter;

use Scardoso\Newsletter\Newsletter;

use Kirby\Cms\Model;
use Kirby\Cms\Pages;
use Kirby\Cms\Page;
use Kirby\Exception\Exception;
use Kirby\Toolkit\Collection;
use Kirby\Toolkit\Str;

class Subscribers extends Collection {

    protected $subscribersPage;

    public function __construct()
    {
        $kirby = kirby();

        $this->subscribersPage = $kirby->page(option('scardoso.newsletter.subscribers'));
        $this->data($this->subscribersPage->children()->toArray());
    }

    public function getPageObject(): Page {
        return $this->subscribersPage;
    }

    public function getSubscriber(string $slug)
    {
        $subscriber = $this->subscribersPage->children()->find($slug);

        return $subscriber;
    }

    public function subscribe(array $subscriberData): Page
    {
        // create virtual subscriber page
        $virtualSubscriber = new Page([
            'slug' => Str::random(16),
            'template' => 'subscriber',
            'model' => 'subscriber',
            'parent' => $this->subscribersPage,
            'content' => $subscriberData,
        ]);

        // check virtual page for validation errors
        $errors = $virtualSubscriber->errors();

        // handle validation errors
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

        // handle error
        if (sizeOf($exists) > 0) {
            throw new Exception([
                'key' => 'scardoso.existingEntry',
                'httpCode' => 400,
            ]);
        }

        // generate security hash
        $virtualSubscriber = $virtualSubscriber->update([
            'hash' => bin2hex(random_bytes(16)),
        ]);

        // authenticate
        $kirby = kirby();
        $kirby->impersonate('kirby');

        // create subscriber
        $subscriber = $virtualSubscriber->save();

        // send a confirmation mail in which the new subscriber has to confirm their subscription
        if (option('scardoso.newsletter.confirm')) {
            $kirby->email([
                'from' => option('scardoso.newsletter.from'),
                'replyTo' => option('scardoso.newsletter.from'),
                'to' => $subscriberData['email'],
                'subject' => 'Welcome!',
                'body'=> 'Please confirm your subscription by clicking this link: ' 
                    . url('newsletter/subscribers/confirm/' 
                    . $subscriber->uid()),
            ]);
        }

        return $subscriber;
    }

    public function confirmSubscription(string $slug): Page
    {
        $subscriber = newsletter()->subscribers()->getSubscriber($slug);

        if (!$subscriber) {
            throw new Exception('No entry.');
        }

        $subscriber->changeStatus('listed');

        return $subscriber;
    }

    public function unsubscribe(Page $subscriber) 
    {
        $kirby = kirby();
        $kirby->impersonate('kirby');
        $subscriber->delete();
    }

    public function getEmails()
    {
        $emails = [];

        // Set the uri of your subscriber page in the config
        foreach ($this->subscribersPage->children()->listed() as $subscriber) {
            $emails[] = $subscriber->email()->toString();
        }

        return $emails;
    }
}