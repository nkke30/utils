<?php
declare(strict_types=1);


namespace Nickimbo\Utils;

use stdClass;

class Stringer {
    private string $Key;
    
    private string $IV;

    private string $Method;

    function __construct(string | array | stdClass $Config)  {
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

?>