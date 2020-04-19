<?php

namespace Jonassiewertsen\StatamicButik\Http\Controllers\Web;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Jonassiewertsen\StatamicButik\Checkout\Customer;
use Jonassiewertsen\StatamicButik\Http\Controllers\WebController;
use Jonassiewertsen\StatamicButik\Http\Models\Order;
use Jonassiewertsen\StatamicButik\Http\Models\Product;

class CheckoutController extends Checkout
{
    public function delivery()
    {
        $customer = session()->has('butik.customer') ?
                    Session::get('butik.customer') :
                    (new Customer())->empty();

        return view(config('butik.template_checkout-delivery'), compact('customer'));
    }

    public function saveCustomerData()
    {
        $validatedData = request()->validate($this->rules());

        $customer = new Customer($validatedData);

        Session::put('butik.customer', $customer);

//        return redirect()->route('butik.checkout.payment');
    }

    public function payment(Product $product)
    {
//        $customer = session('butik.customer');
//
//        return view(config('butik.template_express-checkout-payment'), compact('customer', 'product'));
    }

    public function receipt(Request $request, $order)
    {
//        if (!$request->hasValidSignature()) {
//           return $this->showInvalidReceipt();
//        }
//
//        if (! $order = Order::find($order))
//        {
//            return $this->showInvalidReceipt();
//        }
//
//        $customer = json_decode($order->customer);
//
//        if ($order->status === 'paid') {
//            Session::forget('butik.customer');
//        }
//
//        return view(config('butik.template_checkout-receipt'), compact('customer', 'order'));
    }
}
