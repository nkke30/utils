<?php

namespace Nickimbo\Utils;

use \stdClass;

define('UTILS_CURRENT_URL', (
    ($_SERVER['REQUEST_SCHEME'] ?? 'http') .
    ('://') . 
    ($_SERVER['HTTP_HOST'] ?? 'localhost') . 
    ($_SERVER['REQUEST_URI'] ?? '/')
));


define('UTILS_CURRENT_HOST', (
    ($_SERVER['REQUEST_SCHEME'] ?? 'http') .
    ('://') . 
    ($_SERVER['HTTP_HOST'] ?? 'localhost') 
));


define('UTILS_CURRENT_HTTPS_HOST', ('https://' . ($_SERVER['HTTP_HOST'] ?? 'localhost')));

define('UTILS_CURRENT_HTTPS_URL', (
    ('https://').
    ($_SERVER['HTTP_HOST'] ?? 'localhost') . 
    ($_SERVER['REQUEST_URI'] ?? '/')
));

define('FALSE_PARSE', 0x4227);  

class Basic {
    public static function toSprintf(string $inputString, string $delimiter = '{}', string $escaper = '\\'): string {
        return strtr($inputString, [
            $escaper.$delimiter => $delimiter,
            $delimiter => '%s'
        ]);
    }
    static public function String(string|array $haystack, string|int|bool ...$Args): string|null {
        $Args = array_map('strval', $Args);
        if(gettype($haystack) === 'array') {
            if(!$haystack['String'] || gettype($haystack['String']) !== 'string') return null;
            $haystack['String'] = strtr($haystack['String'], [
                '%METHOD%' => $_SERVER['REQUEST_METHOD'] ?? 'local',
                '%URL%' => UTILS_CURRENT_URL,
                '%QUERY%' => $_SERVER['QUERY_STRING'],
                '%FILE%' => __FILE__,
                '%LINE%' => __LINE__,
                '%VERSION%' => phpversion(),
                '%HOST%' => UTILS_CURRENT_HOST
            ]);
            $String = self::toSprintf($haystack['String'], $haystack['Delimiter'] ?? '{}', $haystack['Escaper'] ?? '\\');
        }
        else {
            $String = self::toSprintf(strtr($haystack, [
                '%METHOD%' => $_SERVER['REQUEST_METHOD'] ?? 'local',
                '%URL%' => UTILS_CURRENT_URL,
                '%QUERY%' => $_SERVER['QUERY_STRING'] ?? '',
                '%FILE%' => __FILE__,
                '%LINE%' => __LINE__,
                '%VERSION%' => phpversion(),
                '%HOST%' => UTILS_CURRENT_HOST
            ]));
        }
        return vsprintf($String, $Args);
    }
    static public function AString(array $Args): string|null {
        return call_user_func_array(['Nickimbo\Utils\Basic', 'String'], $Args);
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
    static public function deepSearch(array $Array, mixed $Needle): array {
        $Arr = [];
        array_walk($Array, function($Value, $Key) use(&$Arr, $Needle) {
            if ($Value == $Needle) $Arr[] = $Key;
        });

        return $Arr;
    }
    static public function Url(
        string | int | array ...$Args): string {
        if (sizeof($Args) < 1) return UTILS_CURRENT_URL;

        if (gettype($Args[0]) === 'array') {
            return UTILS_CURRENT_URL . '?' . http_build_query($Args[0]);
        } else {
            if(isset($Args[1])) {
                if(gettype($Args[1]) === 'array') {
                    if(!isset($Args[2])) return $Args[0] . '?' . http_build_query($Args[1]);
                    else return vsprintf(self::toSprintf($Args[0]), $Args[1]) . '?' . http_build_query($Args[2]);
                }
            }
        }
        return $Args[0];
    }
    static public function Parse(mixed $haystack, ?string $forceType = 'json', ?int $Type = 1): mixed {
        $T = in_array($forceType, ['json', 'object', 'boolean', 'integer', 'double', 'string', 'array', 'null', 'unknown', 'domain']) ? ($forceType === 'json' ? 'object' : ($forceType === 'unknown' ? 'unknown type' : $forceType)) : 'object';
        $O = strtolower(strval(gettype($haystack)));
        if($T === $O) return $haystack;

        switch($T):
            case 'object':
                if($forceType === 'json') return json_validate($haystack) ? json_decode($haystack, $Type ?? 1) : FALSE_PARSE;
                return (object)$haystack;
            case 'boolean':
                return boolval($haystack);
            case 'integer':
                return intval($haystack);
            case 'double':
                return doubleval($haystack);
            case 'string':
                return is_array($haystack) || is_object($haystack) ? json_encode($haystack) : strval($haystack);
            case 'array':
                return (array)$haystack;
            case 'null':
                return is_null($haystack) ? $haystack : FALSE_PARSE;
            case 'domain':
                $Host = parse_url($haystack, PHP_URL_HOST);
                switch (true) {
                    case substr_count($Host, '.') === 2:
                        return explode('.', $Host, 2)[1];
                    case substr_count($Host, '.') > 2:
                        return self::Parse('https://' . explode('.', $Host, 2)[1], 'domain');
                    case substr_count($Host, '.') < 2:
                        return $Host;
                }
            case 'unknown type':
                return 'unknown';
        endswitch;
        return $haystack;
    }
    static public function RequireMulti(string ...$Args): array {
        $metaData = [
            'skipped' => [],
            'required' => [],
            'invalid_dir' => []
        ];
        $functionArgs = array_filter($Args, function($o, $a) use(&$metaData) {
            $isValid = str_ends_with($o, '.php');
            if(!($isValid === true)) array_push($metaData['skipped'], [
                'argValue' => $o,
                'argPlace' => $a
            ]);
            return $isValid;
        }, ARRAY_FILTER_USE_BOTH);
        foreach($functionArgs as $currentKey => $currentArg) {
            if(!file_exists($currentArg)) {
                array_push($metaData['invalid_dir'], [
                    'argValue' => $currentArg,
                    'argPlace' => $currentKey
                ]);
                continue;
            }
            require_once($currentArg);
            array_push($metaData['required'], [
                'argValue' => $currentArg,
                'argPlace' => $currentKey
            ]);
        }
        return $metaData;
    }
    static public function RequireAll(string ...$Args): array {
        $metaData = [
            'skipped' => [
                'directories' => [],
                'files' => [] 
            ],
            'required' => []
        ];
        foreach ($Args as $currentDir) {
            if(!realpath($currentDir) OR !is_dir($currentDir)) {
                $metaData['skipped']['directories'][] = [
                    'Path' => $currentDir,
                    'Reason' => 'Path is not a directory or does not exist.'
                ];
                continue;
            };
            $GD = array_diff(scandir($currentDir), ['.', '..']);
            if (sizeof($GD) === 0) {
                $metaData['skipped']['directories'][] = [
                    'Path' => $currentDir,
                    'Reason' => 'No files detected in directory'
                ];
                continue;
            }
            $GD = array_filter($GD, function($File) use(&$metaData) {
                if(!str_ends_with($File, '.php')) {
                    $metaData['skipped']['files'] = [
                        'File' => $File,
                        'Reason' => 'File is not an PHP File.' 
                    ];
                    return false;
                }
                return true;
            });
            foreach ($GD as $currentFile) {
                require($currentDir . $currentFile);
                $metaData['required'][] = $currentDir . $currentFile;
            }
        }
        return $metaData;
    }
    static public function LoadJSON(string $File, ?int $Type = 1): array | stdClass {
        return self::Parse(file_get_contents($File), 'json', $Type);
    }
    static public function ReLize(array $Array, array | string $Needle, array | string $Replace): array {
        $Serialized = serialize($Array);
        $Needles = [];
        $Replaces = [];
        $Unserialized = [];
        if (gettype($Needle) === 'array') {
            $T = gettype($Replace);
            foreach($Needle as $Key => $Value) {

                $Needles[] = self::String('s:{}:"{}"', strlen($Value), $Value);

                if ($T === 'array') {
                    if(isset($Replace[$Key])) $Replaces[$Key] = self::String('s:{}:"{}"', strlen($Replace[$Key]), $Replace[$Key]);
                    else $Replaces[$Key] = 's:4:"null"';
                } else {
                    $Replaces[$Key] = self::String('s:{}:"{}"', strlen($Replace), $Replace);
                } 
            }
        } else {
            $Needles[] = self::String('s:{}:"{}"', strlen($Replace), $Replace);
            $Replaces[] = self::String('s:{}:"{}"', strlen($Replace), $Replace);
        }

        $Unserialized = unserialize(str_replace($Needles, $Replaces, $Serialized));


        return $Unserialized;
    }
    static public function ReWalk(array $mainArray, array $Replacements): array {
        array_walk_recursive($mainArray, function (&$value) use ($Replacements) {
            $value = strtr($value, $Replacements);
        });
        return $mainArray;
    }
    static public function LoadHTML(string $Path, ?array $Replacements = null): string {

        $renderHTML = file_get_contents($Path);

        if ($Replacements !== null) return \preg_replace_callback('/(?<!\\\\)\{\{(\d+)\}\}/', fn ($Match) => $Replacements[$Match[1]], $renderHTML);

        return $renderHTML;
    }
}
?>
