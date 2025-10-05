<?php

namespace Paymenter\Extensions\Gateways\GCash;

use App\Classes\Extension\Gateway;
use App\Models\Invoice;
use Illuminate\Support\Facades\Redirect; // Import the Redirect facade

class GCash extends Gateway
{
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
     * Return a view or a url to redirect to
     *
     * When a user selects this gateway and attempts to "add credit",
     * this method will be called. For this placeholder, it immediately
     * redirects the user back to the /account/credits page.
     *
     * @param Invoice $invoice The invoice object associated with the payment.
     * @param float $total The total amount to be paid.
     * @return string|\Illuminate\Http\RedirectResponse
     */
    public function pay(Invoice $invoice, $total)
    {
        // In a real GCash integration, this is where you would:
        // 1. Make an API call to GCash to initiate a payment.
        // 2. Redirect the user to the GCash payment page or display a QR code.
        // 3. Wait for a callback/webhook from GCash to confirm the payment.

        // For this placeholder, we simulate a "successful" initiation by
        // redirecting back to the credits page with a message.
        return Redirect::to('/account/credits')->with('success', 'Your GCash top-up request has been initiated. Please follow the manual instructions on this page to complete your top-up.');
    }

    // You might also need a webhook method if your system expects one for gateways.
    // If not, you can omit this.
    // public function webhook(Request $request): \Illuminate\Http\Response
    // {
    //     // Handle incoming webhook notifications from GCash here
    //     return response('GCash webhook received (placeholder).', 200);
    // }
}
