# mProfi API client

[![Build Status](https://travis-ci.org/mozarcik/mprofi_api_client_php.svg?branch=master)](https://travis-ci.org/mozarcik/mprofi_api_client_php)

Simple library for sending messages using mProfi API. This library provides classes and methods for sending single or 
many messages at once.

## Installation

You can install it using composer:
```
composer require mprofi/api-client
```

## Usage

### Single message

You can send single message like this

```php
$message = new mprofi\Message('5556667777', 'some message content');
$client = new mprofi\Client('api-token');

$messageIds = $client->send($message);
```

### Many messages

Sending many messages at one is almost the same as sending single message:

```php
$messages = [
    new mprofi\Message('5556667777', 'first message content'),
    new mprofi\Message('5556668888', 'second message content'),
];

$client = new mprofi\Client('api-token');
$messageIds = $client->send($messages);
```

### Get message status

While sending messages you get ids so you can later check status:
```php 
$client = new mprofi\Client('api-token');

$client->getStatus();
```