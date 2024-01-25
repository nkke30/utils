<?php


namespace Nickimbo\Utils;


use Nickimbo\Utils\Basic;

!defined('UTILS_CURRENT_URL') && define('UTILS_CURRENT_URL', (
    ($_SERVER['REQUEST_SCHEME'] ?? 'http') .
    ('://') . 
    ($_SERVER['HTTP_HOST'] ?? 'localhost') . 
    ($_SERVER['REQUEST_URI'] ?? '/')
));

class Request {

    public $parsedUrl;

    public $method;

    public $path;

    public $body;

    public $headers;

    public $scheme;

    public $host;

    public $hash;

    public $query;

    public function __construct() {
        $this->parsedUrl = parse_url(UTILS_CURRENT_URL);
        $this->method = $_SERVER['REQUEST_METHOD'] ?? 'local';
        $this->path = $this->parsedUrl['path'];
        $this->body = $_POST ?? null;
        $this->headers = array_map('strtolower', getallheaders()) ?? [];
        $this->scheme = $this->parsedUrl['scheme'];
        $this->host = $this->parsedUrl['host'];
        $this->hash = $this->parsedUrl['fragment'];
        $this->query = $this->parsedUrl['query'];
    }

    public function filterHeaders(array $needle): self::headers {
        return array_diff($this->headers, $headers);
    }
}


?>