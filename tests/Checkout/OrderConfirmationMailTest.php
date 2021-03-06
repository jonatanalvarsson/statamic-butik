<?php

namespace Jonassiewertsen\StatamicButik\Tests\Checkout;

use Illuminate\Support\Facades\Mail;
use Jonassiewertsen\StatamicButik\Http\Models\Order;
use Jonassiewertsen\StatamicButik\Mail\Customer\PurchaseConfirmation;
use Jonassiewertsen\StatamicButik\Mail\Seller\OrderConfirmation;
use Jonassiewertsen\StatamicButik\Tests\TestCase;
use Jonassiewertsen\StatamicButik\Tests\Utilities\MolliePaymentSuccessful;
use Mollie\Laravel\Facades\Mollie;

class OrderConfirmationMailTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        Mail::fake();
    }

    /** @test */
    public function a_purchase_confirmation_mail_will_be_sent_to_the_customer()
    {
        $order = create(Order::class)->first();

        $payment = new MolliePaymentSuccessful();
        $payment->id = $order->id;

        $this->mockMollie($payment);
        $this->post(route('butik.payment.webhook.mollie'), ['id' => $payment->id]);

        Mail::assertQueued(PurchaseConfirmation::class);
    }

    /** @test */
    public function a_purchase_confirmation_mail_will_be_addressed_correctly()
    {
        $order = create(Order::class)->first();

        $payment = new MolliePaymentSuccessful();
        $payment->id = $order->id;
        $this->mockMollie($payment);
        $this->post(route('butik.payment.webhook.mollie'), ['id' => $payment->id]);

        Mail::assertQueued(PurchaseConfirmation::class, function ($mail) use ($order) {
            return $mail->hasTo($order->customer->email);
        });
    }

    /** @test */
    public function a_order_confirmation_mail_will_be_sent_to_the_seller()
    {
        $order = create(Order::class)->first();

        $payment = new MolliePaymentSuccessful();
        $payment->id = $order->id;

        $this->mockMollie($payment);

        $this->post(route('butik.payment.webhook.mollie'), ['id' => $payment->id]);

        Mail::assertQueued(OrderConfirmation::class);
    }

    /** @test */
    public function a_order_confirmation_mail_will_be_addressed_to_the_seller()
    {
        $order = create(Order::class)->first();

        $payment = new MolliePaymentSuccessful();
        $payment->id = $order->id;

        $this->mockMollie($payment);
        $this->post(route('butik.payment.webhook.mollie'), ['id' => $payment->id]);

        Mail::assertQueued(OrderConfirmation::class, function ($mail) {
            return $mail->hasTo(config('butik.order-confirmations'));
        });
    }

    /** @test */
    public function the_data_will_get_passed_to_the_blade_views()
    {
        $order = create(Order::class, ['paid_at' => now()])->first();

        $payment = new MolliePaymentSuccessful();
        $payment->id = $order->id;
        $payment->paidAt = $order->paid_at;

        $this->mockMollie($payment);
        $this->post(route('butik.payment.webhook.mollie'), ['id' => $payment->id]);

        Mail::assertQueued(OrderConfirmation::class, function ($mail) use ($order) {
            return $mail->order->id == $order->id &&
                $mail->items == $order->items &&
                $mail->paid_at == $order->paid_at &&
                $mail->order_id == $order->id &&
                $mail->customer == $order->customer &&
                $mail->total_amount == $order->total_amount;
        });
    }

    public function mockMollie($mock)
    {
        Mollie::shouldReceive('api->orders->get')
            ->andReturn($mock);
    }
}
