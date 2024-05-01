<?php

namespace Service;


use Service\Filter\FilterFactory;
use Service\Filter\SafeString;

class Filter extends \Phalcon\Filter\Filter
{
    const FILTER_SAFE_STRING = "safestring";

    public function __construct(array $mapper = [])
    {
        parent::__construct($mapper);

        $this->set(self::FILTER_SAFE_STRING, new SafeString($this));
    }
}
