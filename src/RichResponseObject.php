<?php
namespace kodeops\rro;

use Illuminate\Support\Str;
use Illuminate\Support\Arr;

class RichResponseObject
{
    const SUCCESS_TYPE = 'response';
    const SUCCESS_STATUS_CODE = 200;
    const ERROR_TYPE = 'error';
    const ERROR_STATUS_CODE = 400;
    const SLUG_DELIMITER = '_';

    public $isSuccess;
    public $message;
    public $type;
    public $key;
    public $status_code;
    public $data;
    public $response;

    public function __construct($isSuccess, $message, $type = null, $response_data = null, $status_code = null)
    {
        $this->isSuccess = $isSuccess;
        $this->setKey($isSuccess);
        $this->setResponseType($type);
        $this->setResponseMessage($message);
        $this->setResponseData($response_data);
        $this->setStatusCode($status_code);
    }

    public function setKey($success)
    {
        $this->key = $success ? self::SUCCESS_TYPE : self::ERROR_TYPE;

        return $this;
    }

    public function getKey()
    {
        return $this->key;
    }

    public function setResponseType($type)
    {
        $this->type = is_null($type) ? $this->getResponseMessage() : $type;
        $this->type = Str::slug($this->type, self::SLUG_DELIMITER);
        $this->response[$this->getKey()]['type'] = $this->type;

        return $this;
    }

    public function getResponseType()
    {
        return $this->type;
    }

    // ALIAS
    public function type($type)
    {
        $this->type = $type;
        return $this->setResponseType($this->type);
    }

    private function setResponseMessage($message)
    {
        $this->message = $message;
        $this->response[$this->getKey()]['message'] = $this->message;

        if ($this->getResponseType() == '') {
            $this->setResponseType($this->message);
        }

        return $this;
    }

    public function getResponseMessage()
    {
        return $this->message;
    }

    // ALIAS
    public function message($message)
    {
        return $this->setResponseMessage($message);
    }

    private function setStatusCode($status_code)
    {
        $this->status_code = $status_code;

        return $this;
    }

    public function getStatusCode()
    {
        return $this->status_code;
    }

    // ALIAS
    public function code($status_code)
    {
        return $this->setStatusCode($status_code);
    }

    // ALIAS
    public function statusCode($status_code)
    {
        return $this->setStatusCode($status_code);
    }

    private function setResponseData($data)
    {
        if (is_null($data)) {
            return $this;
        }

        $this->data = $data;
        $this->response[$this->getKey()]['data'] = $this->data;

        return $this;
    }

    public function data($data)
    {
        return $this->setResponseData($data);
    }

    public function getData($dot = false)
    {
        if ($dot) {
            return Arr::get($this->getData(), $dot);
        }

        return $this->data;
    }

    public function addData($new_data)
    {
        $this->setResponseData(array_merge($this->data, $new_data));
        return $this;
    }

    public function setResponse($response)
    {
        $this->response = $response;

        return $this;
    }

    public function getResponse()
    {
        return $this->response;
    }

    public function build()
    {
        if (!($this->getStatusCode())) {
            $this->setStatusCode($this->isSuccess() ? 200 : 400);
        }

        if ($this->getResponseType() == '' AND $this->getResponseMessage() != '') {
            $this->setResponseType($this->getResponseMessage());
        }

        if (env('APP_DEBUG', false)) {
            $this->response['debug_backtrace'] = debug_backtrace()[1];
        }

        return $this;
    }

    public function isSuccess()
    {
        return $this->isSuccess;
    }

    public function isError()
    {
        return !$this->isSuccess;
    }

    public function isResponseType($type)
    {
        return $type == $this->type;
    }

    public function isResponseMessage($message)
    {
        return $message == $this->message;
    }

    public function toResponse()
    {
        return response()->json($this->getResponse(), $this->getStatusCode());
    }

    // ALIAS
    public function __toString()
    {
        return json_encode($this->toResponse());
    }
}