<?php
declare(strict_types=1);

namespace App\Model\Ecommerce\ReadModel\Attribute;

class AttributeForFilter
{
    public $id;
    public $slug;
    public $field_type;
    public $name;
    public $values = null;
    public $value;
    public $from;
    public $to;

    public function __construct()
    {
        if ($this->values) {
            $values = json_decode($this->values, TRUE);
            if (count($values) > 0) {
                $this->values = array_map(static function ($attr) {
                    return $attr['value'];
                }, $values);
            }
        }
    }

    public static function create($slug, $type): self
    {
        $command = new self();
        $command->slug = $slug;
        $command->field_type = $type;
        return $command;
    }
}