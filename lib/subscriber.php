<?php

class Subscriber {

    public static function all() {
        $to = [];
        kirby()->option('scardoso.newsletter.subscriber.uri');

        foreach(kirby()->page(subscriberPageUri)->subscriber()->toStructure() as $e){
            $to[] = $e->email()->toString();
        }

        return $to;
    }
}