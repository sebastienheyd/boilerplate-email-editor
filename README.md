# Laravel Boilerplate E-mail Editor

![Package](https://img.shields.io/badge/Package-sebastienheyd%2Fboilerplate--email--editor-lightgrey.svg)
![Laravel](https://img.shields.io/badge/Laravel-5.7.x-green.svg)
![MIT License](https://img.shields.io/github/license/sebastienheyd/boilerplate.svg)

This package will add a e-mail management tool to [`sebastienheyd/boilerplate`](https://github.com/sebastienheyd/boilerplate).
It allows you to build e-mails for your application.

## Installation

1. In order to install Laravel Boilerplate E-mail Editor run :

```
composer require sebastienheyd/boilerplate-email-editor
```

2. Run the command below to publish assets, lang files, ...

```
php artisan vendor:publish --provider="Sebastienheyd\BoilerplateEmailEditor\BoilerplateEmailEditorServiceProvider"
```

3. After you set your database parameters in your ```.env``` file run :

```
php artisan migrate
```

## Sending an e-mail

```php
use Sebastienheyd\BoilerplateEmailEditor\Models\Email;
// ...
$data = ['firstname' => 'John', 'lastname' => 'Doe'];
Email::find(1)->send('email@tld.com', $data);
```

If needed, you can force another layout by passing his id in the `$data` array :

```php
$data = ['layout_id' => 2, 'firstname' => 'John', 'lastname' => 'Doe'];
```

## Package update

Laravel Boilerplate E-mail Editor comes with assets such as Javascript, CSS, and images. Since you typically will need to overwrite the assets
every time the package is updated, you may use the ```--force``` flag :

```
php artisan vendor:publish --provider="Sebastienheyd\BoilerplateEmailEditor\BoilerplateEmailEditorServiceProvider"
```

To auto update assets each time package is updated, you can add this command to `post-autoload-dump` into the 
file `composer.json` at the root of your project.
 

```json
{
    "scripts": {
        "post-autoload-dump": [
            "@php artisan vendor:publish --provider=\"Sebastienheyd\\BoilerplateEmailEditor\\BoilerplateEmailEditorServiceProvider\" --tag=public --force -q"
        ]
    }
}
```