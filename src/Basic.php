<?php

namespace Nickimbo\Utils;


class Basic {
    static public function string(string &$Str, array ...$Repl): string {
        return $Str;
    }
    static public function find(mixed $needle, array | object $haystack, int $type = 0): array {
        $haystack = (array) $haystack;
        if(!in_array($type, [0, 1])) return ['errors' => [
            'Function argument[2] (Type) is different than available options (0, 1)
            0 = String contains;
            1 = String matches exactly;'
        ]];
        $Ret = [
            'Key' => 0,
            'Value' => null,
            'Found' => false,
            'errors' => []
        ];
        if($type === 0) {
            array_walk(
                $haystack,
                function($Value, $Key) use(&$Ret, $needle): void {
                    if(in_array(gettype($Value), ['string', 'int', 'number'])) {
                        if(str_contains($Value, $needle) === true) {
                            $Ret['Key'] = $Key;
                            $Ret['Value'] = $Value;
                            $Ret['Found'] = true;
                        }
                    }
                }
            );
        }
        else {
            $retrieveKey = array_search($needle, $haystack);
            if(is_numeric($retrieveKey)) {
                $Ret['Key'] = $retrieveKey;
                $Ret['Value'] = $haystack[$retrieveKey];
                $Ret['Found'] = true;
            }
        }
        return $Ret;
    }
}

?>
