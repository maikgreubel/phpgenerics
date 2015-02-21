<?php

namespace Generics\Tests;

use Generics\Util\Directory;
use Generics\Util\RandomString;

class DirectoryTest extends \PHPUnit_Framework_TestCase
{
    public function testCreateAndRemoveSimple()
    {
        $tempDirName = RandomString::generate(8, RandomString::ASCII);
        $dir = new Directory(getcwd() . "/$tempDirName");
        $dir->create();
        $dir->remove();
        $this->assertFalse($dir->exists());
    }

    public function testCreateAndRemoveRecursive()
    {
        $tempDirName = RandomString::generate(8, RandomString::ASCII);
        $dir = new Directory(getcwd() . $tempDirName . '/subdir1/subdir2');
        $dir->create(true);

        $tempFileName = tempnam($dir->getPath(), 'txt');
        $fd = fopen($tempFileName, "w");
        fclose($fd);

        $dir2 = new Directory(getcwd() . $tempDirName);
        $dir2->remove(true);

        $this->assertFalse($dir2->exists());
    }
}
