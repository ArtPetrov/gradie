<?php

declare(strict_types=1);

namespace App\Tests\Unit\Model\Ecommerce\Helper;

use App\Model\Ecommerce\Helper\ArrayCollectionHelper;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\TestCase;

class ArrayCollectionHelperTest extends TestCase
{
    /**
     * @dataProvider providerSortableCorrectly
     */
    public function testSortableSuccess(string $json, string $result): void
    {
        $collection = $this->builderCollection($json);
        $collection = ArrayCollectionHelper::sortableByField($collection, 'position');

        $this->assertSame($this->getSequence($collection, 'id'), $result);
    }

    /**
     * @dataProvider providerSortableErrors
     */
    public function testSortableError($json, $result): void
    {
        $collection = $this->builderCollection($json);
        $collection = ArrayCollectionHelper::sortableByField($collection, 'position');

        $this->assertNotSame($this->getSequence($collection, 'id'), $result);
    }

    /**
     * @dataProvider providerEqualCorrectly
     */
    public function testEqualSuccess(string $json1, string $json2): void
    {
        $result = ArrayCollectionHelper::equalValues($this->builderCollection($json1), $this->builderCollection($json2));
        $this->assertTrue($result);
    }

    /**
     * @dataProvider providerEqualErrors
     */
    public function testEqualError(string $json1, string $json2): void
    {
        $result = ArrayCollectionHelper::equalValues($this->builderCollection($json1), $this->builderCollection($json2));
        $this->assertNotTrue($result);
    }

    public function builderCollection(string $json): ArrayCollection
    {
        $jsonData = json_decode($json);
        $collection = new ArrayCollection();

        foreach ($jsonData as $element) {
            $collection->add($element);
        }
        return $collection;
    }

    private function getSequence(ArrayCollection $collection, string $fieldName): string
    {
        $fields = [];
        foreach ($collection as $element) {
            $fields[] = $element->$fieldName;
        }
        return implode(",", $fields);
    }

    public function providerSortableErrors(): array
    {
        return [
            ['json' => '[{"id":2,"position":2},{"id":3,"position":3},{"id":1,"position":1}]', 'result' => '2,2,3'],
            ['json' => '[{"id":1,"position":3},{"id":2,"position":1},{"id":3,"position":2}]', 'result' => '2,1'],
            ['json' => '[]', 'result' => '4'],
        ];
    }

    public function providerSortableCorrectly(): array
    {
        return [
            ['json' => '[{"id":2,"position":2},{"id":3,"position":3},{"id":1,"position":1}]', 'result' => '1,2,3'],
            ['json' => '[{"id":1,"position":3},{"id":2,"position":1},{"id":3,"position":2}]', 'result' => '2,3,1'],
            ['json' => '[]', 'result' => ''],
        ];
    }

    public function providerEqualErrors(): array
    {
        return [
            ['collection1' => '[{"id":2,"position":2},{"id":1,"position":1}]', 'collection2' => '[{"id":2,"position":3},{"id":1,"position":1}]'],
            ['collection1' => '[{"id":2,"position":2}]', 'collection2' => '[]'],
        ];
    }

    public function providerEqualCorrectly(): array
    {
        return [
            ['collection1' => '[{"id":2,"position":2},{"id":1,"position":1}]', 'collection2' => '[{"id":2,"position":2},{"id":1,"position":1}]'],
            ['collection1' => '[]', 'collection2' => '[]'],
        ];
    }
}