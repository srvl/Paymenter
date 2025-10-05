<?php

namespace Paymenter\Extensions\Others\DiscordLinkedRoles;

use App\Classes\Extension\Extension;
use App\Models\Setting;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Facades\Schema;
use Paymenter\Extensions\Others\DiscordLinkedRoles\Models\LinkedRoleSetting;

class DiscordLinkedRoles extends Extension
{
    public function getConfig($values = [])
    {
        try {
            if (Schema::hasTable('linked_role_settings')) {
                $bots = LinkedRoleSetting::all(['id', 'discordlinkedroles_bot_name']);
                $botOptions = $bots->filter(function ($bot) {
                    return !empty($bot->discordlinkedroles_bot_name);
                })->pluck('discordlinkedroles_bot_name', 'id')->toArray();
                
                $selectedBotId = Setting::where('key', 'discord_bot_id')->value('value');
                $discordBot = LinkedRoleSetting::where('id', $selectedBotId)->first();
                $discordBotName = $discordBot ? $discordBot->discordlinkedroles_bot_name : 'No bot';
                
                return [
                    [
                        'name' => 'Notice',
                        'type' => 'placeholder',
                        'label' => new HtmlString('Discord Linked Roles Extension, originally created by Corwin, the owner of Paymenter, and later adapted by Ricardo Neud.'),
                    ],
                    [
                        'name' => 'Version Check',
                        'type' => 'placeholder',
                        'label' => new HtmlString($this->getVersion() . ' <a href="https://docs.ricardoneud.com/products/billing-portals/paymenter/extensions/discord-linked-roles/setting-up/" target="_blank" style="color:#3b82f6;text-decoration:underline;">View Documentation</a>'),
                    ],
                    [
                        'name' => 'discord_bot_id',
                        'type' => 'select',
                        'label' => 'Select a Discord Bot',
                        'options' => $botOptions,
                        'description' => 'Linked Roles connected with ' . $discordBotName,
                        'value' => $selectedBotId,
                        'disabled' => false,
                        'live' => true,
                    ]
                ];
            } else {
                return $this->defaultConfig();
            }
        } catch (\Exception $e) {
            return $this->defaultConfig();
        }
    }

    private function defaultConfig()
    {
        $selectedBotId = Setting::where('key', 'discord_bot_id')->value('value');
        return [
            [
                'name' => 'Notice',
                'type' => 'placeholder',
                'label' => new HtmlString('Discord Linked Roles Extension, originally created by Corwin, the owner of Paymenter, and later adapted by Ricardo Neud.'),
            ],
            [
                'name' => 'Version Check',
                'type' => 'placeholder',
                'label' => new HtmlString($this->getVersion() . ' <a href="https://docs.ricardoneud.com/products/billing-portals/paymenter/extensions/discord-linked-roles/setting-up/" target="_blank" style="color:#3b82f6;text-decoration:underline;">View Documentation</a>'),
            ],
            [
                'name' => 'discord_bot_id',
                'type' => 'select',
                'label' => 'Select a Discord Bot',
                'options' => [
                    '' . $selectedBotId . '' => 'No Bots Found',
                ],
                'description' => 'No bot selected.',
                'value' => $selectedBotId,
                'disabled' => true,
                'live' => true,
            ],
        ];
    }

    public function enabled()
    {
        Artisan::call('migrate', [
            '--path' => [
                'extensions/Others/DiscordLinkedRoles/database/migrations/2025_02_13_122225_create_linkedroles_table.php',
                'extensions/Others/DiscordLinkedRoles/database/migrations/2025_02_13_122225_create_linkedroles_custom_pages_table.php',
                'extensions/Others/DiscordLinkedRoles/database/migrations/2025_02_13_122225_create_linkedroles_custom_error_pages_table.php',
            ],
            '--force' => true,
        ]);
    }

    public function boot()
    {
        require __DIR__ . '/routes/web.php';
    }

    public function getVersion()
    {
        try {
            $response = Http::get("https://api.ricardoneud.com/public/products/3/latest-version");
            $latestRelease = $response->json();
            if (!is_array($latestRelease) || !isset($latestRelease['latest_version'])) {
                return 'Could not check for updates at this time.';
            }
            $latestVersion = $latestRelease['latest_version'];
            $currentVersion = 'v1.2';
            if (version_compare($currentVersion, $latestVersion, '>')) {
                return 'The version ' . $currentVersion . ' does not exist. If this is a Alpha version, it may contain errors. Please downgrade to the latest stable version (' . $latestVersion . ') to avoid potential issues.';
            } elseif ($currentVersion === $latestVersion) {
                return 'You are using the latest version (' . $latestVersion . ').';
            } else {
                return 'You are using version ' . $currentVersion . ', but version ' . $latestVersion . ' is available. Please update!';
            }
        } catch (\Exception $e) {
            return 'Could not check for updates at this time.';
        }
    }
}