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
if (!function_exists('success')) {
    function success($response_message = null) {
        return (new \kodeops\rro\RichResponseObject(
            true, 
            $response_message, 
            str_slug($response_message, '_')
        ))->build();
    }
}

if (!function_exists('error')) {
    function error($response_message = null) {
        return (new \kodeops\rro\RichResponseObject(
            false, 
            $response_message, 
            str_slug($response_message, '_')
        ))->build();
    }
}

```

Add `helpers.php` to `composer.json` autoload section:

```
"files": [
    "Rayotable/helpers.php"
],
``` 
##  Methods

### `type(string $string)`

### `message(string $string)`

### `data(array $data)`

### `code(int $code)`

### `toResponse()`

### `isError()`

### `isSuccess()`