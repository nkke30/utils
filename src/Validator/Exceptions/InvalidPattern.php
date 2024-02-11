<?php

namespace Nickimbo\Utils\Validator\Exceptions;


final class InvalidPattern extends \Exception {

    private const MESSAGE = 'Invalid pattern (`%s`) provided';


    public function __construct(string $Pattern) {
        parent::__construct(sprintf(self::MESSAGE, $Pattern));
    }
}


?>