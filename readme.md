# Laravel Boilerplate E-mail Editor

[![Packagist](https://img.shields.io/packagist/v/sebastienheyd/boilerplate-email-editor.svg?style=flat-square)](https://packagist.org/packages/sebastienheyd/boilerplate-email-editor)
[![Build Status](https://scrutinizer-ci.com/g/sebastienheyd/boilerplate-email-editor/badges/build.png?b=master)](https://scrutinizer-ci.com/g/sebastienheyd/boilerplate-email-editor/build-status/master)
[![StyleCI](https://github.styleci.io/repos/170875496/shield?branch=master)](https://github.styleci.io/repos/170875496)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/sebastienheyd/boilerplate-email-editor/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/sebastienheyd/boilerplate-email-editor/?branch=master)
![Laravel](https://img.shields.io/badge/Laravel-6.x%20→%208.x-green?logo=Laravel&style=flat-square)
[![Nb downloads](https://img.shields.io/packagist/dt/sebastienheyd/boilerplate-email-editor.svg?style=flat-square)](https://packagist.org/packages/sebastienheyd/boilerplate-email-editor)
[![MIT License](https://img.shields.io/github/license/sebastienheyd/boilerplate.svg)](license.md)

This package for [`sebastienheyd/boilerplate`](https://github.com/sebastienheyd/boilerplate) allows developers to manage 
emails for their applications. It allows you to create layouts and then define editors who will only be able to edit 
texts without being able to modify the layouts.

## Installation

1. In order to install Laravel Boilerplate Email Editor run :

```
composer require sebastienheyd/boilerplate-email-editor
```

2. Then run :

```
php artisan migrate
```

You can go to the admin and start using the email management panel.

## Generating an email layout

Before generating a layout, be aware that there is a default html layout provided with this package.

To generate a new layout, you can use the following artisan command :

```
php artisan email:layout {name} 
```

This command will generate a new blade file in the `resources/views/email-layouts` folder.

To change the default folder, change the value of `layouts_path` in the `email-editor` configuration file.

However, you must publish the configuration file in order to do so. To do this, use the following command:

```
php artisan vendor:publish --provider="Sebastienheyd\BoilerplateEmailEditor\ServiceProvider"
```

You can also remove a layout by using the `--remove` option

```
php artisan email:layout --remove {name}
```

## Defining editors

This package is provided with two permissions that can be used depending on the desired profile.

* Email development : to be reserved for developers, it allows to define the slug, the description and the layout.
* Email edition : for users who will be able to edit the content of emails.

Permissions and roles are manageable by default with `sebastienheyd/boilerplate`

## Email variables

In the editing of the content of an e-mail, you will find a "Insert a variable" button. This button allows you to insert 
a variable in the e-mail and make it uneditable.

However, you can also enter the variables by hand by framing them with [ and ]. In this manner, you can also add 
variables to the subject line of the email.

Example : "Hello [first_name]"

## Sending an email

```php
use Sebastienheyd\BoilerplateEmailEditor\Models\Email;

// Setting data
$data = ['first_name' => 'John', 'last_name' => 'Doe'];

// Sending email by his slug
Email::findBySlug('my_slug')->send('email@tld.com', $data);

// Or by his id
Email::find(1)->send('email@tld.com', $data);
```

## Package update

Version 7 has undergone a major upgrade, do not upgrade to this version without knowing what you are doing.
