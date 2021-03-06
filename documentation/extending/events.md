# Events

Statamic _butik_ does dispatch evens on certain actions, so you can easily hook into them, if you want to extend _butik_.

## How to hook into events

[Check out the Statamic documentation](https://statamic.dev/extending/events#overview)

## Available events

Those events are available at the moment:

### Order Authorized

 `Jonassiewertsen\StatamicButik\Events\OrderAuthorized`

Will be dispatched if a payment has been authorized,  if using Klarna for example.

```php
public function handle(OrderAuthorized $event)
{
    $event->order;
}
```

### 

### Order Canceled

 `Jonassiewertsen\StatamicButik\Events\OrderCanceled`

Will be dispatched if a payment has been canceled. For example after expiring, if not paid. 

```php
public function handle(OrderCanceled $event)
{
    $event->order;
}
```

### 

### Order Completed

 `Jonassiewertsen\StatamicButik\Events\OrderCompleted`

Will be dispatched if a payment has been fully completed.

With Mollie this is the case, after all items has been paid & shipped. 

```php
public function handle(OrderCompleted $event)
{
    $event->order;
}
```

### 

### Order Created

 `Jonassiewertsen\StatamicButik\Events\OrderCreated`

Will be dispatched right after an order has been created.

```php
public function handle(OrderCreated $event)
{
    $event->order;
}
```

### 

### Order Expired

 `Jonassiewertsen\StatamicButik\Events\OrderExpired`

Will be dispatched if a payment did expire. 

```php
public function handle(OrderExpired $event)
{
    $event->order;
}
```



### Order Paid

 `Jonassiewertsen\StatamicButik\Events\OrderPaid`

Will be dispatched after the order has been paid. 

```php
public function handle(OrderPaid $event)
{
    $event->order;
}
```

