[![Latest Version on Packagist](https://img.shields.io/packagist/v/rubik-llc/laravel-invite.svg)](https://packagist.org/packages/rubik-llc/laravel-invite)
[![Check & fix styling](https://img.shields.io/github/workflow/status/rubik-llc/laravel-invte/php-cs-fixer.yml?label=check%20and%20fix%20styling)](https://github.com/rubik-llc/laravel-invite/actions/workflows/php-cs-fixer.yml)
![Platform](https://img.shields.io/badge/platform-laravel-red)
[![GitHub Workflow Status](https://img.shields.io/github/workflow/status/rubik-llc/laravel-invite/run-tests.yml?label=tests)](https://github.com/rubik-llc/laravel-invite/actions/workflows/run-tests.yml)
[![GitHub](https://img.shields.io/github/license/rubik-llc/laravel-invite)](LICENSE.md)

[//]: # (![GitHub all releases]&#40;https://img.shields.io/github/downloads/rubik-llc/laravel-invite/total&#41;)


A simple invitation system for Eloquent models in your Laravel application. The package doesn't cover sending emails,
views or routing.

```php
// Make an invitation
Invitation::to('test@example.com')->make();
```

```php
$user = User::find(1);

// Make an invitation directly from an Eloquent Model
$user->invitation()->to('test@example.com')->make();
```

```php
$referer = User::find(1);

$invitee = User::find(2);

// Set properties of an invitation
Invitation::to('test@example.com')
            ->referer($referer)
            ->invitee($invitee)
            ->expireIn(3, 'days')
            ->make();
```

```php
// Accept an invitation
Invitation::findByToken('1234')->accept();
```

## Installation

You can install the package via composer:

```bash
composer require rubik-llc/laravel-invite
```

You can publish and run the migrations with:

```bash
php artisan vendor:publish --tag="invite-migrations"
php artisan migrate
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="invite-config"
```

This is the contents of the published config file:

```php
return [

    /*
    |--------------------------------------------------------------------------
    | Invitation class
    |--------------------------------------------------------------------------
    |
    | The invite class that should be used to store and retrieve the invitations.
    | If you specify a different model class, make sure that model extends the default
    | Invitation model that is shipped with this package.
    |
    */

    'invitation_model' => \Rubik\LaravelInvite\Models\Invitation::class,

    /*
    |--------------------------------------------------------------------------
    | Delete on decline
    |--------------------------------------------------------------------------
    |
    | When this option is enabled, whenever an invitation is declined it will automatically
    | be deleted.
    |
    */

    'delete_on_decline' => false,

    /*
    |--------------------------------------------------------------------------
    | Unit
    |--------------------------------------------------------------------------
    |
    | The unit of the values.
    | This package uses Carbon for date and time related calculations, therefore
    | the value of this option should be only values that Carbon accepts.
    | e.g: seconds, minutes, hours, days, weeks, months, years, etc.
    |
    */

    'unit' => 'hours',

    /*
    |--------------------------------------------------------------------------
    | Expire
    |--------------------------------------------------------------------------
    | The default value of when to expire an invitation after its created. It uses
    | the units that are specified above.
    |
    | If the delete.auto value is set to true, it enables a scheduler that executes
    | a command every hour which deletes all invitations that have surpassed the amount
    | of time given in delete.after
    |
    */
    'expire' => [

        'after' => 48,

        'delete' => [
            'auto' => false,
            'after' => 48,
        ],

    ],

];
```

## Usage

### Registering the Referable Model

In order to let a model be able to make invitations, simply add the `CanInvite` trait to the class of that model.

``` php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Rubik\LaravelInvite\Traits\CanInvite;

class User extends Model
{
    use CanInvite;
    
    ...
}
```

### Registering the Invitable Model

In order to let a model be able to receive invitations, add the `RecivesInvitation` trait to the class of that model.

``` php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Rubik\LaravelInvite\Traits\RecivesInvitation;

class User extends Model
{
    use RecivesInvitation;
    
    ...
}
```

### Making invitations

There are two ways to make an invitation:

#### 1. Making invitations using the Facade

```php
use Rubik\LaravelInvite\Facades\Invitation;

Invitation::to('test@example.com')->make();
```

#### 2. Making invitations from a referer Model

Making an invitation from a model will automatically set in as the referer. Make sure the model class uses
the `CanInvite` trait.

```php
$user = User::find(1);

$invitation = $user->invitation()->to('test@example.com')->make();

$invitation->referable // will return the user that made the invitation.
```

**NOTE**: An email is required in order to make an invitation.

Additionally, you can specify other parameters like:

### Referer

You can associate a referer to an invitation using the `referer` method which accepts an eloquent model as a parameter.

```php
$referer = User::find(1);

Invitation::to('test@example.com')->referer($referer)->make();
```

or update the referer of an existing invitation.

```php
$invitation = Invitation::find(1);

$invitation->referer($anotherReferer);
```

### Invitee

You can associate an invitee to an invitation using the `invitee` method. This method accepts an eloquent model or a
model class name.

- #### Using Eloquent Model

This option should be used when the invitee is created before the invitation is sent.

```php
$invitee = User::find(1);

// Using the Facade
$invitation = Invitation::to('test@example.com')->invitee($invitee)->make();

// Using the Referer Model
$invitation = $user->invitation()->to('test@example.com')->invitee($invitee)->make();

$invitation->invitable // will return an instance of the User model
```

or update the invitee of an existing invitation.

```php
$invitation = Invitation::find(1);

$invitation->invitee($anotherInvitee);
```

- #### Using class name

This option should be used when the invitee is created after the invitation is accepted, and you want to specify its
model class.

```php
// Using the Facade
$invitation = Invitation::to('test@example.com')->invitee(User::class)->make();

// Using the Referer Model
$invitation = $user->invitation()->to('test@example.com')->invitee($invitee)->make();

$invitation->invitable_type // will return App/Models/User
```

### Expiration

In addition to the config file, you can specify the expiration of a specific invitation using the `expireAt`
or `expireIn` methods.

- #### Expire at

This method accepts a date as string or Carbon instance and sets the invitation `expires_at` property to the given date.

```php
use Carbon\Carbon;

// Using the Facade
$invitation = Invitation::to('test@example.com')->expireAt('2022-02-02')-make(); //$invitation->expires_at will return '2022-02-02 00:00:00'
$invitation = Invitation::to('test@example.com')->expireAt('2022-02-02 12:50:30')-make(); //$invitation->expires_at will return '2022-02-02 12:50:30'
$invitation = Invitation::to('test@example.com')->expireAt(Carbon::parse('2022-01-01'))-make(); //$invitation->expires_at will return '2022-01-01 00:00:00'

// Using the Referer Model
$invitation = $user->invitation()->to('test@example.com')->expireAt('2022-02-02')-make(); //$invitation->expires_at will return '2022-02-02 00:00:00'
$invitation = $user->invitation()->to('test@example.com')->expireAt('2022-02-02 12:50:30')-make(); //$invitation->expires_at will return '2022-02-02 12:50:30'
$invitation = $user->invitation()->to('test@example.com')->expireAt(Carbon::parse('2022-01-01'))-make(); //$invitation->expires_at will return '2022-01-01 00:00:00'

```

- #### Expire in

This method accepts two parameters, the value and unit.

```php
use Carbon\Carbon;

// Using the Facade
$invitation = Invitation::to('test@example.com')->expireIn(48, 'hours')-make(); //$invitation->expires_at will return now() + 48 hours
$invitation = Invitation::to('test@example.com')->expireIn(10, 'days')-make(); //$invitation->expires_at will return now() + 10 days

// Using the Referer Model
$invitation = $user->invitation()->to('test@example.com')->expireIn(15, 'minutes')-make();  //$invitation->expires_at will return now() + 15 minutes
$invitation = $user->invitation()->to('test@example.com')->expireIn(2, 'months')-make();  //$invitation->expires_at will return now() + 2 months

```

### Getting an invitation by its token

```php
Invitation::findByToken('1234');
```

### Accepting invitations

```php
$invitation->accept();
```

### Declining invitations

```php
$invitation->decline();
```

### Delete on decline

If `delete_on_decline` option in `config/invite.php` is set to **true**, whenever an invitation is declined it will
automatically be deleted.

```php
$invitation->decline();

$invitation //null
```

### Auto delete expired invitations

Enabling `expire.delete.auto` option in `config/invite.php`, will
run `Rubik\LaravelInvite\Commands\DeleteExpiredInvitesCommand` every hour that deletes all invitations the expiry date
of which has surpassed the value given in `expire.delete.after` option in `config/invite.php`

### Using a custom Invitation class

If you are using a custom invitation class make sure it extends the default `Invitation` class that is shipped with this
package.

``` php
namespace App\Models;
use Rubik\LaravelInvite\Models\Invitation;

class CustomInvitation extends Invitation
{   
    ...
}
```

In addition to that, you need to set the `invitation_model` value in the config file to the path of your custom class.

```php
// config/invite.php

return [
     ...
    
    'invitation_model' => App\Models\CustomInvitation::class,  
     
     ...
]
````

## Events

The package dispatches various events

- ### Rubik\LaravelInvite\Events\InvitationCreated

  This event dispatches whenever a new Invitation is created.

- ### Rubik\LaravelInvite\Events\InvitationAccepted

  This event dispatches whenever the `accept` method is called and successfully executed.

- ### Rubik\LaravelInvite\Events\InvitationDeclined

  This event dispatches whenever the `decline` method is called and successfully executed.


- ### Rubik\LaravelInvite\Events\InvitationDeleted

  This event dispatches whenever an Invitation is deleted.

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](https://github.com/spatie/.github/blob/main/CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Rron Nela](https://github.com/rronik)
- [Yllndrit Beka](https://github.com/yllndritb)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
