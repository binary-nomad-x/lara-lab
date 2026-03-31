<?php

namespace App\Helpers;

class NexusHelper
{
    public static function formatCurrency($amount, $currency = 'USD')
    {
        return $currency . ' ' . number_format($amount, 2);
    }

    public static function getStatusBadge($status)
    {
        $statusClasses = [
            'Draft' => 'bg-label-warning',
            'Confirmed' => 'bg-label-success',
            'Processing' => 'bg-label-info',
            'PaymentPending' => 'bg-label-danger',
            'Completed' => 'bg-label-primary',
            'Cancelled' => 'bg-label-secondary',
        ];

        $class = $statusClasses[$status] ?? 'bg-label-secondary';
        
        return "<span class=\"badge $class text-uppercase\">$status</span>";
    }

    public static function getStockStatus($stock)
    {
        if ($stock <= 0) return '<span class="badge bg-label-danger">Out of Stock</span>';
        if ($stock <= 10) return '<span class="badge bg-label-warning">Low Stock</span>';
        return '<span class="badge bg-label-success">Available</span>';
    }

    public static function generateTransactionId()
    {
        return 'TX-' . strtoupper(bin2hex(random_bytes(5)));
    }
}
