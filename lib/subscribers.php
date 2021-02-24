<?php
namespace Scardoso\Newsletter;

use Kirby\Toolkit\Collection;

class Subscribers extends Collection {

    public function __construct()
    {
        
    }

    public function getEmails() 
    {
        $to = [];

        // Set the uri of your subscriber page in the config
        foreach (kirby()->page(option('scardoso.newsletter.subscribers'))->subscriber()->toStructure() as $e) {
            $to[] = $e->email()->toString();
        }

        return $to;
    }
}