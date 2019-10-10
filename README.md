```
 _     _  _____  ______  _______  _____   _____  _______
 |____/  |     | |     \ |______ |     | |_____] |______
 |    \_ |_____| |_____/ |______ |_____| |       ______|
 
```
 

# rro | rich response object

## Setup

Add composer package:

`$ composer require kodeops/rro:dev-master`

Add helpers:

```
use Illuminate\Support\Str;

if (!function_exists('success')) {
    function success($response_message = null) {
        return (new \kodeops\rro\RichResponseObject(
            true, 
            $response_message, 
            Str::slug($response_message, '_')
        ))->build();
    }
}

if (!function_exists('error')) {
    function error($response_message = null) {
        return (new \kodeops\rro\RichResponseObject(
            false, 
            $response_message, 
            Str::slug($response_message, '_')
        ))->build();
    }
}

if (!function_exists('rro')) {
    function rro($response) {
        if (isset($response->response)) {
            $isSuccess = true;
            $message = $response->response->message;
            $type = $response->response->type;
            $data = isset($response->response->data) ? $response->response->data : null;
            $status_code = isset($response->status_code) ? $response->status_code : 200;
        } else {
            $isSuccess = false;
            $message = $response->error->message;
            $type = $response->error->type;
            $data = isset($response->error->data) ? $response->error->data : null;
            $status_code = isset($response->status_code) ? $response->status_code : 400;
        }
        return (new \kodeops\rro\RichResponseObject(
            $isSuccess, 
            $message, 
            $type, 
            $data,
            $status_code
        ))->build();
    }
}

```

Add `helpers.php` to `composer.json` autoload section:

```
"files": [
    "<your-app-folder>/helpers.php"
],
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

### `isError()`

Wether the response is an error.

### `isSuccess()`

Wether the response is a success.

### Accessing response details
***	

### `response('message')`

Get the response message.

### `response('type')`

Get the response type.

### `response('data', $dot)`

Get the response data array (uses dot syntax to retrieve specific key). 

Example: `response('data', user.id')`

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