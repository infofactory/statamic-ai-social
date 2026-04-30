<?php

namespace Infofactory\StatamicAiSocial;

use Statamic\Statamic;
use Statamic\Facades\CP\Nav;
use Statamic\Facades\Permission;
use Statamic\Providers\AddonServiceProvider;

class ServiceProvider extends AddonServiceProvider
{
    protected $vite = [
        'input' => [
            'resources/js/social-post-addon.js',
            'resources/css/social-post-addon.css',
        ],
        'publicDirectory' => 'resources/dist',
    ];

    public function bootAddon()
    {
        // Register permissions
        Permission::register('manage ai-social config')
            ->label('Manage Statamic Ai Social Configs');

        // Register navigation
        Nav::extend(function ($nav) {
            $nav->content(__('statamic-ai-social::cp.title'))
                ->section('Tools')
                ->route('statamic-ai-social.config')
                ->can('manage ai-social config');
        });

        // Publish the configs as well
        Statamic::afterInstalled(function ($command) {
            $command->call('vendor:publish', [
                '--tag' => $this->getAddon()->slug().'-config',
            ]);
        });
    }
}
