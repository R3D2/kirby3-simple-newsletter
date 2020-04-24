# Kirby 3 Simple Newsletter

![Version](https://img.shields.io/badge/version-0.1-green.svg) ![License](https://img.shields.io/badge/license-MIT-green.svg) ![Kirby Version](https://img.shields.io/badge/Kirby-3.0%2B-red.svg)

## Setup

### Blueprints
There is two custom blueprints that needs to be added to your panel so you can create the pages 
to send a newsletter and to get visitors to subscribe at your newsletter.

You can find them in the blueprints folder...

## Options

The following options need to be set in your `/site/config/config.php` file:

```
'email' => [
        'transport' => [
          'type' => 'smtp',
          'host' => 'mail.example.com',
          'port' => 587,
          'security' => true,
          'auth' => true,
          'username' => 'email@example.com',
          'password' => 'mailpassword',
        ]
    ],
    'scardoso.newsletter.from' => 'myfrom@email.com',
```

### Subscribers
To allow visitor to subscribe to your newsletter you need to send a POST request to this route :

- First create a page with the subscriber blueprint
- Create a form who sends the email through a POST request
- Then use the uri you have set for the blueprint you have created to the page function.

```php
<?php
if( !empty($_POST) && isset($_POST['email']) ) {
    // Get the current list of subscribers
    // parameter of the page function must be the uri of your subscriber page
    $subs = kirby()->page('abonnes')->subscriber()->toStructure()->toArray();
    
    // Add the new one
    $subs[] = [
        'email' => $_POST['email']
    ];
    
    // Add it to our struct
    $kirby = kirby();
    $kirby->impersonate('kirby');

    $kirby->page('abonnes')->save([
        'subscriber' => $subs
    ]);
}
?>
```

## TODO
- Add url in the footer of the newsletter to unsubscribe
- Add the possibility to customize routes in the config.php
- Modify the feature to add subscribers in the plugin for easier integration
- Enhance the way the plugin deals with error in the panel
- Add Translations

## Disclaimer

This plugin is provided "as is" with no guarantee. Use it at your own risk and always test it yourself before using it in a production environment. If you find any issues, please [create a new issue](https://github.com/username/plugin-name/issues/new).

## License

[MIT](https://opensource.org/licenses/MIT)

It is discouraged to use this plugin in any project that promotes racism, sexism, homophobia, animal abuse, violence or any other form of hate speech.