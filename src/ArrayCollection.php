<?php

declare(strict_types=1);

namespace Someniatko\MatchingArrayCollection;

use Doctrine\Common\Collections\Criteria;

final class ArrayCollection extends \Doctrine\Common\Collections\ArrayCollection
{
    public function matching(Criteria $criteria)
    {
        $expr     = $criteria->getWhereExpression();
        $filtered = $this->toArray();

        if ($expr) {
            $filter   = (new PatchedClosureExpressionVisitor())->dispatch($expr);
            $filtered = array_filter($filtered, $filter);
        }

        $orderings = $criteria->getOrderings();

        if ($orderings) {
            $next = null;
            foreach (array_reverse($orderings) as $field => $ordering) {
                $next = PatchedClosureExpressionVisitor::sortByField($field, ($ordering === Criteria::DESC) ? -1 : 1, $next);
            }

            uasort($filtered, $next);
        }

        $offset = $criteria->getFirstResult();
        $length = $criteria->getMaxResults();

        if ($offset || $length) {
            $filtered = array_slice($filtered, (int) $offset, $length);
        }

        return $this->createFrom($filtered);
    }
}
