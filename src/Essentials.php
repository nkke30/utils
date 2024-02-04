<?php
//declare(strict_types=1);


namespace Nickimbo\Utils;

use stdClass;

class Stringer {
    private string $Key;
    
    private string $IV;

    private string $Method;

    public function __construct(string | array | stdClass $Config)  {
        if(is_string($Config)) {
            if(!is_file($Config)) throw new \Exception(Basic::String('{} is not a File.', $Config));
            if(!str_ends_with($Config, '.json')) throw new \Exception(Basic::String('{} is not an JSON File.', $Config));
            if(!file_exists($Config)) throw new \Exception(Basic::String('{} does not exist.', $Config));
            $Load = Basic::LoadJSON($Config, 0);
            $this->Key = $this->Transform($Load->key);
            $this->IV = $this->Transform($Load->iv, 'iv');
            $this->Method = $Load->method;
        } elseif(is_array($Config)) {
            $this->Key = $this->Transform($Config['key']); $this->IV = $this->Transform($Config['iv'], 'iv'); $this->Method = $Config['method'];
        } else {
            $this->Key = $this->Transform($Config->key); $this->Transform($this->IV = $Config->iv, 'iv'); $this->Method = $Config->method;
        }
    }

    private function Transform(string $haystack, $Type = 'key'): string {
        return substr(hash('sha256', $haystack), 0, $Type == 'key' ? 32 : 16);
    }
    public function Encrypt(string $needle): string {
        return base64_encode(openssl_encrypt($needle, $this->Method, $this->Key, 0, $this->IV));
    }
    public function Decrypt(string $needle): ?string {
        return openssl_decrypt(base64_decode($needle), $this->Method, $this->Key, 0, $this->IV);
    }
}


class Response {
    private array $format;

    private int $status;

    private array $headers;




    public function __construct(array | stdClass $Format, ?array $Options = null) {

        //if(array_search('{{message}}', $Format) === false) throw new \Error("{{message}} value not found when constructing Errors object");

        $this->format = (array) $Format;

        if($Options !== null) {
            if(isset($Options['headers']) && gettype($Options['headers']) === 'array') $this->headers = $Options['headers'];
            if(isset($Options['status']) && gettype($Options['status']) === 'int') $this->status = $Options['status'];
        }
    }

    private function _Dash(string | array $Message, ?int $Status = null, ?array $Headers = null): void {




        if(gettype($Message) === 'string') {
            $Format = Basic::Lize($this->format, ['{{status}}', '{{message}}'], [
                $Status ? $Status : ($this->status ? $this->status : 402),
                $Message
            ]);
        }
        
        http_response_code($Status ?? $this->status);

        if ($this->headers) {
            if ($Headers !== null) {
                foreach(array_merge($this->headers, $Headers) as $Key => $Value) header(Basic::String('{}: {}', $Key, $Value));
            } else {
                foreach($this->headers as $Key => $Value) header(Basic::String('{}: {}', $Key, $Value));
            }
        } elseif($Headers !== null) {
            foreach($Headers as $Key => $Value) header(Basic::String('{}: {}', $Key, $Value));
        }

        die(json_encode(isset($Format) ? $Format : $Message, JSON_PRETTY_PRINT | JSON_NUMERIC_CHECK | JSON_UNESCAPED_UNICODE));
    }

    public function Error(string $Message, ?int $Status = null, ?array $Headers = null): void {

         $Status = $Status ? ((int) $Status[0] === 4 ? $Status : 403) : 403;

         $this->_Dash($Message, $Status, $Headers);
    }

    public function Ok(string $Message, ?int $Status = null, ?array $Headers = null): void {
        $Status = $Status ? ((int) $Status[0] === 2 ? $Status : 200) : 200;

        $this->_Dash($Message, $Status, $Headers);
    }

    public function Response(array $Response, ?int $Status = null, ?array $Headers = null): void {
        $this->_Dash($Response, $Status, $Headers);
    }
}
?>