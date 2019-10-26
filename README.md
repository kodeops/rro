```
 _     _  _____  ______  _______  _____   _____  _______
 |____/  |     | |     \ |______ |     | |_____] |______
 |    \_ |_____| |_____/ |______ |_____| |       ______|
 
```
 

# rro | rich response object

## Setup

Add composer package:

`$ composer require kodeops/rro:dev-master`

Copy function helpers to your project within your namespace (recommended, not mandatory):

[https://raw.githubusercontent.com/kodeops/rro/master/src/helpers.php](https://raw.githubusercontent.com/kodeops/rro/master/src/helpers.php)

Add `rro-helpers.php` to `composer.json` autoload section:

```
"autoload": {
    ...
        
   "files": [
       "<your-app-folder>/rro-helpers.php"
   ]
}
```

## Usage 
Assuming helper functions are loaded in `composer.json`:

```
$rro = error()
  ->type('error_type')
  ->message('This is a sample rich response object.')
  ->code(404)
  ->data(['foo' => 'bar']);
```

Checking status:

```
if ($rro->isError()) {
  // Response contains an error payload
}

if ($rro->isSuccess()) {
  // Response contains a success payload
}

if ($rro->response('is_type', 'error_type')) {
  // returns true if given type matches response type
}

if ($rro->response('is_message', 'Descriptive error message')) {
  // returns true if given message matches response message
}
```

Accessing response:

```
$type = $rro->response('type');
$message = $rro->response('message');
$foo = $rro->response('data', 'foo');
$code = $rro->response('code');
$add = $rro->response('add', ['bar' => 'foo']);
```

##  Methods

### Building response
***	

### `type(string $string)`

Set the type for the response.

### `message(string $string)`

Set the message for the response.

### `data(array $data)`

Set the data for the response.

### `code(int $code)`

Set the status code for the response.

### Check response status
***	

### `isError()`

Wether the response is an error.

### `isSuccess()`

Wether the response is a success.

### Accessing response details
***	

### `response('type')`

Get the response type.

### `response('message')`

Get the response message.

### `response('data', $dot)`

Get the response data array (uses dot syntax to retrieve specific key). 

Example: `response('data', 'user.id')`

### `response('code')`

Get the response code.

### `response('add', array $data)`

Add more items to the data array (will be automatically merged to existing data).

### `response('is_type', $type)`

Wether the response type equals to the parameter sent.

### `response('is_message', $message)`

Wether the response message equals to the parameter sent.

### Render for Laravel response
***	

### `toResponse()`

Render response in Laravel way.