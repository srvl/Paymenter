<?php

namespace Paymenter\Extensions\Others\PaymenterBotApi;

use Illuminate\Support\HtmlString;
// use App\Support\Passport\ScopeRegistry;
use App\Classes\Extension\Extension;

class PaymenterBotApi extends Extension
{
    public static $version = "v1.1.0";
    /**
     * Get all the configuration for the extension
     * 
     * @param array $values
     * @return array
     */
    public function getConfig($values = [])
    {
        return [
            
        ];
    }

    public function boot()
    {
        require __DIR__ . '/routes/api.php';
        // ScopeRegistry::addMany([
        //     'services.read' => 'View your services',
        //     'invoices.read' => 'View your invoices',
        //     'orders.read' => 'View your orders',
        //     'tickets.read' => 'View your tickets',
        // ]);
    }
}
