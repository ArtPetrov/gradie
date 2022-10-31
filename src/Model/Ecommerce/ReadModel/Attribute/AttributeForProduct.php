<?php
declare(strict_types=1);

namespace App\Model\Ecommerce\ReadModel\Attribute;

class AttributeForProduct
{
    public $values = null;
    public $label = null;

    public function __construct(string $value)
    {
        if ($this->values) {
            $values = json_decode($this->values, TRUE);
            foreach ($values as $attr) {
                if ($attr['value'] === $value) {
                    $this->label = $attr['label'];
                    break;
                }
            }
        }
    }

    public function getLabelForValue(): ?string
    {
        return $this->label;
    }

}