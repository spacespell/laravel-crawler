<?php

namespace SpaceSpell\LaravelCrawler\Scope;

abstract class Scope implements ScopeInterface
{
    public function method()
    {
        return $this->method ?? config('laravelcrawler.default_method', 'GET');
    }

    public function headers()
    {
        return array_merge($this->headers, config('laravelcrawler.default_headers', []));
    }

    public function timeout()
    {
        return $this->timeout ?? config('laravelcrawler.default_timeout', 30);
    }

    public function parseQueueName()
    {
        return $this->parseQueueName ?? config('laravelcrawler.default_parse_queue', 'parse');
    }

    public function parser()
    {
        return $this->parser ?? null;
    }
}
