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

        $this->convertValues(
            $target,
        );

        return $target;
    }

    public function toArray(): array
    {
        $output = [];

        $this->convertValues(
            $output,
        );

        return $output;
    }

    private function convertValues(
        object|array &$output,
    ): void {
        if (\is_object($output)) {
            $targetReflection = new \ReflectionObject($output);
        }

        foreach ($this->getPropertyList($this->input) as $property) {
            if ($this->isPerformableActor($property, $this->input)) {
                continue;
            }

            $this->updateAccessibility($property);

            if (\is_array($output)) {
                $output[$property->getName()] = $property->getValue($this->input);

                continue;
            }

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
        $properties = (new \ReflectionObject($input))->getProperties();

        if (null !== ($parent = (new \ReflectionObject($input))->getParentClass())) {
            $properties = \array_merge($properties, $parent->getProperties());
        }

        return $properties;
    }
}
