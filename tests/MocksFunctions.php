<?php

declare(strict_types=1);

namespace Ramsey\Uuid\Test;

use Closure;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\Attributes\After;
use Throwable;
use phpmock\Mock;
use phpmock\MockBuilder;

use function array_values;
use function count;
use function implode;
use function var_export;

/**
 * Mocks global functions within a namespace using php-mock
 *
 * Each expectation is a single call to the function and must occur exactly once. Calls may occur in any order.
 *
 * @mixin Assert
 */
trait MocksFunctions
{
    /**
     * @var array<string, Mock>
     */
    private array $functionMocks = [];

    /**
     * @var array<string, list<array{args: list<mixed>|null, result: mixed, called: bool}>>
     */
    private array $functionExpectations = [];

    /**
     * @param list<mixed>|null $args Arguments the call must have, or null to allow any; a Closure argument is used
     *     as a predicate for the actual argument
     * @param mixed $result The value to return, or a Throwable to throw
     */
    protected function expectFunctionCall(string $namespace, string $function, ?array $args, mixed $result): void
    {
        $key = $namespace . '\\' . $function;

        $this->functionExpectations[$key][] = ['args' => $args, 'result' => $result, 'called' => false];

        if (isset($this->functionMocks[$key])) {
            return;
        }

        $mock = (new MockBuilder())
            ->setNamespace($namespace)
            ->setName($function)
            ->setFunction(function (mixed ...$actual) use ($key): mixed {
                return $this->dispatchFunctionCall($key, array_values($actual));
            })
            ->build();

        $mock->enable();

        $this->functionMocks[$key] = $mock;
    }

    #[After]
    protected function verifyFunctionMocks(): void
    {
        foreach ($this->functionMocks as $mock) {
            $mock->disable();
        }

        $expectations = $this->functionExpectations;
        $this->functionMocks = [];
        $this->functionExpectations = [];

        foreach ($expectations as $key => $calls) {
            foreach ($calls as $call) {
                Assert::assertTrue(
                    $call['called'],
                    'Expected a call to ' . $key . '(' . $this->describeArguments($call['args']) . ')',
                );
            }
        }
    }

    /**
     * @param list<mixed> $actual
     */
    private function dispatchFunctionCall(string $key, array $actual): mixed
    {
        foreach ($this->functionExpectations[$key] as $index => $call) {
            if ($call['called'] || !$this->argumentsMatch($call['args'], $actual)) {
                continue;
            }

            $this->functionExpectations[$key][$index]['called'] = true;

            if ($call['result'] instanceof Throwable) {
                throw $call['result'];
            }

            return $call['result'];
        }

        Assert::fail('Unexpected call to ' . $key . '(' . $this->describeArguments($actual) . ')');
    }

    /**
     * @param list<mixed>|null $expected
     * @param list<mixed> $actual
     */
    private function argumentsMatch(?array $expected, array $actual): bool
    {
        if ($expected === null) {
            return true;
        }

        if (count($expected) !== count($actual)) {
            return false;
        }

        foreach (array_values($expected) as $i => $argument) {
            $matches = $argument instanceof Closure ? $argument($actual[$i]) : $argument === $actual[$i];

            if (!$matches) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param list<mixed>|null $arguments
     */
    private function describeArguments(?array $arguments): string
    {
        if ($arguments === null) {
            return '...';
        }

        $described = [];

        foreach ($arguments as $argument) {
            $described[] = $argument instanceof Closure ? '<matcher>' : var_export($argument, true);
        }

        return implode(', ', $described);
    }
}
