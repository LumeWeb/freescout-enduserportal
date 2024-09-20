<?php

return [
    'name' => 'EndUserPortal',
    'options' => [
        EndUserPortal::getPluginOption('portal_oauth_enabled') => ['default' => false],
        EndUserPortal::getPluginOption('portal_server_url') => ['default' => ''],
        EndUserPortal::getPluginOption('portal_client_id') => ['default' => ''],
        EndUserPortal::getPluginOption('portal_client_secret') => ['default' => ''],
    ],
];
