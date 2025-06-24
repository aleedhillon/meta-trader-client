# MetaTrader Client for Laravel

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Total Downloads][ico-downloads]][link-downloads]
[![License: MIT][ico-license]][link-license]

A powerful Laravel package for seamless integration with the MetaTrader 5 Web API. This package enables brokers and financial applications to connect with MT5 servers and perform various operations including account management, trading operations, and market data retrieval.

**✨ Modernized for PHP 8.4** - Features modern PHP syntax, proper exception handling, type safety, and clean API design.

## 📋 Features

- **🔗 Server Connection Management**: Establish secure connections to MT5 servers with automatic connection handling
- **👥 User Management**: Create, update, and manage trading accounts with full lifecycle support
- **📈 Trading Operations**: Execute trades, manage orders and positions with real-time updates
- **📊 Market Data**: Access real-time quotes, ticks, and symbol information
- **👥 Group Management**: Create and manage user groups with flexible permissions
- **⚙️ Symbol Configuration**: Configure trading instruments with advanced settings
- **📧 Communication Tools**: Send emails and news to platform users
- **🚨 Exception Handling**: Comprehensive exception system with specific error types
- **🔐 Type Safety**: Modern PHP 8.4 with strict typing and null safety
- **🎯 Clean API**: No more reference parameters - all methods return values directly

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
    'agent' => env('MT5_AGENT', 'WebAPI'),
    'should_crypt' => env('MT5_SHOULD_CRYPT', true),
    'ip' => env('MT5_SERVER_IP'),
    'port' => env('MT5_SERVER_PORT', 443),
    'login' => env('MT5_SERVER_WEB_LOGIN'),
    'password' => env('MT5_SERVER_WEB_PASSWORD'),
    'timeout' => env('MT5_SERVER_TIMEOUT', 30),
];
```

Add the following environment variables to your `.env` file:

```
MT5_AGENT=WebAPI
MT5_SHOULD_CRYPT=true
MT5_SERVER_IP=your-mt5-server-ip
MT5_SERVER_PORT=443
MT5_SERVER_WEB_LOGIN=your-web-api-login
MT5_SERVER_WEB_PASSWORD=your-web-api-password
MT5_SERVER_TIMEOUT=30
```

## 🎯 Exception Handling

The modernized client uses a comprehensive exception system instead of return codes. All exceptions extend `MetaTraderException`:

```php
use Aleedhillon\MetaTraderClient\Exceptions\MetaTraderException;
use Aleedhillon\MetaTraderClient\Exceptions\AuthenticationException;
use Aleedhillon\MetaTraderClient\Exceptions\NetworkException;
use Aleedhillon\MetaTraderClient\Exceptions\UserManagementException;
use Aleedhillon\MetaTraderClient\Exceptions\TradingException;
use Aleedhillon\MetaTraderClient\Exceptions\ConfigurationException;

try {
    $user = MetaTraderClient::userGet(123456);
} catch (AuthenticationException $e) {
    // Handle authentication errors (1000-1023)
    Log::error('Authentication failed: ' . $e->getMessage());
} catch (UserManagementException $e) {
    // Handle user management errors (3001-3012)
    Log::error('User management error: ' . $e->getMessage());
} catch (NetworkException $e) {
    // Handle network errors (7-10)
    Log::error('Network error: ' . $e->getMessage());
} catch (MetaTraderException $e) {
    // Handle other MT5 errors
    Log::error('MT5 Error: ' . $e->getMessage() . ' (Code: ' . $e->getMtCode() . ')');
}
```

### Exception Types

- **AuthenticationException** - Authentication and authorization errors (codes 1000-1023)
- **ConfigurationException** - Configuration and setup errors (codes 2000-2012)
- **UserManagementException** - User management operations (codes 3001-3012)
- **TradeManagementException** - Trade management operations (codes 4001-4005)
- **TradingException** - Trading operations (codes 10001-11002)
- **ReportException** - Report generation (codes 5001-6001)
- **NetworkException** - Network and connection issues (codes 7-10)

## 📖 Usage

### Basic Connection

The client now features **automatic connection management** - you don't need to manually connect/disconnect:

```php
use Aleedhillon\MetaTraderClient\Facades\MetaTraderClient;

