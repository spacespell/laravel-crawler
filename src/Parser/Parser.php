<?php

namespace SpaceSpell\LaravelCrawler\Parser;

abstract class Parser implements ParserInterface
{
    protected $context;

    protected $scope;

    public function __construct($scope)
    {
        $this->scope = $scope;
    }

    public function setContext(array $context)
    {
        $this->context = $context;
    }

    public function parse($response)
    {
        return;
    }
}
