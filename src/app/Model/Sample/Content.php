<?php

namespace Model\Sample;

use Model\AbstractModel;

class Content extends AbstractModel
{
    protected $name;

    protected $content;

    public function initialize()
    {
        // tabla neve
        $this->setSource('sample_content');
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getContent()
    {
        return $this->content;
    }

    public function setContent($content)
    {
        $this->content = $content;
    }
}
