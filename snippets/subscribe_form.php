<?php

    $subscribeText = (isset($subscribeText)) ? $subscribeText : 'Subscribe';
    $customFields = (isset($customFields)) ? $customFields : false;

    if (!$customFields) {
        $newsletter = newsletter();
        $subscriberPage = $newsletter->subscribers()->getPageObject();
    
        if ($newsletter->subscribers()->count() == 0) {
    
            $virtualSubscriber = new Page([
                'slug' => 'virtual',
                'template' => 'subscriber',
                'model' => 'subscriber',
                'parent' => $subscriberPage,
            ]);

            // this is not working:
            // $fields = $virtualSubscriber->getRequiredFields();
            // so we replicate the outcome of that method:
            $fields = $virtualSubscriber->blueprint()->fields();
            $fields = array_filter($fields, function ($var) {
                if (array_key_exists('required', $var)) {
                    return ($var['required'] === true);
                }
            });
    
        } else {
            $fields = $subscriberPage->children()->first()->blueprint()->fields();
            $fields = $subscriberPage->children()->first()->getRequiredFields();
        }

    } else {
        $fields = $customFields;
    }

?>

<form action="newsletter/subscribers/add" method="POST">
    <?php foreach ($fields as $field): ?>
    <input type="<?= $field['type']; ?>" name="<?= $field['name']; ?>" required>
    <?php endforeach; ?>
    <button type="submit"><?= $subscribeText; ?></button>
</form>