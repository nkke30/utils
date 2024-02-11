<?php

namespace Nickimbo\Utils\Validator\Rules;


use Nickimbo\Utils\Validator\Interfaces\RuleInterface;

class Custom implements RuleInterface {

    private RuleInterface $call;

    public function __construct(RuleInterface $run) {
        $this->call = $run;
    }

    public function run($needle, $field): bool {
        return $this->call->run($needle, $field);
    }
}
?>