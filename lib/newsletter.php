<?php
namespace Scardoso\Newsletter;

use Scardoso\Newsletter\Subscribers;
use Kirby\Exception\Exception;

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
            'message' => $log,
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