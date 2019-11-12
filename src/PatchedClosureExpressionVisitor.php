<?php

declare(strict_types=1);

namespace Someniatko\MatchingArrayCollection;

use Doctrine\Common\Collections\Expr\ClosureExpressionVisitor;

/** @internal */
final class PatchedClosureExpressionVisitor extends ClosureExpressionVisitor
{
    public static function getObjectFieldValue($object, $field)
    {
        if (strpos($field, '.') !== false) {
            [ $mainField, $subField ] = explode('.', $field, 2);
            $object = parent::getObjectFieldValue($object, $mainField);
            return parent::getObjectFieldValue($object, $subField);
        }

        return parent::getObjectFieldValue($object, $field);
    }
}