try {
    // The client automatically connects when needed
    $serverTime = MetaTraderClient::timeGet();
    echo "Server time: " . $serverTime->TimeServer;
    
    // Check connection status
    if (MetaTraderClient::isConnected()) {
        echo "Connected to MT5 server";
    }
    
    // Manual connection (optional)
    MetaTraderClient::connect();
    
} catch (MetaTraderException $e) {
    echo "Error: " . $e->getMessage();
}

// Manual disconnect (optional - handled automatically)
MetaTraderClient::disconnect();
```

### User Management

All user management methods now return objects directly and throw exceptions on errors:

```php
use Aleedhillon\MetaTraderClient\Facades\MetaTraderClient;
use Aleedhillon\MetaTraderClient\Lib\MTEnDealAction;

try {
    // Create a new user
    $user = MetaTraderClient::userCreate();
    $user->Name = 'John Doe';
    $user->Email = 'john@example.com';
    $user->Group = 'demo';
    $user->Leverage = 100;
    $user->MainPassword = 'password123';
    $user->Phone = '+1234567890';

    $newUser = MetaTraderClient::userAdd($user);
    echo "Created user with login: " . $newUser->Login;

    // Get user information
    $userInfo = MetaTraderClient::userGet(123456);
    echo "User: " . $userInfo->Name . " (" . $userInfo->Email . ")";

    // Update user
    $userInfo->Leverage = 200;
    $updatedUser = MetaTraderClient::userUpdate($userInfo);

    // Delete user
    MetaTraderClient::userDelete(123456);

    // Check user password (returns true or throws exception)
    $isValidPassword = MetaTraderClient::userPasswordCheck(123456, 'password123');

    // Change user password
    MetaTraderClient::userPasswordChange(123456, 'newpassword123');

    // Change user balance
    MetaTraderClient::userDepositChange(
        123456,                      // User login
        1000.00,                     // New deposit amount
        'API deposit',               // Comment
        MTEnDealAction::DEAL_BALANCE // Deal type
    );

    // Get user account information
    $account = MetaTraderClient::userAccountGet(123456);
    echo "Balance: " . $account->Balance . " " . $account->Currency;

    // Get list of user logins
    $logins = MetaTraderClient::userLogins('demo');
    
} catch (UserManagementException $e) {
    echo "User management error: " . $e->getMessage();
} catch (MetaTraderException $e) {
    echo "MT5 error: " . $e->getMessage();
}
```

### Trading Operations

Trading operations now return objects directly with full type safety:

```php
try {
    // Get order information
    $order = MetaTraderClient::orderGet(987654);
    echo "Order: " . $order->Symbol . " Volume: " . $order->VolumeInitial;

    // Get total number of orders for user
    $orderCount = MetaTraderClient::orderGetTotal(123456);

    // Get orders by page
    $orders = MetaTraderClient::orderGetPage(123456, 0, 10);
    foreach ($orders as $order) {
        echo "Order " . $order->Order . ": " . $order->Symbol;
    }

    // Get position
    $position = MetaTraderClient::positionGet(123456, 'EURUSD');
    echo "Position: " . $position->Symbol . " Profit: " . $position->Profit;

    // Get all positions count
    $positionCount = MetaTraderClient::positionGetTotal(123456);

    // Get positions by page
    $positions = MetaTraderClient::positionGetPage(123456, 0, 10);

    // Get deal information
    $deal = MetaTraderClient::dealGet(789012);
    echo "Deal: " . $deal->Symbol . " Volume: " . $deal->Volume;

    // Get deals count
    $dealCount = MetaTraderClient::dealGetTotal(123456, strtotime('-1 week'), time());

    // Get deals by page
    $deals = MetaTraderClient::dealGetPage(123456, strtotime('-1 week'), time(), 0, 10);

    // Get history order
    $historyOrder = MetaTraderClient::historyGet(456789);

    // Get history count
    $historyCount = MetaTraderClient::historyGetTotal(123456, strtotime('-1 month'), time());

    // Get history by page
    $historyOrders = MetaTraderClient::historyGetPage(123456, strtotime('-1 month'), time(), 0, 10);

    // Execute balance operation
    $ticket = MetaTraderClient::tradeBalance(
        123456,                      // User login
        MTEnDealAction::DEAL_BALANCE, // Operation type
        1000.00,                     // Amount
        'Deposit via API',           // Comment
        true                         // Margin check
    );
    
    if ($ticket) {
        echo "Balance operation ticket: " . $ticket;
    }
    
} catch (TradingException $e) {
    echo "Trading error: " . $e->getMessage();
} catch (MetaTraderException $e) {
    echo "MT5 error: " . $e->getMessage();
}
```

### Market Data

Market data operations with modern return types:

```php
try {
    // Get symbol information
    $symbol = MetaTraderClient::symbolGet('EURUSD');
    echo "Symbol: " . $symbol->Symbol . " Digits: " . $symbol->Digits;

    // Get symbol count
    $symbolCount = MetaTraderClient::symbolTotal();

    // Get next symbol by position
    $nextSymbol = MetaTraderClient::symbolNext(0);

    // Get symbol by group
    $groupSymbol = MetaTraderClient::symbolGetGroup('EURUSD', 'demo');

    // Get last ticks
    $ticks = MetaTraderClient::tickLast('EURUSD');
    foreach ($ticks as $tick) {
        echo "Tick: " . $tick->Bid . "/" . $tick->Ask;
    }

    // Get last ticks by group
    $groupTicks = MetaTraderClient::tickLastGroup('EURUSD', 'demo');

    // Get tick statistics
    $tickStats = MetaTraderClient::tickStat('EURUSD');
    
} catch (MetaTraderException $e) {
    echo "Market data error: " . $e->getMessage();
}
```

### Group Management

Group management with modern API design:

```php
try {
    // Get total groups count
    $groupCount = MetaTraderClient::groupTotal();

    // Get next group
    $group = MetaTraderClient::groupNext(0);
    echo "Group: " . $group->Group;

    // Create a new group
    $newGroup = MetaTraderClient::groupCreate();
    $newGroup->Group = 'new_demo';
    $newGroup->Company = 'Demo Company';
    $newGroup->Currency = 'USD';
    // Set other group properties...

    $createdGroup = MetaTraderClient::groupAdd($newGroup);

    // Get group information
    $groupInfo = MetaTraderClient::groupGet('demo');
    echo "Group: " . $groupInfo->Group . " Currency: " . $groupInfo->Currency;

    // Delete group
    MetaTraderClient::groupDelete('old_demo');
    
} catch (ConfigurationException $e) {
    echo "Configuration error: " . $e->getMessage();
} catch (MetaTraderException $e) {
    echo "MT5 error: " . $e->getMessage();
}
```

### Symbol Management

Symbol configuration with type-safe operations:

```php
try {
    // Create a new symbol
    $symbol = MetaTraderClient::symbolCreate();
    $symbol->Symbol = 'CUSTOM';
    $symbol->Description = 'Custom Symbol';
    $symbol->CurrencyBase = 'USD';
    $symbol->CurrencyProfit = 'USD';
    $symbol->Digits = 5;
    // Set other symbol properties...

    $newSymbol = MetaTraderClient::symbolAdd($symbol);
    echo "Created symbol: " . $newSymbol->Symbol;

    // Get symbol information
    $symbolInfo = MetaTraderClient::symbolGet('EURUSD');

    // Delete symbol
    MetaTraderClient::symbolDelete('OBSOLETE');
    
} catch (ConfigurationException $e) {
    echo "Symbol configuration error: " . $e->getMessage();
} catch (MetaTraderException $e) {
    echo "MT5 error: " . $e->getMessage();
}
```

### Communication

Send notifications with error handling:

```php
try {
    // Send email to user
    MetaTraderClient::mailSend(
        '123456',                    // User login
        'Important Notification',     // Subject
        '<h1>Important Message</h1><p>This is an important message.</p>' // HTML body
    );

    // Send news
    MetaTraderClient::newsSend(
        'Market Update',             // Subject
        'General',                   // Category
        1033,                        // Language (English)
        1,                          // Priority
        '<h2>Market Update</h2><p>Important market information.</p>' // HTML body
    );
    
} catch (MetaTraderException $e) {
    echo "Communication error: " . $e->getMessage();
}
```

### Server Management

Server operations with modern exception handling:

```php
try {
    // Get server time
    $time = MetaTraderClient::timeGet();
    echo "Server time: " . date('Y-m-d H:i:s', $time->TimeServer);

    // Get server time as Unix timestamp
    $timestamp = MetaTraderClient::timeServer();

    // Get common server information
    $common = MetaTraderClient::commonGet();
    echo "Server: " . $common->Name . " Build: " . $common->Build;

    // Ping server
    MetaTraderClient::ping();

    // Restart server (requires appropriate permissions)
    MetaTraderClient::serverRestart();
    
} catch (NetworkException $e) {
    echo "Network error: " . $e->getMessage();
} catch (MetaTraderException $e) {
    echo "Server error: " . $e->getMessage();
}
```

### Custom Commands

Execute custom commands with structured responses:

```php
try {
    // Send custom command to MT5 server
    $result = MetaTraderClient::customSend(
        'YOUR_CUSTOM_COMMAND',
        ['param1' => 'value1', 'param2' => 'value2'],
        'Request body'
    );
    
    $answer = $result['answer'];
    $answerBody = $result['answer_body'];
    
} catch (MetaTraderException $e) {
    echo "Custom command error: " . $e->getMessage();
}
```

### Utility Methods

The client provides many static utility methods for working with MT5 data:

```php
use Aleedhillon\MetaTraderClient\Facades\MetaTraderClient;

