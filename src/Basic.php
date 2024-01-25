<?php

namespace Nickimbo\Utils;

define('UTILS_CURRENT_URL', (
    ($_SERVER['REQUEST_SCHEME'] ?? 'http') .
    ('://') . 
    ($_SERVER['HTTP_HOST'] ?? 'localhost') . 
    ($_SERVER['REQUEST_URI'] ?? '/')
));
function castString($delimiter,$escaper,$text){$d=preg_quote($delimiter,"~");$e=preg_quote($escaper,"~");$tokens=preg_split('~'.$e.'('.$e.'|'.$d.')(*SKIP)(*FAIL)|(?<='.$d.')~',$text,-1,PREG_SPLIT_NO_EMPTY);$escaperReplacement=str_replace(['\\','$'],['\\\\','\\$'],$escaper);$delimiterReplacement=str_replace(['\\','$'],['\\\\','\\$'],$delimiter);return implode(preg_replace(['~\\\\.(*SKIP)(*FAIL)|'.($escaper.$delimiter).'~s','~'.$e.$d.'~'],['%s',$delimiterReplacement],$tokens));}
class Basic {
    static public function String(string|array $haystack, string|number|bool ...$Args): string|null {
        $Args = array_map('strval', $Args);
        if(gettype($haystack) === 'array') {
            if(!$haystack['String'] || gettype($haystack['String']) !== 'string') return null;
            $Str = castString($haystack['Delimiter'] ?? '{}', $haystack['Escaper'] ?? '\\', $haystack['String']);
        }
        else {
            $Str = castString('{}', '\\', $haystack);
        }
        return vsprintf($Str, $Args);
    }
    static public function Find(mixed $needle, array | object $haystack, int $type = 0): array {
        $haystack = (array) $haystack;
        if(!in_array($type, [0, 1])) return ['errors' => [
            'Function argument[2] (Type) is different than available options (0, 1)
            0 = String contains;
            1 = String matches;'
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
    static public function Url(
        string | number | array ...$Args): string | UTILS_CURRENT_URL {
        if (sizeof($Args) < 1) return UTILS_CURRENT_URL;

        if (gettype($Args[0]) === 'array') {
            return UTILS_CURRENT_URL . '?' . http_build_query($Args[0]);
        } else {
            if(isset($Args[1])) {
                if(gettype($Args[1]) === 'array') {
                    if(!isset($Args[2])) return $Args[0] . '?' . http_build_query($Args[1]);
                    else return vsprintf(castString('{}', '\\', $Args[0]), $Args[1]) . '?' . http_build_query($Args[2]);
                }
            }
        }
        return $Args[0];
    }
    static public function Parse(mixed $haystack, string $forceType = 'json'): array {
        $T = in_array($forceType, ['json', 'object', 'boolean', 'integer', 'double', 'string', 'array', 'object', 'null', 'unknown']) ? ($forceType === 'json' ? 'object' : $forceType) : 'object';
        $P;
        switch($T):
            case 'object':
            if($forceType === 'json') {
                
            }
            break;
        endswitch;
    }
}
?>
