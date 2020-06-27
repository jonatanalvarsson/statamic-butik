<?php

namespace Jonassiewertsen\StatamicButik\Tests\Unit;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Jonassiewertsen\StatamicButik\Checkout\Item;
use Jonassiewertsen\StatamicButik\Http\Models\Product;
use Jonassiewertsen\StatamicButik\Http\Models\ShippingProfile;
use Jonassiewertsen\StatamicButik\Http\Traits\MoneyTrait;
use Jonassiewertsen\StatamicButik\Tests\TestCase;

class ItemTest extends TestCase
{
    use MoneyTrait;

    protected Product $product;

    public function setUp(): void {
        parent::setUp();

        $this->product = create(Product::class, ['stock' => 5])->first();
    }

    /** @test */
    public function it_has_a_id()
    {
        $item = new Item($this->product);

        $this->assertEquals($item->id, $this->product->slug);
    }

    /** @test */
    public function it_has_a_taxRate()
    {
        $item = new Item($this->product);

        $this->assertEquals($item->taxRate, $this->product->tax->percentage);
    }

    /** @test */
    public function it_has_a_name()
    {
        $item = new Item($this->product);

        $this->assertEquals($item->name, $this->product->title);
    }

    /** @test */
    public function it_can_be_buyable()
    {
        $item = new Item($this->product);

        $this->assertTrue($item->buyable);
    }

    /** @test */
    public function it_can_be_declared_as_buyable()
    {
        $item = new Item($this->product);
        $item->buyable();

        $this->assertTrue($item->buyable);
    }

    /** @test */
    public function it_can_be_declared_as_notbuyable()
    {
        $item = new Item($this->product);
        $item->notBuyable();

        $this->assertFalse($item->buyable);
    }

    /** @test */
    public function it_has_a_description()
    {
        $item = new Item($this->product);

        $this->assertEquals($item->description, Str::limit($this->product->description, 100));
    }

    /** @test */
    public function it_has_a_default_quanitity_of_1()
    {
        $item = new Item($this->product);

        $this->assertEquals($item->getQuantity(), 1);
    }

    /** @test */
    public function an_item_can_be_increased()
    {
        $item = new Item($this->product);
        $item->increase();

        $this->assertEquals($item->getQuantity(), 2);
    }

    /** @test */
    public function an_item_can_max_increases_to_the_avialable_stock()
    {
        $this->product->update(['stock' => 1]);
        $item = new Item($this->product);
        $item->increase();

        $this->assertEquals(1, $item->getQuantity());
    }

    /** @test */
    public function an_item_can_be_decreased()
    {
        $item = new Item($this->product);
        $item->setQuantity(2);

        $item->decrease();

        $this->assertEquals($item->getQuantity(), 1);
    }

    /** @test */
    public function an_item_will_check_the_stock_when_increasing()
    {
        $item = new Item($this->product);
        $item->setQuantity(10);

        $this->assertEquals($item->getQuantity(), 5);
    }

    /** @test */
    public function an_item_quantity_cant_be_lover_then_one()
    {
        $item = new Item($this->product);
        $item->decrease();

        $this->assertEquals($item->getQuantity(), 1);
    }

    /** @test */
    public function an_item_has_a_total_price()
    {
        $item = new Item($this->product);

        $this->assertEquals($this->product->price, $item->totalPrice());
    }

    /** @test */
    public function multiple_prices_will_be_added_up_by_the_given_quantity()
    {
        $item = new Item($this->product);
        $item->setQuantity(3);

        $productPrice = $this->makeAmountSaveable($this->product->price);
        $total = $this->makeAmountHuman($productPrice * 3);

        $this->assertEquals($total, $item->totalPrice());
    }

    /** @test */
    public function A_new_name_will_be_reflected_on_the_item_update()
    {
        $item = new Item($this->product);

        $newDescription = 'new Description';
        $this->product->update(['description' => $newDescription]);
        Cache::flush();
        $item->update();

        $this->assertEquals($item->description, $newDescription);
    }

    /** @test */
    public function A_new_base_price_will_be_reflected_on_the_item_update()
    {
        $item = new Item($this->product);

        $newPrice = 9999;
        $this->product->update(['price' => $newPrice]);
        Cache::flush();
        $item->update();

        $this->assertEquals($item->singlePrice(), $this->product->price);
    }

    /** @test */
    public function A_new_total_base_price_will_be_reflected_on_the_item_update()
    {
        $item = new Item($this->product);

        $oldPrice = $item->totalPrice();
        $this->product->update(['price' => 999]);
        Cache::flush();
        $item->update();

        $this->assertNotEquals($item->totalPrice(), $oldPrice);
    }

    /** @test */
    public function A_non_available_item_will_be_set_to_null()
    {
        $item = new Item($this->product);

        $this->product->update(['available' => false]);

        Cache::flush();
        $item->update();

        $this->assertEquals(0, $item->getQuantity());
    }

    /** @test */
    public function it_has_a_shipping_profile()
    {
        $item = new Item($this->product);

        $this->product->update(['available' => false]);

        Cache::flush();
        $item->update();

        $this->assertEquals(ShippingProfile::first(), $item->shippingProfile);
    }
}
