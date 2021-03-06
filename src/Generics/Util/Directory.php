<?php
/**
 * This file is part of the PHP Generics package.
 *
 * @package Generics
 */
namespace Generics\Util;

use Generics\DirectoryException;

/**
 * This utility class helps on acting on directories
 *
 * @author Maik Greubel <greubel@nkey.de>
 */
class Directory
{

    /**
     * The absolute path of directory
     *
     * @var string
     */
    private $path;

    /**
     * Create new directory object
     *
     * @param string $path
     */
    public function __construct($path)
    {
        $this->path = $this->fixDirectorySeparator($path);
        if ($this->exists()) {
            $this->path = realpath($this->path);
        }
    }

    /**
     * Checks whether directory is empty or not
     *
     * @param string $filter
     *            The filter for entries to skip
     *
     * @return bool
     *
     * @throws DirectoryException
     */
    public function isEmpty($filter = null): bool
    {
        if (! $this->exists()) {
            throw new DirectoryException("Directory {dir} does not exist", array(
                'dir' => $this->path
            ));
        }
        
        $iter = new \DirectoryIterator($this->path);
        while ($iter->valid()) {
            if (! $iter->isDot() && ($filter === null || ! preg_match("/$filter/", $iter->getFilename()))) {
                return false;
            }
            $iter->next();
        }
        
        return true;
    }

    /**
     * Remove a directory
     *
     * @param boolean $recursive
     *            Whether to remove it if its not empty
     *
     * @throws DirectoryException
     */
    public function remove($recursive = false)
    {
        if (! $this->exists()) {
            return;
        }
        
        if ($this->isEmpty()) {
            if (rmdir($this->path) === false) {
                throw new DirectoryException("Could not remove directory {dir}", array(
                    'dir' => $this->path
                ));
            }
            return;
        }
        
        if (! $recursive) {
            throw new DirectoryException("Directory {dir} is not empty", array(
                'dir' => $this->path
            ));
        }
        
        $iter = new \DirectoryIterator($this->path);
        while ($iter->valid()) {
            if ($iter->isDot()) {
                $iter->next();
                continue;
            }
            
            if ($iter->isDir()) {
                $dir = new Directory($iter->getPathname());
                $dir->remove(true);
            } else {
                unlink($iter->getPathname());
            }
            
            $iter->next();
        }
        rmdir($this->path);
    }

    /**
     * Create a directory
     *
     * @param boolean $recursive
     *            Create also sub directories
     *
     * @throws DirectoryException
     */
    public function create($recursive = false, $mode = 0755)
    {
        if ($this->exists()) {
            throw new DirectoryException("Directory {dir} already exists", array(
                'dir' => $this->path
            ));
        }
        
        if (mkdir($this->path, $mode, $recursive) === false) {
            throw new DirectoryException("Could not create the directory {dir}", array(
                'dir' => $this->path
            ));
        }
        $this->path = realpath($this->path);
    }

    /**
     * Checks whether directory exists
     *
     * @throws DirectoryException
     * @return bool
     */
    public function exists(): bool
    {
        if (! file_exists($this->path)) {
            return false;
        }
        
        if (! is_dir($this->path)) {
            throw new DirectoryException("Entry {path} exists, but it is not a directory!", array(
                'path' => $this->path
            ));
        }
        
        return true;
    }

    /**
     * Retrieve the path
     *
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * Check whether a particular file exist in directory
     *
     * @param string $fileName
     *            The file name to check
     *
     * @throws DirectoryException
     *
     * @return bool
     */
    public function fileExists($fileName): bool
    {
        if (! $this->exists()) {
            return false;
        }
        
        $file = sprintf("%s/%s", $this->path, $fileName);
        
        return file_exists($file);
    }

    /**
     * Generate a platform specific path by replacing invalid directory separators
     *
     * @param string $path
     *            The path to check
     * @return string The corrected path
     */
    private function fixDirectorySeparator($path): string
    {
        $path = str_replace("\\", DIRECTORY_SEPARATOR, $path);
        $path = str_replace("/", DIRECTORY_SEPARATOR, $path);
        
        return $path;
    }
}
