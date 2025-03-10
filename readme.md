# MetaTrader Client for Laravel

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Total Downloads][ico-downloads]][link-downloads]
[![License: MIT][ico-license]][link-license]

A powerful Laravel package for seamless integration with the MetaTrader 5 Web API. This package enables brokers and financial applications to connect with MT5 servers and perform various operations including account management, trading operations, and market data retrieval.

## 📋 Features

- **Server Connection Management**: Establish secure connections to MT5 servers
- **User Management**: Create, update, and manage trading accounts
- **Trading Operations**: Execute trades, manage orders and positions
- **Market Data**: Access real-time quotes, ticks, and symbol information
- **Group Management**: Create and manage user groups
- **Symbol Configuration**: Configure trading instruments
- **Communication Tools**: Send emails and news to platform users
- **Comprehensive Logging**: Detailed logging for debugging and auditing

## 🚀 Installation

You can install the package via composer:

```bash
composer require aleedhillon/meta-trader-client
```

The package will automatically register its service provider and facade.

## ⚙️ Configuration

Publish the configuration file:

```bash
php artisan vendor:publish --tag="meta-trader-client.config"
```

This will create a `config/meta-trader-client.php` file with the following content:

```php
<?php

return [
    'ip' => env('MT5_SERVER_IP'),
    'port' => env('MT5_SERVER_PORT', 443),
    'login' => env('MT5_SERVER_WEB_LOGIN'),
    'password' => env('MT5_SERVER_WEB_PASSWORD'),
    'timeout' => env('MT5_SERVER_TIMEOUT', 30),
];
```

Add the following environment variables to your `.env` file:

```
MT5_SERVER_IP=your-mt5-server-ip
MT5_SERVER_PORT=443
MT5_SERVER_WEB_LOGIN=your-web-api-login
MT5_SERVER_WEB_PASSWORD=your-web-api-password
MT5_SERVER_TIMEOUT=30
```

## 📖 Usage

### Basic Connection

```php
use Aleedhillon\MetaTraderClient\Facades\MetaTraderClient;

// Connect to the MT5 server
$result = MetaTraderClient::connect();

// Check if connected
if (MetaTraderClient::isConnected()) {
    // Perform operations
}

// Disconnect when done
MetaTraderClient::disconnect();
```

### User Management

```php
// Create a new user
$user = MetaTraderClient::UserCreate();
$user->Name = 'John Doe';
$user->Email = 'john@example.com';
$user->Group = 'demo';
$user->Leverage = 100;
$user->MainPassword = 'password123';
$user->Phone = '+1234567890';

$newUser = null;
$result = MetaTraderClient::UserAdd($user, $newUser);

// Get user information
$userInfo = null;
MetaTraderClient::UserGet(123456, $userInfo);

// Update user
$userInfo->Leverage = 200;
$updatedUser = null;
MetaTraderClient::UserUpdate($userInfo, $updatedUser);

// Delete user
MetaTraderClient::UserDelete(123456);

// Check user password
$result = MetaTraderClient::UserPasswordCheck(123456, 'password123');

// Change user password
MetaTraderClient::UserPasswordChange(123456, 'newpassword123');

// Get user account information
$account = null;
MetaTraderClient::UserAccountGet(123456, $account);
```

### Trading Operations

```php
// Get order information
$order = null;
MetaTraderClient::OrderGet(987654, $order);

// Get all user orders
$total = 0;
MetaTraderClient::OrderGetTotal(123456, $total);

// Get orders by page
$orders = [];
MetaTraderClient::OrderGetPage(123456, 0, 10, $orders);

// Get position
$position = null;
MetaTraderClient::PositionGet(123456, 'EURUSD', $position);

// Get all positions
$positions = [];
MetaTraderClient::PositionGetPage(123456, 0, 10, $positions);

// Get deal information
$deal = null;
MetaTraderClient::DealGet(789012, $deal);

// Get deals by page
$deals = [];
MetaTraderClient::DealGetPage(123456, strtotime('-1 week'), time(), 0, 10, $deals);

// Change user balance
use Aleedhillon\MetaTraderClient\Lib\MTEnDealAction;

$ticket = null;
MetaTraderClient::TradeBalance(
    123456,                      // User login
    MTEnDealAction::DEAL_BALANCE, // Operation type
    1000.00,                     // Amount
    'Deposit via API',           // Comment
    $ticket,                     // Ticket (output)
    true                         // Margin check
);
```

### Market Data

```php
// Get symbol information
$symbol = null;
MetaTraderClient::SymbolGet('EURUSD', $symbol);

// Get last ticks
$ticks = [];
MetaTraderClient::TickLast('EURUSD', $ticks);

// Get tick statistics
$tickStats = [];
MetaTraderClient::TickStat('EURUSD', $tickStats);
```

