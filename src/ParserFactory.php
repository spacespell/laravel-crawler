<?php

namespace SpaceSpell\LaravelCrawler;

class ParserFactory
{
    public static function make($scopeName)
    {
        $definition = new \ReflectionClass($scopeName);
        $scope = $definition->newInstance();

        $parserClass = $scope->parser() ?? str_replace("\\Scope\\", "\\Parser\\", $scopeName);
        $parserDefinition = new \ReflectionClass($parserClass);

        if ($parserDefinition->isInstantiable()) {
            return $parserDefinition->newInstance([$scope]);
        } else {
            throw new \Exception("No parser found for scope {$scopeName}, expect {$class}");
        }
    }
}