// Error handling utilities
$errorDescription = MetaTraderClient::getErrorDescription(10004);
echo "Error: " . $errorDescription; // "No connection"

// Volume conversion utilities
$newVolume = MetaTraderClient::toNewVolume(100); // Convert 1.00 lots to 1000000
$oldVolume = MetaTraderClient::toOldVolume(1000000); // Convert back to 100

// Validation utilities
$isValidMode = MetaTraderClient::validateTradeMode(4); // Returns 4 (TRADE_FULL)
$isValidSymbol = MetaTraderClient::isValidSymbolName('EURUSD'); // true
$isValidLogin = MetaTraderClient::isValidLogin(123456); // true

// Data conversion utilities
$hexString = MetaTraderClient::binaryToHex('test'); // Convert to hex
$binaryData = MetaTraderClient::hexToBinary('74657374'); // Convert from hex
$escaped = MetaTraderClient::escapeProtocolString('test=value|data'); // Escape special chars

// Time utilities
$formatted = MetaTraderClient::formatMtTimestamp(time(), 'Y-m-d H:i:s');
$mtTime = MetaTraderClient::toMtTimestamp(); // Current time

// Default values
$marginRates = MetaTraderClient::getDefaultMarginRates();
$randomHex = MetaTraderClient::generateRandomHex(32); // For testing

