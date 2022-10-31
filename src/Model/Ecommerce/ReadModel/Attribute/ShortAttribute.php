<?php
declare(strict_types=1);

namespace App\Model\Ecommerce\ReadModel\Attribute;

use App\Model\Ecommerce\Entity\Attribute\Field;

class ShortAttribute
{
    public $id;
    public $slug;
    public $field_type;
    public $name;
    public $field_name;
    public $values;

    public function __construct(string $field_type)
    {
        $this->field_name = Field::labelField($this->$field_type);
    }
}