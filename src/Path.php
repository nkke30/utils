<?php
declare(strict_types=1);



namespace Nickimbo\Utils;


interface IPath {

    public function file(string $fileName, string $targetDir): self;

    public function dir(string $dirName, string $targetDir): self;

    public function collect(?string $type): ?string;

}


class Path implements IPath {

    private string $rootDir = __DIR__;

    private string $resultDir;

    private string $resultFile;

    public function construct(?string $rootDir = null): void {
        if ($rootDir !== null) $this->rootDir = $rootDir;
    }

    public function file(string $name, string $dirName = ''): self {
        $rootDir = '/';
        $foundFile = null;

        $dirs = array_values(array_filter(explode(DIRECTORY_SEPARATOR, $this->match($this->rootDir, $dirName))));
    
        foreach ($dirs as $dir) {

            $currentDir = $rootDir . $dir . DIRECTORY_SEPARATOR;
    
            $scanDir = array_values(array_diff(scandir($currentDir), ['.', '..']));
    
            if (in_array($name, $scanDir)) {
                $foundFile = $currentDir . $name;
                break; 
            }
    

            $rootDir = $currentDir;
        }

        $this->resultFile = $foundFile;

        return $this;
    }

    public function dir(string $dirName, string $targetDirName = ''): self {
        $rootDir = '/';
        $foundDir = null;

        $dirs = array_values(array_filter(explode(DIRECTORY_SEPARATOR, $this->match($this->rootDir, $targetDirName))));


        if($dirName[-1] !== '/') $dirName .= '/';
        if($dirName[0] !== '/') $dirName = '/' . $dirName;

        foreach ($dirs as $dir) {

            $currentDir = $rootDir . $dir;

            if (is_dir($currentDir . $dirName)) {
                $foundDir = $currentDir . $dirName;
                break; 
            } elseif($currentDir == $dirName) {
                $foundDir = $currentDir;
                break;
            }
    
            $rootDir = $currentDir;
        }

        $this->resultDir = $foundDir;
    
        return $this;
    }

    public function collect(?string $type = null): ?string {
        switch($type) {
            case 'dir':
                return $this->resultDir;
            case 'file':
                return $this->resultFile;
            default:
                return $this->resultFile;
        }
    }

    private function match(string $haystack, string $needle): string {
        $Pos = strrpos($haystack, $needle);

        if ($Pos !== false) return substr($haystack, 0, $Pos + strlen($needle));
        else return $haystack;
    }

}


?>