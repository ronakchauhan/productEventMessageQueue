<?php declare(strict_types=1);

use App\Product\Product;
use PHPUnit\Framework\TestCase;

class ProductTest extends TestCase
{
    public function testGetters()
    {
        $product = new Product(42, 'Staubsauger', 100.00);

        $this->assertEquals(42, $product->getId());
        $this->assertEquals('Staubsauger', $product->getName());
        $this->assertEquals(100.00, $product->getPrice());
    }

    public function testSetters()
    {
        $product = new Product(42, 'Staubsauger', 100.00);

        $product->setName('NewStaubsauger');
        $product->setPrice(150.00);

        $this->assertEquals('NewStaubsauger', $product->getName());
        $this->assertEquals(150.00, $product->getPrice());
    }

    public function testGetChangedFields()
    {
        $product1 = new Product(42, 'Staubsauger', 100.00);
        $product2 = new Product(42, 'NewStaubsauger', 150.00);

        $changedFields = $product2->getChangedFields($product1);

        $this->assertEquals(['name' => 'NewStaubsauger', 'price' => 150.00], $changedFields);
    }

    public function testToArray()
    {
        $product = new Product(42, 'Staubsauger', 100.00);

        $expectedArray = [
            'id' => 42,
            'name' => 'Staubsauger',
            'price' => 100.00,
        ];

        $this->assertEquals($expectedArray, $product->toArray());
    }
}
