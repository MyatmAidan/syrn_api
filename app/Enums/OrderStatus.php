<?php

namespace App\Enums;

enum OrderStatus: string
{
    case PendingPayment = 'pending_payment';
    case AwaitingVerification = 'awaiting_verification';
    case Confirmed = 'confirmed';
    case Cancelled = 'cancelled';
}
