<?php

namespace Paymenter\Extensions\Others\Redeemify;

use Livewire\Livewire;
use Illuminate\Support\HtmlString;
use App\Classes\Extension\Extension;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Artisan;
use Paymenter\Extensions\Others\Redeemify\Admin\Resources\RedeemifyCodeResource;
use Paymenter\Extensions\Others\Redeemify\Livewire\Redeemify\Redeemify as RedeemifyComponent;

class Redeemify extends Extension
{
    /**
     * Get all the configuration for the extension
     * 
     * @param array $values
     * @return array
     */
    public function getConfig($values = [])
    {
        try {
            return [
                [
                    'name' => 'Notice',
                    'type' => 'placeholder',
                    'label' => new HtmlString('🎁 You can use this extension to manage redeemable codes that give credits to users. To create or manage codes, go to <a class="text-primary-600" href="' . RedeemifyCodeResource::getUrl() . '">Redeemify</a>.'),
                ],
                [
                    'name' => 'display_history',
                    'type' => 'checkbox',
                    'label' => 'Display Redeem History',
                    'default' => false,
                    'description' => 'Enable this option to show the history of redeemed codes by users.'
                ],
                [
                    'name' => 'use_discord_webhook',
                    'type' => 'checkbox',
                    'label' => 'Enable Discord Broadcasting',
                    'desccription' => 'You can trigger action in edit menu'
                ],
                [
                    'name' => 'discord_webhook',
                    'type' => 'password',
                    'label' => 'Discord Webhook URL',
                ],
                [
                    'name' => 'to_ping_role_id',
                    'type' => 'number',
                    'label' => 'The ID of the role to ping'
                ]
            ];
        } catch (\Exception $e) {
            return [
                [
                    'name' => 'Notice',
                    'type' => 'placeholder',
                    'label' => new HtmlString('⚠️ Redeemify is not ready yet. Please make sure the extension is enabled and Filament is working.'),
                ],
            ];
        }
    }

    public function enabled()
    {
        Artisan::call('migrate', [
            '--path' => [
                'extensions/Others/Redeemify/database/migrations/2025_06_27_115639_create_ext_redeemify_codes.php',
                'extensions/Others/Redeemify/database/migrations/2025_06_27_120049_create_ext_redeemify_usages.php',
            ],
            '--force' => true,
        ]);
    }

    public function boot()
    {
        require __DIR__ . '/routes/web.php';
        View::addNamespace('redeemify', __DIR__ . '/resources/views');
        Lang::addNamespace('redeemify', __DIR__ . '/resources/lang');
        Livewire::component('redeemify', RedeemifyComponent::class);
        Event::listen('navigation.account', function () {
            return [
                'name' => __('redeemify::redeemify.redeemify'),
                'route' => 'redeemify.index',
                'priority' => 100,
            ];
        });
    }
}
