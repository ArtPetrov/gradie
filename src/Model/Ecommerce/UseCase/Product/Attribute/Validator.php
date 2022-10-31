<?php

declare(strict_types=1);

namespace App\Model\Ecommerce\UseCase\Product\Attribute;

use App\Model\Ecommerce\Entity\Attribute\Field;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;

class Validator
{
    public static function validValues(ArrayCollection $values): void
    {
        foreach ($values as $field) {

            if (!isset($field->label) || (!isset($field->value) && $field->type !== Field::BOOL)) {
                throw new \DomainException("product.attribute.select.null");
            }

            if ($values->matching(self::searchDuplicate($field))->count() > 1) {
                throw new \DomainException("product.attribute.select.duplication");
            }

        }
    }

    public static function searchDuplicate(Command $field): Criteria
    {
        return Criteria::create()
            ->where(Criteria::expr()->eq('slug', $field->slug));
    }

    public static function validationValue(string $value): bool
    {
        return preg_match('/^[A-z0-9 ,._-]+$/i', $value) === 1;
    }

}