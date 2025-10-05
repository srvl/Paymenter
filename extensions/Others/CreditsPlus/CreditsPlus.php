<?php

/**
 * Credits Plus Extension for Paymenter
 * 
 * Created by: Exp (Discord: exp_yt)
 * Company: ExpHost - https://www.exphost.net/
 * 
 * This code is the intellectual property of Exp and ExpHost.
 * Unauthorized copying, modification, or distribution is prohibited.
 */

namespace Paymenter\Extensions\Others\CreditsPlus;

use App\Classes\Extension\Extension;

class CreditsPlus extends Extension
{
    public function getName(): string
    {
        return 'Credits Plus';
    }

    public function getDescription(): string
    {
        return 'Clickable credit display in navigation.';
    }

    public function getVersion(): string
    {
        return '2.0.0';
    }

    public function getAuthor(): string
    {
        return 'ExpHost';
    }

    public function boot(): void
    {
        $this->registerCreditsNavigation();
    }

    public function enabled(): void {}
    public function disabled(): void {}
    public function updated(): void {}
    public function install(): void {}

    private function registerCreditsNavigation(): void
    {
        try {
            if (function_exists('app') && app()->bound('view')) {
                app('view')->composer('components.navigation.index', function ($view) {
                    try {
                        if (!function_exists('auth') || !auth()->check() || !auth()->user()) {
                            return;
                        }

                        $user = auth()->user();
                        $creditsDisplay = $this->calculateUserCreditsDisplay($user);
                        $creditsUrl = function_exists('route') ? route('account.credits') : '/account/credits';

                        $customPath = __DIR__ . '/resources/views/components/navigation/index.blade.php';
                        if (file_exists($customPath)) {
                            $view->setPath($customPath);
                        }

                        $view->with([
                            'userCreditsDisplay' => $creditsDisplay,
                            'creditsUrl' => $creditsUrl
                        ]);
                    } catch (\Exception $e) {
                    }
                });
            }
        } catch (\Exception $e) {
        }
    }

    private function calculateUserCreditsDisplay($user): string
    {
        try {
            $credits = $user->credits()->with('currency')->get();

            if ($credits->isEmpty()) {
                $currencyCode = session('currency', config('settings.default_currency', 'USD'));
                $currency = \App\Models\Currency::where('code', $currencyCode)->first();
                if ($currency) {
                    return $currency->prefix . '0.00' . $currency->suffix;
                } else {
                    return '0.00 ' . $currencyCode;
                }
            }

            $creditsByCurrency = $credits->groupBy('currency_code');
            $maxAmount = null;
            $maxCurrencyCode = null;
            $maxCurrency = null;

            foreach ($creditsByCurrency as $currencyCode => $userCredits) {
                $totalAmount = $userCredits->sum('amount');
                if ($totalAmount <= 0) {
                    continue;
                }
                if ($maxAmount === null || $totalAmount > $maxAmount) {
                    $maxAmount = $totalAmount;
                    $maxCurrencyCode = $currencyCode;
                    $maxCurrency = $userCredits->first()->currency;
                }
            }

            if ($maxAmount === null) {
                $currencyCode = session('currency', config('settings.default_currency', 'USD'));
                $currency = \App\Models\Currency::where('code', $currencyCode)->first();
                if ($currency) {
                    return $currency->prefix . '0.00' . $currency->suffix;
                } else {
                    return '0.00 ' . $currencyCode;
                }
            }

            if ($maxCurrency) {
                return $maxCurrency->prefix . number_format($maxAmount, 2) . $maxCurrency->suffix;
            } else {
                return number_format($maxAmount, 2) . ' ' . $maxCurrencyCode;
            }
        } catch (\Exception $e) {
            $currencyCode = session('currency', config('settings.default_currency', 'USD'));
            $currency = class_exists('\App\Models\Currency') ? \App\Models\Currency::where('code', $currencyCode)->first() : null;
            if ($currency) {
                return $currency->prefix . '0.00' . $currency->suffix;
            } else {
                return '0.00 ' . $currencyCode;
            }
        }
    }
}
