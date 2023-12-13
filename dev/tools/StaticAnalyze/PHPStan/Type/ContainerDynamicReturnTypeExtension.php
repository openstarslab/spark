<?php

namespace Spark\Dev\StaticAnalyze\PHPStan\Type;

use PhpParser\Node\Expr\MethodCall;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Type\DynamicMethodReturnTypeExtension;
use PHPStan\Type\MixedType;
use PHPStan\Type\ObjectType;
use PHPStan\Type\TypeCombinator;
use Spark\Framework\Container\ContainerInterface;

class ContainerDynamicReturnTypeExtension implements DynamicMethodReturnTypeExtension
{

    public function getClass(): string
    {
        return ContainerInterface::class;
    }

    public function isMethodSupported(MethodReflection $methodReflection): bool
    {
        return \in_array($methodReflection->getName(), [
            'get',
            'make'
        ], true);
    }

    public function getTypeFromMethodCall(MethodReflection $methodReflection, MethodCall $methodCall, Scope $scope): ?\PHPStan\Type\Type
    {
        $argType = $scope->getType($methodCall->getArgs()[0]->value);

        $types = [];
        foreach ($argType->getConstantStrings() as $constantString) {
            $type = new ObjectType($constantString->getValue());

            if ($methodReflection->getName() === 'get' && \count($methodCall->getArgs()) >= 2) {
                $behaviorValue = $scope->getType($methodCall->getArgs()[1]->value);

                if ($behaviorValue->getValue() == ContainerInterface::NULL_ON_INVALID_REFERENCE) {
                    $type = TypeCombinator::addNull($type);
                }
            }

            $types[] = $type;
        }

        return TypeCombinator::union(...$types);
    }
}