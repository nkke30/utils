<?php

namespace Nickimbo\Utils\Validator\Rules;


use Nickimbo\Utils\Validator\Interfaces\RuleInterface;
use Nickimbo\Utils\Validator\Exceptions;

class Atleast implements RuleInterface {

    private array $rules;

    private int $count;

    public function __construct(array $Rules, int $rulesCount) {
        $invalidRules = [];
        array_map(function($Rule) use(&$invalidRules) {
            if(!($Rule instanceof RuleInterface)) $invalidRules[] = $Rule;
        }, $Rules);

        if(sizeof($invalidRules) > 0) throw new Exceptions\InvalidRule($invalidRules);

        $this->rules = $Rules;
        $this->count = $rulesCount;
    }

    public function run($needle, $field): bool {
        
        $validCount = 0;

        foreach($this->rules as $Rule) {
            if($Rule->run($needle, $field)) $validCount += 1;
        }

        return $this->count >= $validCount;
    }
}


?>