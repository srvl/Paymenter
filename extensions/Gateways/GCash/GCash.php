<?php

namespace Paymenter\Extensions\Gateways\GCash;

use App\Classes\Extension\Gateway;
// Removed unnecessary imports for brevity
use App\Models\Invoice;
use Illuminate\Support\Facades\View;
// Added View namespace logic to boot()

class GCash extends Gateway
{
    // Copy the view registration pattern from PayPal.php's boot() method
    public function boot()
    {
        // Load the extension's routes file
        require __DIR__ . '/routes.php'; 
        
        // Register the view namespace: 'gateways.gcash' points to the views directory
        View::addNamespace('gateways.gcash', __DIR__ . '/resources/views');
    }

    /**
     * Get all the configuration for the extension
     *
     * @param array $values
     * @return array
     */
    public function getConfig($values = [])
    {
        // For this simple placeholder, no specific settings are required.
        return [];
    }

    /**
     * Return a view to display the payment instructions and credit check.
     *
     * @param Invoice $invoice The invoice object associated with the payment.
     * @param float $total The total amount to be paid.
     * @return \Illuminate\Contracts\View\View|string
     */
    public function pay(Invoice $invoice, $total)
    {
        // Use the namespace registered in the boot() method: 'gateways.gcash'
        return View::make('gateways.gcash::gcash-popup', [ 
            'invoice' => $invoice,
            'total' => $total,
        ]);
    }
}