<?php

namespace Nickimbo\Utils\Validator;


class BodyValidator {
    private ?array $errors = null;

    private array $body;

    private bool $isValid = false;

    private array $fields;

    private array $rules;

    public function __construct(array $Body) {

        $this->body = $Body;

    }

    public function setFields(string ...$Fields): self {
        $this->fields = $Fields;
        return $this;
    }

    public function setRules(Rules\Rule ...$Rules): self {
        $this->rules = $Rules;
        return $this;
    }

    public function run(): void {

        if(!is_array($this->errors)) $this->errors = array();

        $needCount = 0;

        $validCount = 0;

        foreach ($this->fields as $Key => $Field):

            $optionalField = str_starts_with($Field, '?');
            $inBody = in_array($Field, $this->body);

            if($optionalField === false) $needCount++;


            if(!$inBody && $optionalField === false) {
                $this->errors[] = [
                    'field' => $Field,
                    'required' => true,
                    'reason' => 'Field does not exist in body, even tho it is required.',
                    'value' => null,
                    'rule' => isset($this->rules[$Key]) ? $this->rules[$Key] : null
                ];
                continue;
            }

            if(!isset($this->rules[$Key])) {
                $this->errors[] = [
                    'field' => $Field,
                    'required' => !$optionalField,
                    'reason' => 'Field\'s matching rule has not been found. perhaps missed?',
                    'value' => $inBody ? $this->body[$Field] : null,
                    'rule' => null
                ];
                continue;
            }

            if($inBody === true) {

                if($this->rules[$Key]->run($this->body[$Field]) === false) {
                    $this->errors[] = [
                        'field' => $Field,
                        'required' => !$optionalField,
                        'reason' => 'Field failed passing check.',
                        'value' => $this->body[$Field],
                        'rule' => $this->rules[$Key]
                    ];
                    continue;
                }
                
                $validCount++;

            }

        endforeach;

        $this->isValid = $needCount === $validCount;

    }

    public function errors(): ?array {
        return $this->errors;
    }
    public function isValid(): bool {
        return $this->isValid;
    }
}



?>