<?php
namespace kodeops\rro;

use Illuminate\Support\Str;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Lang;
use rroException;

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

    public function __construct(
        $isSuccess, 
        $message, 
        $type = null, 
        $response_data = null, 
        $status_code = null
    )
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

    public function setResponseType($type, $slugged = true)
    {
        $this->type = is_null($type) ? $this->getResponseMessage() : $type;
        
        if ($slugged) {
            $this->type = Str::slug($this->type, self::SLUG_DELIMITER);    
        }
        
        $this->response[$this->getKey()]['type'] = $this->type;

        return $this;
    }

    public function getResponseType()
    {
        return $this->type;
    }

    // ALIAS
    public function type($type, $slugged = true)
    {
        $this->type = $type;
        return $this->setResponseType($this->type, $slugged);
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

    // ALIAS
    public function status($status_code)
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
            if (is_array($this->data)) {
                return Arr::get($this->getData(), $dot);
            }
            
            if (is_object($this->data)) {
                if (isset($this->data->{$dot})) {
                    return $this->data->{$dot};
                } else {
                    return;
                }
            }
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

    public function isSuccess($property = null, $value = null)
    {
        if (is_null($property) OR is_null($value)) {
            return $this->isSuccess;
        }

        return $this->responseProperty($property, $value);
    }

    public function isError($property = null, $value = null)
    {
        if (is_null($property) OR is_null($value)) {
            return !$this->isSuccess;
        }

        return $this->responseProperty($property, $value);
    }

    private function responseProperty($property, $value)
    {
        if (! in_array($property, ['type', 'message'])) {
            throw new rroException("Invalid property: {$property}");
        }

        return $this->{'isResponse' . ucwords($property)}($value);
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

    public function toHtml()
    {
        $html = '<h1>' . $this->message . '</h1>';
        
        if (Str::slug($this->message, self::SLUG_DELIMITER) != $this->type) {
            $html .= '<h3><code>' . $this->type . '</code></h3>';
        }

        return response($html);
    }

    public function toArray()
    {
        return $this->response;
    }

    public function response($method, $arguments = null)
    {
        switch ($method) {
            case 'code':
                return $this->getStatusCode();
            break;

            case 'message':
                return $this->getResponseMessage();
            break;

            case 'type':
                return $this->getResponseType();
            break;

            case 'data':
                return $this->getData($arguments);
            break;

            case 'add':
                return $this->addData($arguments);
            break;

            case 'is_type':
                return $this->isResponseType($arguments);
            break;

            case 'is_message':
                return $this->isResponseMessage($arguments);
            break;

            default:
                throw new rroException("Invalid method: " . $method);
            break;
        }
    }

    public function trans(string $key, array $replace = [], string $locale = null, bool $fallback = true)
    {
        if (! Lang::has($key)) {
            throw new rroException("Translation not found: “{$method}”");
        }

        $this->setResponseType($key, false);
        $this->setResponseMessage(Lang::get($key, $replace, $locale, $fallback));

        return $this;
    }

    public function __toString()
    {
        return json_encode($this->toResponse());
    }
}