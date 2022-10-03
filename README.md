# Zoho CRM connector

A simple connector to Zoho CRM API v3.

Unlike official Zoho SDK this connector does not provide helpers for each API endpoint.
Essentially this is just a thin wrapper around Guzzle HTTP client that authorizes requests
to Zoho API.
Note that you need to handle connection errors yourself.

## System Requirements
PHP 8.1 or later

## Installation
```
composer require chi-teck/zoho-api-connector
```

[Register](https://www.zoho.com/crm/developer/docs/api/v3/register-client.html) your application in Zoho Developer Console.
The way you obtain auth token (grant code) depends on the application type. Refer to [Authorization Request guide](https://www.zoho.com/crm/developer/docs/api/v3/auth-request.html) for details.

## Usage
```php
<?php declare(strict_types = 1);

use GuzzleHttp\Client;
use ZohoCrmConnector\Config;
use ZohoCrmConnector\Connector;
use ZohoCrmConnector\Auth\Storage\FileStorage;
use ZohoCrmConnector\Auth\AccessTokenProvider;

require __DIR__ . '/../vendor/autoload.php';

// Configuring the connector.
$config = new Config(
  domain: 'https://accounts.zoho.com',
  clientId: '•••••••••••••••••••••••••••••••••••',
  clientSecret: '••••••••••••••••••••••••••••••••••••••••••',
  authToken: '•••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••',
);

$storage = new FileStorage(__DIR__ . '/path/to/zoho-token.bin');
$token_provider = new AccessTokenProvider($config, $storage, new Client());

$connector = new Connector($token_provider);

// Retreiving data.
$response = $connector->get('Leads?fields=Last_Name&per_page=5');
print_r($response->decode());

// Posting data.
$data = [
    [
      "First_Name" => "Mickey",
      "Last_Name" => "Mouse",
    ],
];
$response = $connector->post('Leads', ['data' => $data]);
print_r($response->decode());
```

## License
GNU General Public License, version 2 or later.
