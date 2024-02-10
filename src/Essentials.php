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
    public function Encrypt(string $needle, ?bool $useTag = null): string {
        if ($useTag) {
            $Tag = self::Tag();
            return base64_encode(openssl_encrypt($needle, $this->Method, $this->Key, 0, $this->IV, $Tag));
        } else {
            return base64_encode(openssl_encrypt($needle, $this->Method, $this->Key, 0, $this->IV));
        }
    }
    public function Decrypt(string $needle, ?string $Tag = null): ?string {
        return $Tag ? openssl_decrypt(base64_decode($needle), $this->Method, $this->Key, 0, $this->IV, $Tag) : openssl_decrypt(base64_decode($needle), $this->Method, $this->Key, 0, $this->IV);
    }
    public function Tag(): string {
        return base64_encode($this->Method);
    }
}


class Response {
    private array $format;

    private int $status = 200;

    private ?array $headers = null;

    public string $contentType = 'application/json';



    public function __construct(array | stdClass $Format = [
        'response' => [
            'message' => '{{message}}',
            'status' => '{{status}}'
        ]
    ], ?array $Options = null) {
        $this->format = (array) $Format;

        if($Options !== null) {
            if(isset($Options['headers']) && gettype($Options['headers']) === 'array') $this->headers = $Options['headers'];
            if(isset($Options['status']) && gettype($Options['status']) === 'int') $this->status = $Options['status'];
        }
    }

    private function _Dash(string | array $Message, ?int $Status = null, ?array $Headers = null, bool $useFormat = true): void {
        if(gettype($Message) === 'string' && $useFormat) $Format = Basic::ReWalk($this->format, ['{{status}}' => $Status ? $Status : ($this->status ? $this->status : 402), '{{message}}' => $Message]);
        
        http_response_code($Status ?? $this->status);

        header(Basic::String('content-type: {}', $this->contentType));

        if ($this->headers) {
            if ($Headers !== null) {
                foreach(array_merge($this->headers, $Headers) as $Key => $Value) header(Basic::String('{}: {}', $Key, $Value));
            } else {
                foreach($this->headers as $Key => $Value) header(Basic::String('{}: {}', $Key, $Value));
            }
        } elseif($Headers !== null) {
            foreach($Headers as $Key => $Value) header(Basic::String('{}: {}', $Key, $Value));
        }


        die(
            isset($Format) ? json_encode($Format, 128|32|256) : (is_array($Message) ? json_encode($Message, 128|32|256) : $Message)
        );
    }

    public function Error(string $Message, ?int $Status = null, ?array $Headers = null): void {

         $Status = $Status ? ((int) $Status[0] === 4 ? $Status : 403) : 403;

         $this->_Dash($Message, $Status, $Headers);
    }

    public function Ok(string $Message, ?int $Status = null, ?array $Headers = null): void {
        $Status = $Status ? ((int) $Status[0] === 2 ? $Status : 200) : 200;

        $this->_Dash($Message, $Status, $Headers);
    }

    public function Response(array | string $Response, ?int $Status = null, ?array $Headers = null): void {
        $this->_Dash($Response, $Status, $Headers);
    }

    public function Redirect(string $Url, ?array $Headers = null): void {
        $this->_Dash('', 302, $Headers ? array_merge($Headers, ['location' => $Url]) : ['location' => $Url], false);
    }
    public function Format(array | stdClass $Format): self {
        $this->format = (array) $Format;
        return $this;
    }
}


class Headers {
    private array $Headers;
    function __construct() {
        $this->Headers = array_change_key_case(getallheaders());
    }
    public function get(): array {
        return (array) $this->Headers;
    }
    public function filter(array $Filter): self {
        $this->Headers = array_diff_key($this->Headers, array_change_key_case(array_fill_keys($Filter, null)));
        return $this;
    }
    public function replace(array $Replace): self {
        $this->Headers = array_replace($this->Headers, array_change_key_case($Replace));
        return $this;
    }
}
?>