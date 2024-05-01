<?php

namespace Service\Filter;

use Service\Filter;

class SafeString
{

    /** @var Filter */
    protected $filter;

    public function __construct($filter)
    {
        $this->filter = $filter;
    }


    public function __invoke($data)
    {
        return $this->filter->sanitize($data, [
            Filter::FILTER_TRIM,
            Filter::FILTER_STRIPTAGS,
            Filter::FILTER_STRING,
        ]);
    }
}
