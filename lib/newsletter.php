<?php
namespace Scardoso\Newsletter;

use Scardoso\Newsletter\Subscribers;
use Kirby\Exception\Exception;
use Kirby\Cms\Page;
use Kirby\Toolkit\Html;

class Newsletter
{
    protected $subscribers;
    
    public function __construct()
    {
        $this->subscribers = new Subscribers();
    }

    public function subscribers(): Subscribers
    {
        return $this->subscribers;
    }

    public function newSend(Page $newsletter, bool $test = true): Page
    {
        // check if "from" option is set
        if (null == option('scardoso.newsletter.from')) {
            throw new Exception('define FROM address in config');
        }

        // set "from"
        $from = option('scardoso.newsletter.from');

        // get log
        $log = $newsletter->log();

        // get recipients
        if ($test) {
            $recipients = $newsletter->to()->trim()->split(',');
        } else {
            $recipients = $this
            ->subscribers()
            ->getPageObject()
            ->children()
            ->listed();
        }

        // get attachments
        $attachments = $this::getFiles($newsletter);

        // get newsletter content
        $message = $newsletter->message()->kirbyText();

        // get newsletter subject
        $subject = $test ? '[Test] ' . $newsletter->subject() : $newsletter->subject();

        // send mails
        foreach ($recipients as $recipient) {
            $html = $test ? $message : $message . Html::link(url('unsubscribe/' . $recipient->uid() . '/' . $recipient->hash()), 'Unsubscribe');
            $to = $test ? $recipient : $recipient->email()->toString();

            try {
                kirby()->email([
                    'from' => $from,
                    'replyTo' => $from,
                    'to' => $to,
                    'subject' => $subject, 
                    'body' => [
                        'html' => $html,
                    ],
                    'attachments' => $attachments
                ]);
            } catch(Exception $e) {
                $log = $log . '\n' . $e->getMessage();
            }
        }

        // trigger Send:After Hook
        if (!$test) {
            kirby()->trigger('newsletter.send:after', ['page' => $newsletter]);
        };

        // write errors to log
        $newsletter->update([
            'log' => $log,
        ]);

        return $newsletter;
    }

    public function send($from, $to, $subject, $message, $page, $test): array
    {
        $result = [];
        $log = '';
        $status = 200;

        // Get all the subscribers or set test recipients
        $to = $test ? $to : $this->subscribers->getEmails();
        $files = Newsletter::getFiles($page);

        // Check if we have at least of subscriber or a test recipient
        if (empty($to)) {
            $errorMessage = $test ? 'No test mail address provided!' : 'There is no subscriber to send our newsletter to!';
            throw new Exception($errorMessage);
        };

        $message = $message;

        // send mails
        try {
            $email = kirby()->email([
                'from' => $from,
                'replyTo' => $from,
                'to' => $to,
                'subject' => $test ? '[Test] ' . $subject : $subject,
                'body' => [
                    'html' => $message,
                ],
                'attachments' => $files
            ]);
            $log = $email->isSent() ? 'Mail has been sent!' : 'Mail could not be sent';
        } catch (Exception $error) {
            throw new Exception($error->getMessage());
            $log = $error->getMessage();
            $status = 400;
        }

        $result = [
            'from' => $from,
            'subject' => $subject,
            'to' => $to,
            'body' => $message,
            'log' => $log,
            'attachments' => $files,
            'status' => $status
        ];

        // trigger Send:After Hook
        if (!$test) {
            kirby()->trigger('newsletter.send:after', compact('page'));
        };

        return $result;
    }

    public static function getFiles($page)
    {
        $files = [];

        foreach ($page->files() as $f) {
            $files[] = $f->root();
        }

        return $files;
    }
}