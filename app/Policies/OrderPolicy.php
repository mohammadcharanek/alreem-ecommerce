<?php

namespace App\Policies;

use App\Models\Order;
use App\Models\User;

class OrderPolicy
{
    public function view(User $user, Order $order): bool
    {
        return (int) $user->id === (int) $order->user_id
            || (bool) $user->is_admin;
    }
}
