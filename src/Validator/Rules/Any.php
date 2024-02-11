<?php

namespace Nickimbo\Utils\Validator\Rules;

use Nickimbo\Utils\Validator\Interfaces\RuleInterface;

class Any implements RuleInterface {

    private array $rules;

    public function __construct(RuleInterface ...$Rules) {
        $this->rules = $Rules;
    }

    public function run($needle, string $field): bool {
        
        $validCount = 0;

        foreach($this->rules as $Rule) {
            if($Rule->run($needle, $field)) $validCount += 1;
        }

        return $validCount > 0;
    }
}




?>