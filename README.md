# Zoho CRM connector

A simple connector to Zoho CRM API version 3.

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

## Usage
```shell
// 1. Register your application in Zoho Developer Console.
// https://www.zoho.com/crm/developer/docs/api/v3/register-client.html
// 2. The way you obtains auth token (grant code) depends on the application type.
// Refer to https://www.zoho.com/crm/developer/docs/api/v3/auth-request.html for details.
$config = new Config(
  domain: 'https://accounts.zoho.com',
  clientId: '1000.MBPDCVAMR9QWRQ52GU942XN7O7J5FR',
  clientSecret: '010f88a0cb8014c23967f30c5ae7be5e8c76e1147e',
  authToken: '1000.6379e3e96c39a32b4938a23ad693aba3.27313aa79ff326efd92b695b8fc13b01',
);

$storage = new FileStorage(__DIR__ . '/path/to/zoho-token.bin');
$token_provider = new AccessTokenProvider($config, $storage, new Client());

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