// Version information
$version = MetaTraderClient::getVersionInfo();
echo "MT5 Web API Version: " . $version['web_api_version'];
```

## 🧪 Development & Testing

### Using Workbench

This package includes a [Workbench](https://packages.tools/workbench) environment for development and testing. The workbench provides a complete Laravel application environment to test the package functionality.

#### Setup Workbench

1. Copy the environment file:
   ```bash
   cp workbench/.env.example workbench/.env
   ```

2. Configure your MT5 server details in `workbench/.env`:
   ```env
   MT5_SERVER_IP=your.mt5server.com
   MT5_SERVER_PORT=443
   MT5_SERVER_WEB_LOGIN=your_web_api_login
   MT5_SERVER_WEB_PASSWORD=your_web_api_password
   ```

3. Test the connection:
   ```bash
   php vendor/bin/testbench test:mt5
   ```

#### Available Commands

- **Test Connection**: `php vendor/bin/testbench test:mt5`
- **Demo Mode**: `php vendor/bin/testbench test:mt5 --demo`
- **Serve Application**: `composer run serve` (if configured)

The workbench command provides a comprehensive test of your MT5 connection including:
- Server connectivity validation
- Authentication testing
- Server information retrieval
- Error handling demonstration

## 🔧 Advanced Usage

### Error Code to Exception Mapping

The client automatically maps MT5 error codes to specific exception types:

```php
try {
    $user = MetaTraderClient::userGet(123456);
} catch (MetaTraderException $e) {
    // Get the original MT5 error code
    $mtCode = $e->getMtCode();
    echo "MT5 Error Code: " . $mtCode;
    
    // Exception type is determined by error code range:
    // 1000-1023: AuthenticationException
    // 2000-2012: ConfigurationException  
    // 3001-3012: UserManagementException
    // 4001-4005: TradeManagementException
    // 10001-11002: TradingException
    // 5001-6001: ReportException
    // 7-10: NetworkException
}
```

### Direct Instantiation

For advanced use cases, instantiate the client directly:

```php
use Aleedhillon\MetaTraderClient\MetaTraderClient;

