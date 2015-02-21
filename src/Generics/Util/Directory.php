<?php
namespace Generics\Util;

use Generics\DirectoryException;

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
        $this->path = $path;
    }

    /**
     * Checks whether directory is empty or not
     *
     * @return boolean
     *
     * @throws DirectoryException
     */
    public function isEmpty()
    {
        if (! $this->exists()) {
            throw new DirectoryException("Directory {dir} does not exist", array(
                'dir' => $this->path
            ));
        }

        $iter = new \DirectoryIterator($this->path);
        while ($iter->valid()) {
            if ($iter->isDot()) {
                $iter->next();
            } else {
                return false;
            }
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
        if (mkdir($this->path, $mode, $recursive) === false) {
            throw new DirectoryException("Could not create the directory {dir}", array(
                'dir' => $this->path
            ));
        }
    }

    /**
     * Checks whether directory exists
     *
     * @throws DirectoryException
     * @return boolean
     */
    public function exists()
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
    public function getPath()
    {
        return $this->path;
    }
}
