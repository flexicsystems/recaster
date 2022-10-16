<?php

declare(strict_types=1);

/**
 * Copyright (c) 2022-2022 ThemePoint
 *
 * @author Hendrik Legge <hendrik.legge@themepoint.de>
 *
 * @version 1.0.0
 */

namespace Flexic\Recaster;

final class Recaster
{
    public function __construct(
        readonly private object $input,
    ) {
    }

    public function toClass(
        object|string $target,
    ): object {
        if (\is_string($target) && !\class_exists($target)) {
            throw new \RuntimeException('Target class to recast does not exist');
        }

        if (\is_string($target)) {
            $target = new $target();
        }

        $this->convertToClass(
            $target,
        );

        return $target;
    }

    public function toArray(): array
    {
        $output = [];

        $this->convertToArray(
            $output,
        );

        return $output;
    }

    private function convertToArray(
        array &$output,
    ): void {
        foreach ($this->getPropertyList($this->input) as $property) {
            if ($this->isPerformableActor($property, $this->input)) {
                continue;
            }

            $this->updateAccessibility($property);

            $output[$property->getName()] = $property->getValue($this->input);
        }
    }

    private function convertToClass(
        object &$output,
    ): void {
        $targetReflection = new \ReflectionObject($output);

        foreach ($this->getPropertyList($this->input) as $property) {
            if ($this->isPerformableActor($property, $this->input)) {
                continue;
            }

            $this->updateAccessibility($property);

            if (!$targetReflection->hasProperty($property->getName())) {
                continue;
            }

            $targetProperty = $targetReflection->getProperty($property->getName());

            $this->updateAccessibility($targetProperty);

            $targetProperty->setValue(
                $output,
                $property->getValue($this->input),
            );
        }
    }

    private function isPerformableActor(
        \ReflectionProperty $property,
        object $input,
    ): bool {
        if (!$property->isInitialized($input) || !$property->isStatic()) {
            return false;
        }

        return true;
    }

    private function updateAccessibility(\ReflectionProperty $property): void
    {
        if ($property->isProtected() || $property->isPrivate()) {
            $property->setAccessible(true);
        }
    }

    private function getPropertyList(object $input): array
    {
        $reflection = (new \ReflectionObject($input));
        $properties = $reflection->getProperties();

        $parentClass = $reflection->getParentClass();

        while ($parentClass instanceof \ReflectionClass) {
            $properties = \array_merge($properties, $parentClass->getProperties());

            $parentClass = $parentClass->getParentClass();
        }

        return $properties;
    }
}