$client = new MetaTraderClient(
    'CustomAgent',               // Agent name
    true,                        // Use encryption
    '192.168.1.1',              // Custom IP
    8443,                        // Custom port
    60,                          // Custom timeout
    'manager',                   // Custom login
    'password123'                // Custom password
);

try {
    $client->connect();
    $user = $client->userGet(123456);
} catch (MetaTraderException $e) {
    echo "Error: " . $e->getMessage();
}
```

### Configuration Options

| Option         | Environment Variable      | Default  | Description                          |
| -------------- | ------------------------- | -------- | ------------------------------------ |
| `agent`        | `MT5_AGENT`               | `WebAPI` | User agent string for API requests   |
| `should_crypt` | `MT5_SHOULD_CRYPT`        | `true`   | Enable/disable connection encryption |
| `ip`           | `MT5_SERVER_IP`           | `null`   | MT5 server IP address                |
| `port`         | `MT5_SERVER_PORT`         | `443`    | MT5 server port                      |
| `login`        | `MT5_SERVER_WEB_LOGIN`    | `null`   | Web API login                        |
| `password`     | `MT5_SERVER_WEB_PASSWORD` | `null`   | Web API password                     |
| `timeout`      | `MT5_SERVER_TIMEOUT`      | `30`     | Connection timeout in seconds        |

## 🔍 Migration from Legacy Code

If you're upgrading from an older version, here are the key changes:

### Before (Legacy)
```php
// Old way with reference parameters and return codes
$user = null;
$result = MetaTraderClient::UserGet(123456, $user);
if ($result === MTRetCode::MT_RET_OK) {
    echo $user->Name;
} else {
    echo "Error code: " . $result;
}
```

### After (Modern)
```php
// New way with direct returns and exceptions
try {
    $user = MetaTraderClient::userGet(123456);
    echo $user->Name;
} catch (MetaTraderException $e) {
    echo "Error: " . $e->getMessage();
}
```

## 📚 API Reference

For a complete list of available methods and their parameters, please refer to the [MetaTrader 5 Web API Documentation](https://www.metatrader5.com/en/terminal/help/automation/webapi).

### Key Method Changes

- All methods now use `camelCase` naming (e.g., `userGet` instead of `UserGet`)
- Methods return objects directly instead of using reference parameters
- All methods throw `MetaTraderException` on errors instead of returning error codes
- Automatic connection management - no need to manually connect/disconnect
- Added comprehensive utility methods for data conversion, validation, and formatting
- Static helper methods for working with MT5 data formats, timestamps, and error codes

## 🔒 Security

If you discover any security-related issues, please email [aleedhx@gmail.com](mailto:aleedhx@gmail.com) instead of using the issue tracker.

## 🤝 Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

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
