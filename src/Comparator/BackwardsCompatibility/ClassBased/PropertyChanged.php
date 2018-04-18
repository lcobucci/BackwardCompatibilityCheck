<?php

declare(strict_types=1);

namespace Roave\ApiCompare\Comparator\BackwardsCompatibility\ClassBased;

use Roave\ApiCompare\Changes;
use Roave\ApiCompare\Comparator\BackwardsCompatibility\PropertyBased\PropertyBased;
use Roave\BetterReflection\Reflection\ReflectionClass;

final class PropertyChanged implements ClassBased
{
    /**
     * @var PropertyBased
     */
    private $checkProperty;

    public function __construct(PropertyBased $checkProperty)
    {
        $this->checkProperty = $checkProperty;
    }

    public function compare(ReflectionClass $fromClass, ReflectionClass $toClass) : Changes
    {
        $propertiesFrom   = $fromClass->getProperties();
        $propertiesTo     = $toClass->getProperties();
        $commonProperties = array_intersect_key($propertiesFrom, $propertiesTo);

        return array_reduce(
            array_keys($commonProperties),
            function (Changes $accumulator, string $propertyName) use ($propertiesFrom, $propertiesTo) : Changes {
                return $accumulator->mergeWith($this->checkProperty->compare(
                    $propertiesFrom[$propertyName],
                    $propertiesTo[$propertyName])
                );
            },
            Changes::new()
        );
    }
}
