<?php

namespace Nickimbo\Utils\Validator\Exceptions;


final class GlobalError extends \Exception {

    public function __construct(string $Message) {
        parent::__construct($Message);
    }
}


?>