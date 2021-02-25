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

    public function getSubscriber(string $slug): Page 
    {
        $subscriber = page($slug);

        if (!$subscriber) {
            throw new Error('No Entry.');
        }

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
                'details' => $exists,
            ]);
        }

        // authenticate
        $kirby = kirby();
        $kirby->impersonate('kirby');

        // create subscriber
        $virtualSubscriber->save();

        // TODO
        // send a confirmation mail in which the new subscriber has to confirm their subscription

        return $virtualSubscriber;
    }

    public function confirmSubscription(string $slug): Page
    {
        $subscriber = getSubscriber($slug);

        return $subscriber;
    }

    public function unsubscribe(string $slug): Page 
    {
        $subscriber = getSubscriber($slug);

        $subscriber->changeStatus('unlisted');

        return $subscriber;
    }

    public function delete(string $slug): Page
    {
        $subscriber = getSubscriber($slug);

        // TODO
        // delete subscriber page

        return $subscriber;
    }

    public function getEmails()
    {
        $emails = [];

        // Set the uri of your subscriber page in the config
        //TODO add only listed; subscribers should have to confirm their subscription
        foreach ($this->subscribersPage->children() as $subscriber) {
            $emails[] = $subscriber->email()->toString();
        }

        return $emails;
    }
}