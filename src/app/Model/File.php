<?php

namespace Model;

use Model\AbstractModel;

class File extends AbstractModel
{
    protected $name;
    protected $path;

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     * @return File
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @param mixed $path
     * @return File
     */
    public function setPath($path)
    {
        $this->path = $path;
        return $this;
    }

    public function toResponseArray()
    {
        return array_merge(
            $this->toArray(),
            ['url' => $this->getPublicUrl()]
        );
    }

    public function getPublicUrl(): string
    {
        return $this->getDI()->getShared('storage')->getBucket()->publicUrl($this->path);
    }

}