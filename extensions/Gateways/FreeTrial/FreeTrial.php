<?php

namespace Paymenter\Extensions\Gateways\FreeTrial;

use App\Classes\Extension\Gateway;
use App\Helpers\ExtensionHelper;
use App\Models\Invoice;

class FreeTrial extends Gateway
{

    /**
     * Get all the configuration for the extension
     * 
     * @param array $values
     * @return array
     */
    public function getConfig($values = [])
    {
        return [
            [
                'name' => 'max_order_value',
                'label' => 'Max Order Value',
                'type' => 'number',
                'required' => true,
            ]
        ];
    }
    
    /**
     * Return a view or a url to redirect to
     * 
     * @param Invoice $invoice
     * @param float $total
     * @return string
     */
    public function pay(Invoice $invoice, $total)
    {
        $items = $invoice->items;
        $maxOrderValue = $this->config('max_order_value');

        // Make sure that the invoice is not for adding credits
        foreach ($items as $item) {
            if ($item->reference_type == 'App\\Models\\Credit') {
                return redirect()->route('invoices.show', ['invoice' => $invoice->id])
                ->with('notification', ['message' => 'Cannot be used on Credits', 'type' => 'error']);
            }
        }

        if ($total > $maxOrderValue) {
            return redirect()->route('invoices.show', ['invoice' => $invoice->id])
            ->with('notification', ['message' => 'Item not eligible', 'type' => 'error']);
        }

        ExtensionHelper::addPayment($invoice->id, 'FreeTrial', $total);
        return redirect()->route('invoices.show', ['invoice' => $invoice->id])->with('notification', [
            'message' => 'Trial Activated',
            'type' => 'success'
        ]);
    }
}
