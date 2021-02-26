# Kirby 3 Simple Newsletter 💌

a Toolkit for creating and sending minimal, GDPR-compliant newsletters via the Kirby panel. 

## Features
- send minimal HTML newsletters using markdown and KirbyText
- basic subscription management
- subscribe form generator (alpha; todo)
- schedule newsletters (planned)
- send mails via API (early stage)
- basic routes for subscribe / unsubscribe

## Install

1. `git clone` or unzip into your plugins directory. 
2. `composer install`.

### Plugin Dependencies
- Custom add fields plugin (https://github.com/steirico/kirby-plugin-custom-add-fields)

This is a plugin for Kirby CMS (https://getkirby.com/). 
Kirby is *not free software*.

## Setup

### Content
Requires a `Subscribers` and a `Newsletters` page with the respective slug / template. (I think I have implemented the option to change the template names but you can never be too sure…)

### Options

Transport configuration needs to be set in the main config file (for now) => https://getkirby.com/docs/guide/emails#transport-configuration. 

Other options:

```php
// set "From" Email address. Required
'scardoso.newsletter.from' => 'bruno@email.com',
// set slug of subscribers page. Default: 'subscribers'
'scardoso.newsletter.subscribers' => 'subscribers',

```

### Routes

there's a route each to subscribe / confirm / unsubscribe a user, find their definitions in the `index.php` file.

There's an example snippet for how to implement a subscription form located at `snippets/subscribe_form.php`. Integration with the uniform plugin is planned.

## Planned
- [ ] more logical class names (“List”, “Campaign”, “Mail/Mails”,…)
- [ ] Uniform plugin integration for dynamic subscription forms
- [ ] release via composer
- [ ] import subscribers (as json)
- [ ] multiple subscriber lists/Sections – choose which list to send newsletter to. 
- [ ] use different possible Collections as subscriber list (subscribers/recipients should extend collection; possibility to send letter to all panel users)
- [ ] simple mailgun integration
- [ ] schedule sending of newsletters

## TODO
- [ ] disable changing newsletter templates and statuses via panel, as this is handled programatically (panel view extended plugin?)
- [ ] allow setting custom blueprints/templates/locations/names for “newsletters” and “subscribers” pages in options
- [ ] prevent clicking the “send” button twice
- [ ] implement styling of the newsletter (option to define a stylesheet location?)
- [ ] apply required fields in panel “add” dialog (custom dialog component instead of plugin?)
- [ ] use uniform form to generate subscription form
- [ ] generate plaintext version of newsletter
- [ ] use plugin specific mail configuration, not the global one
- [ ] more panel translations
- [ ] improve handling of exceptions / success messages / redirects
- [ ] set status of subscriber to “draft” if error detected?

## ideas
- [ ] a new name for the plugin to avoid confusion (is “Newsletter” the name of the plugin or the class name of “a single newsletter”?
- [ ] integrate pagetable plugin for subscriber lists
- [ ] merge newsletter field into a “newsletter-tools” section so that it can be imported on any page
- [ ] export subscribers as json
- [ ] allow different sources as subscriber pages (i.e all registered users, or an external tool)
- [ ] allow for setting different templates (plaintext, html)
- [ ] integration with matomo plugin for tracking? (:/ not sure)


## Disclaimer

This plugin is provided "as is" with no guarantee. Use it at your own risk and always test it yourself before using it in a production environment. If you find any issues, please [create a new issue](https://github.com/username/plugin-name/issues/new).
