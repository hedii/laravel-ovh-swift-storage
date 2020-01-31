[![Build Status](https://travis-ci.org/hedii/laravel-ovh-swift-storage.svg?branch=master)](https://travis-ci.org/hedii/laravel-ovh-swift-storage)

# Laravel Ovh Swift Storage

Out of the box [Ovh Swift storage](https://www.ovhcloud.com/en-ie/public-cloud/object-storage/) usage for Laravel 6.0+

## Table of contents

- [Table of contents](#table-of-contents)
- [Installation](#installation)
- [Usage](#usage)
  - [Example](#example)
- [Testing](#testing)
- [License](#license)

## Installation

Install via [composer](https://getcomposer.org/doc/00-intro.md)

```sh
composer require hedii/laravel-ovh-swift-storage
```

Edit `config/filesystems.php` to add the new `ovh-swift` disk

```php
return [

    'disks' => [
        /* ... */

        'ovh-swift' => [
            'driver' => 'ovh-swift',
            'authUrl' => env('OVH_SWIFT_OPENSTACK_AUTH_URL', 'https://auth.cloud.ovh.net/v3/'),
            'region' => env('OVH_SWIFT_OPENSTACK_REGION'),
            'projectId' => env('OVH_SWIFT_OPENSTACK_PROJECT_ID'),
            'containerName' => env('OVH_SWIFT_CONTAINER_NAME'),
            'prefix' => env('OVH_SWIFT_PREFIX'),
            'username' => env('OVH_SWIFT_OPENSTACK_USERNAME'),
            'password' => env('OVH_SWIFT_OPENSTACK_PASSWORD'),
            'visibility' => env('OVH_SWIFT_VISIBILITY', 'public'),
            'public_url' => env('OVH_SWIFT_PUBLIC_URL'),
        ],
    
    ],

];
```

Edit `.env` to add the required environment variables

```
OVH_SWIFT_OPENSTACK_REGION=GRA
OVH_SWIFT_OPENSTACK_PROJECT_ID=xxxxxxxxxxxxxxxxxxx
OVH_SWIFT_CONTAINER_NAME=xxxxxxxxxxxxxxxxxxx
OVH_SWIFT_OPENSTACK_USERNAME=xxxxxxxxxxxxxxxxxxx
OVH_SWIFT_OPENSTACK_PASSWORD=xxxxxxxxxxxxxxxxxxx
```

## Usage

Once you have modified the Laravel filesystem configuration and the environment variables, you can use the new Ovh Swift storage disk [as any Laravel storage disk](https://laravel.com/docs/6.x/filesystem#obtaining-disk-instances).

### Example

```php
use Illuminate\Support\Facades\Storage;

Storage::disk('ovh-swift')->put('avatars/1', $fileContents);
```

## Testing

If you want to test the package, you must create a new Ovh Object Storage container and a new Openstack user. Once it's done, copy `phpunit.xml.dist` to `phpunit.xml` and update the environment variables.

Be aware that the test suite will delete all files in the container after each test. **Do not test against a production container!**

To start test suite, run this command:

```
composer test
```

## License

laravel-ovh-swift-storage is released under the MIT Licence. See the bundled [LICENSE](https://github.com/hedii/laravel-ovh-swift-storage/blob/master/LICENSE.md) file for details.
