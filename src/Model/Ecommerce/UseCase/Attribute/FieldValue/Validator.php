<?php

declare(strict_types=1);

namespace App\Model\Ecommerce\UseCase\Attribute\FieldValue;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;

class Validator
{
    public static function validValues(ArrayCollection $values): void
    {
        if (count($values) === 0) {
            throw new \DomainException("attributes.select.zero");
        }

        foreach ($values as $field) {

            if (!isset($field->label) || !isset($field->value)) {
                throw new \DomainException("attributes.select.null");
            }

            if ($values->matching(self::searchDuplicate($field))->count() > 1) {
                throw new \DomainException("attributes.select.duplication");
            }

            if (!self::validationValue($field->value)) {
                throw new \DomainException("attributes.select.value.error.validation");
            }

        }
    }

    public static function searchDuplicate(Command $field): Criteria
    {
        return Criteria::create()
            ->where(Criteria::expr()->eq('label', $field->label))
            ->orWhere(Criteria::expr()->eq('value', $field->value));
    }

    public static function validationValue(string $value): bool
    {
        return preg_match('/^[A-z0-9._-]+$/i', $value) === 1;
    }

}