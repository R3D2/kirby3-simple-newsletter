<?php 
namespace Scardoso\Newsletter\Models;

use Kirby\Cms\Page;
use Kirby\Exception\Exception;
use Kirby\Toolkit\Str;

class SubscriberPage extends Page {

    public function getRequiredFields(): array {
        $fields = $this->blueprint()->fields();
        $fields = array_filter($fields, function ($var) {
            if (array_key_exists('required', $var)) {
                return ($var['required'] === true);
            }
        });
        return $fields;
    }

    // called when adding subscriber via panel
    public static function hookPageCreate($page) {

        // handle existing email address
        if ($page->siblings(false)->filter(function($sibling) use ($page) {
            return $sibling->email()->toString() == $page->email()->toString();
        })->count() > 0) {
            $page->delete();
            throw new Exception([
                'key' => 'scardoso.existingEntry',
                'httpCode' => 500
            ]);
        }

        // update page field content
        $page->changeSlug(Str::random(16));

    }
}