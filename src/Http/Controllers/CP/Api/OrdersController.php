<?php

namespace Jonassiewertsen\StatamicButik\Http\Controllers\CP\Api;

use Illuminate\Pagination\LengthAwarePaginator;
use Jonassiewertsen\StatamicButik\Blueprints\OrderBlueprint;
use Jonassiewertsen\StatamicButik\Http\Models\Order;
use Statamic\Http\Resources\CP\Submissions\Submissions;

class OrdersController
{
    public function index()
    {
        $orders = Order::all();

        $paginator = new LengthAwarePaginator($orders, 2, 50, 0);

        $orderBlueprint = new OrderBlueprint();

        return (new Submissions($paginator))
            ->blueprint($orderBlueprint())
            ->columnPreferenceKey("id");
    }
}
