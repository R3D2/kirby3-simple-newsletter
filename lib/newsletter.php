<?php

use Kirby\Email\PHPMailer as Email;
use Kirby\Exception;

class Newsletter {
    
    public static function send($from, $to, $subject, $message, $page, $test) {
        $to = $test ? $to : Newsletter::getSubscribers();
        $files = Newsletter::getFiles($page);

        $result = [];
        $log = '';
        $status = 200;
        
        try {
            foreach($to as $recipient) {
                $email = new Email([
                    'from'      => $from,
                    'replyTo'   => $from,
                    'to'        => $recipient,
                    'subject'   => $subject,
                    'body' => [
                        'html' => $message,
                    ],
                    'attachments' => $files
                ]);
            };
            $log = $email->isSent() ? 'Mail has been sent !' : 'Mais has not been sent';
        } catch (Exception $error) {
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

        return $result;
    }

    public static function getSubscribers() {
        $to = [];

        foreach(kirby()->page('abonnes')->subscriber()->toStructure() as $e){
            $to[] = $e->email()->toString();
        }

        return $to;
    }

    public static function getFiles($page) {
        $files = [];

        foreach($page->files() as $f){
            $files[] = $f->root();
        }

        return $files;
    }
}