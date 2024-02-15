<?php

namespace Nickimbo\Utils\Validator\Exceptions;


final class InvalidMethod extends \Exception {

    private const MESSAGE = 'Invalid method (`%s`) provided. Available options: %s';

    public function __construct(string $Method) {
        parent::__construct(sprintf(self::MESSAGE, $Method, 'GET, POST, PUT, PATCH, OPTIONS, CONNECT, TRACE, HEAD, ANY'));
    }
}


?>