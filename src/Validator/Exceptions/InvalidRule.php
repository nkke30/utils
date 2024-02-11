<?php

namespace Nickimbo\Utils\Validator\Exceptions;


final class InvalidRule extends \Exception {

    private const MESSAGE = 'Invalid Rule(s) provided: %s';


    public function __construct(array $Rules) {

        $Map = array_map('gettype', $Rules);

        parent::__construct(sprintf(self::MESSAGE, '[' . implode(', ', $Map) . ']'));
    }
}


?>