<?php

/**
 * Copyright (C) 2023 OpenStars Lab Development Team
 *
 * This file is part of spark/spark
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA
 */

namespace Spark\Framework\Framework\Routing;

use Nulldark\Routing\Route;

class CallableResolver implements CallableResolverInterface
{
    public function resolve(Route $route): callable
    {
        $toResolve = $this->prepareCandidateToResolve($route->callback());
        if (\is_callable($toResolve)) {
            return $toResolve;
        }

        return $this->tryResolveCandidate($toResolve);
    }

    /**
     * @param string $toResolve
     *  Candidate to resolve.
     *
     * @return callable
     *  Returns prepared candidate.
     */
    private function tryResolveCandidate(string $toResolve): callable
    {
        \preg_match('/^([^:]+)::([a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)$/', $toResolve, $matches);
        \array_shift($matches);

        if ($matches[1] === null) {
            $matches[1] = '__invoke';
        }

        try {
            $matches[0] = new $matches[0]();
        } catch (\Error|\LogicException $error) {
            if (\is_callable($matches[0])) {
                return $matches;
            }

            throw $error;
        }

        return $matches;
    }

    /**
     * @param array|string|callable $toResolve
     *  Candidate to resolve.
     *
     * @return string|callable
     *  Prepared candidate.
     */
    private function prepareCandidateToResolve(array|string|callable $toResolve): string|callable
    {
        if (!\is_array($toResolve)) {
            return $toResolve;
        }

        $candidate = $toResolve;
        $controller = \array_shift($candidate);
        $method = \array_shift($candidate);

        if (\is_string($controller) && \is_string($method)) {
            return $controller . "::" . $method;
        }

        return $toResolve;
    }
}