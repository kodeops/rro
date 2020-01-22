<?php
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
        } elseif (isset($response->error)) {
            $isSuccess = false;
            $message = $response->error->message;
            $type = $response->error->type;
            $data = isset($response->error->data) ? $response->error->data : null;
            $status_code = isset($response->status_code) ? $response->status_code : 400;
        } else {
            return false;
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
if (!function_exists('is_rro')) {
    function is_rro($rro) {
        return $rro instanceof \kodeops\rro\RichResponseObject;
   }
}   
