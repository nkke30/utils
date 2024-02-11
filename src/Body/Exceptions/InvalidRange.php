<?php

namespace Nickimbo\Utils\Validator\Exceptions;


final class Range extends \Exception {

    private const MESSAGE = 'Invalid range array provided. Requires [int, int] got: %s';


    public function __construct(array $Range) {

        $Map = array_map('gettype', $Range);

        parent::__construct(sprintf(self::MESSAGE, '[' . implode(', ', $Map) . ']'));
    }
}


?>