### Group Management

```php
// Create a new group
$group = MetaTraderClient::GroupCreate();
$group->Group = 'new_demo';
// Set group properties...

$newGroup = null;
MetaTraderClient::GroupAdd($group, $newGroup);

// Get group information
$groupInfo = null;
MetaTraderClient::GroupGet('demo', $groupInfo);

// Delete group
MetaTraderClient::GroupDelete('old_demo');
```

### Symbol Management

```php
// Create a new symbol
$symbol = MetaTraderClient::SymbolCreate();
$symbol->Symbol = 'CUSTOM';
// Set symbol properties...

$newSymbol = null;
MetaTraderClient::SymbolAdd($symbol, $newSymbol);

// Get symbol information
$symbolInfo = null;
MetaTraderClient::SymbolGet('EURUSD', $symbolInfo);

// Delete symbol
MetaTraderClient::SymbolDelete('OBSOLETE');
```

### Communication

```php
// Send email to user
MetaTraderClient::MailSend(
    '123456',                // User login
    'Important Notification', // Subject
    'This is an important message.' // Body (can be HTML)
);

// Send news
MetaTraderClient::NewsSend(
    'Market Update',         // Subject
    'General',               // Category
    'en',                    // Language
    1,                       // Priority
    'Important market update information.' // Body (can be HTML)
);
```

### Server Management

```php
// Get server time
$time = null;
MetaTraderClient::TimeGet($time);

// Get server time as Unix timestamp
$timestamp = MetaTraderClient::TimeServer();

// Get common information
$common = null;
MetaTraderClient::CommonGet($common);

// Ping server
$result = MetaTraderClient::Ping();

// Restart server (requires appropriate permissions)
MetaTraderClient::ServerRestart();
```

### Logging

```php
// Enable/disable logging
MetaTraderClient::SetLoggerIsWrite(true);

// Set log file path
MetaTraderClient::SetLoggerFilePath('/path/to/logs');

// Set log file prefix
MetaTraderClient::SetLoggerFilePrefix('mt5_api_');

// Enable/disable debug logging
MetaTraderClient::SetLoggerWriteDebug(true);
```

### Custom Commands

```php
// Send custom command to MT5 server
$command = 'YOUR_CUSTOM_COMMAND';
$params = ['param1' => 'value1', 'param2' => 'value2'];
$body = 'Request body';
$answer = [];
$answerBody = '';

MetaTraderClient::CustomSend($command, $params, $body, $answer, $answerBody);
```

## 🔄 Return Codes

Most methods return an `MTRetCode` value indicating the success or failure of the operation. You can check these codes against the constants defined in the `MTRetCode` class:

```php
use Aleedhillon\MetaTraderClient\Lib\MTRetCode;

$result = MetaTraderClient::connect();

if ($result === MTRetCode::MT_RET_OK) {
    // Operation successful
} else {
    // Handle error
    echo "Error code: $result";
}
```

## 🛠️ Advanced Usage

### Direct Instantiation

While the facade is convenient for most use cases, you can also instantiate the client directly for more control:

```php
use Aleedhillon\MetaTraderClient\MetaTraderClient;

$client = new MetaTraderClient(
    'CustomAgent',           // Agent name
    '/path/to/logs',         // Log file path
    true,                    // Use encryption
    '192.168.1.1',           // Custom IP
    8443,                    // Custom port
    60,                      // Custom timeout
    'manager',               // Custom login
    'password123'            // Custom password
);

$client->connect();
```

## 📚 API Documentation

For a complete list of available methods and their parameters, please refer to the [MetaTrader 5 Web API Documentation](https://www.metatrader5.com/en/terminal/help/automation/webapi).

## 🔒 Security

If you discover any security-related issues, please email [aleedhx@gmail.com](mailto:aleedhx@gmail.com) instead of using the issue tracker.

## 📄 License

The MIT License (MIT). Please see the [License File](license.md) for more information.

## 👨‍💻 Credits

- [Ali A. Dhillon](https://github.com/aleedhillon)
- [All Contributors](../../contributors)

[ico-version]: https://img.shields.io/packagist/v/aleedhillon/meta-trader-client.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/aleedhillon/meta-trader-client.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/License-MIT-brightgreen.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/aleedhillon/meta-trader-client
[link-downloads]: https://packagist.org/packages/aleedhillon/meta-trader-client
[link-license]: https://opensource.org/licenses/MIT
[link-author]: https://github.com/aleedhillon
[link-contributors]: ../../contributors
