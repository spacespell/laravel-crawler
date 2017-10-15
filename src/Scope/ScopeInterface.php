<?php

namespace SpaceSpell\LaravelCrawler\Scope;

interface ScopeInterface
{
    public function headers();

    public function timeout();

    public function method();

    public function parseQueueName();
}
