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
        if (newsletter()->subscribers()->getPageObject()->children()->filter(function($sibling) use ($page) {
            return $sibling->email()->toString() == $page->email()->toString();
        })->count() > 0) {
            $page->delete(true);
            throw new Exception([
                'key' => 'scardoso.existingEntry',
                'httpCode' => 500
            ]);
        };

        // generate slug
        $slug = Str::random(16);

        // random slugs
        $page = $page->changeSlug($slug);

        // change status
        $subscribe = $page->subscribe()->toBool();
        $page = $page->changeStatus($subscribe ? 'listed' : 'unlisted');

        // indicate panel add
        $page = $page->update([
            'addedBy' => kirby()->user()->name()->or(kirby()->user()->email()),
            'hash' => bin2hex(random_bytes(16)),
        ]);
    }
}