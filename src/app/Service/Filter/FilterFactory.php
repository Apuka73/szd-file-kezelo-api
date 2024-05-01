<?php
namespace Service\Filter;

use Phalcon\Filter\FilterInterface;
use Service\Filter;

class FilterFactory extends \Phalcon\Filter\FilterFactory
{
    public function newInstance(): FilterInterface
    {
        return new Filter($this->getServices());
    }

}
