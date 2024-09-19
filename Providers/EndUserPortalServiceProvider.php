<?php

namespace Modules\EndUserPortal\Providers;

// Module alias.
define('EUP_MODULE', 'enduserportal');

use App\Conversation;
use App\Customer;
use App\Mailbox;
use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Factory;

class EndUserPortalServiceProvider extends ServiceProvider
{
    // Subfolder in URL.
    const URL_SUBFOLDER = 'help';
    const WIDGET_SALT = '0624d105de20';
    const AUTH_PERIOD = 43800; // month
    const AUTH_LINK_LIFETIME = 3600; // seconds

    public static $mailboxes_ids = [];

    // Authenticated customer.
    public static $auth_customer = null;

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Boot the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerConfig();
        $this->registerViews();
        $this->registerFactories();
        $this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');
        $this->hooks();
    }

    /**
     * Module hooks.
     */
    public function hooks()
    {
        // Add module's CSS file to the application layout.
        \Eventy::addFilter('stylesheets', function($styles) {
            $styles[] = \Module::getPublicPath(EUP_MODULE).'/css/module.css';
            $styles[] = \Module::getPublicPath(EUP_MODULE).'/css/bootstrap-colorpicker.css';
            return $styles;
        });

        // Add module's JS file to the application layout.
        \Eventy::addFilter('javascripts', function($javascripts) {
            //$javascripts[] = \Module::getPublicPath(TIMETR_MODULE).'/js/laroute.js';
            $javascripts[] = \Module::getPublicPath(EUP_MODULE).'/js/bootstrap-colorpicker.js';
            $javascripts[] = \Module::getPublicPath(EUP_MODULE).'/js/module.js';
            return $javascripts;
        });

        // Add item to the mailbox menu
        \Eventy::addAction('mailboxes.settings.menu', function($mailbox) {
            if (auth()->user()->isAdmin()) {
                echo \View::make('enduserportal::partials/settings_menu', ['mailbox' => $mailbox])->render();
            }
        }, 40);

        \Eventy::addFilter('menu.selected', function($menu) {
            if (self::isEup()) {
                $menu['enduserportal.submit'] = 'enduserportal.submit';
                $menu['enduserportal.tickets'] = 'enduserportal.tickets';
            }

            return $menu;
        });
    }

    /**
     * Returns a shorter value than encrypt().
     */
    public static function encodeMailboxId($id, $extra_salt = '')
    {
        return crc32(config('app.key').EUP_MODULE.$extra_salt.$id);

        //return encrypt($data);
        // $hashids = new Hashids();
        // return $hashids->encode($data);
    }

    public static function decodeMailboxId($encoded_id, $extra_salt = '')
    {
        if (!empty(self::$mailboxes_ids[$encoded_id])) {
            return self::$mailboxes_ids[$encoded_id];
        }
        $result = '';

        $mailboxes = Mailbox::get();

        foreach ($mailboxes as $mailbox) {
            $cur_encoded_id = self::encodeMailboxId($mailbox->id, $extra_salt);
            self::$mailboxes_ids[$cur_encoded_id] = $mailbox->id;

            if ($cur_encoded_id == $encoded_id) {
                $result = $mailbox->id;
            }
        }

        return $result;

        //return decrypt($data);

        // $hashids = new Hashids();
        // return $hashids->decode($data);
    }

    public static function getMailboxParam($mailbox, $param)
    {
        return $mailbox->meta['eup'][$param] ?? \EndUserPortal::getDefaultPortalSettings()[$param] ?? '';
    }    

    public static function isEup()
    {
        return preg_match('/.*\/'.self::URL_SUBFOLDER.'\/.*/', \Request::url());
    }

    public static function urlHome()
    {
        return route('enduserportal.submit', ['mailbox_id' => \Request::route()->parameter('mailbox_id')]);
    }

    public static function saveWidgetSettings($mailbox_id, $settings)
    {
        return \Option::set(EUP_MODULE.'.widget_settings_'.$mailbox_id, $settings);
    }    

    // public static function saveWidgetScript($mailbox_id, $settings)
    // {
    //     $file_name = self::getWidgetScriptFileName($mailbox_id);
    //     $file_path = storage_path('app/public/js/'.$file_name);

    //     $content = view('enduserportal::js/widget', ['settings' => $settings])->render();

    //     try {
    //         \Storage::put('js/'.$file_name, $content);
    //     } catch (\Exception $e) {
    //         throw new Exception(__("Could not save widget file, please check folders permissions:").' '.$file_path.' ('.$e->getMessage().')', 1);
    //     }

    //     $check = \Storage::get('js/'.$file_name);

    //     if (!$check) {
    //         throw new Exception(__("Could not save widget file, please check folders permissions:").' '.$file_path, 1);
    //     }
    // }

    // public static function getWidgetScriptFileName($mailbox_id)
    // {
    //     return 'eup_widget_'.self::encodeMailboxId($mailbox_id, self::WIDGET_SALT).'.js';
    // }
    
    public static function getWidgetScriptUrl($mailbox_id, $include_version = false)
    {
        $url = config('app.url').\Module::getPublicPath(EUP_MODULE).'/js/widget.js';

        if ($include_version) {
            $module = \Module::findByAlias(EUP_MODULE);
            if ($module) {
                $url .= '?v='.substr(crc32($module->get('version').config('app.key')), 0, 4);
            }
        }

        return $url;
        // $file_name = self::getWidgetScriptFileName($mailbox_id);
        // return \Storage::url('js/'.$file_name);
    }

    public static function getWidgetSettings($mailbox_id)
    {
        return \Option::get(EUP_MODULE.'.widget_settings_'.$mailbox_id, []);

        // $settings = [];

        // $file_name = self::getWidgetScriptFileName($mailbox_id);
        // $file_path = storage_path('app/public/js/'.$file_name);

        // try {
        //     $script = \Storage::get('js/'.$file_name);
        // } catch (\Exception $e) {
        //     return $settings;
        // }

        // preg_match("#/\* SETTINGS_START \*/(.*)/\* SETTINGS_END \*/#", $script, $m);

        // if (!empty($m[1])) {
        //     $settings = json_encode($m[1], true);
        // }

        // return $settings;
    }

    public static function getDefaultPortalSettings($param = '')
    {
        $settings = [
            //'locales' => [],
            'numbers' => 0,
            'existing' => 0,
            'text_submit' => __('Submit a Ticket'),
            'footer' => '&copy; {%year%} {%mailbox.name%}',
            'subject' => 0,
            'consent' => 0,
            'privacy' => '',
        ];
        if ($param) {
            return $settings[$param] ?? '';
        } else {
            return $settings;
        }
    }

    public static function getDefaultWidgetSettings()
    {
        return [
            'id' => '',
            'color' => '#0068bd',
            //'title' => __('Contact us'),
            'position' => 'br',
            'locale' => '',
        ];
    }

    public static function getPortalName($mailbox)
    {
        return __(':mailbox.name Support Portal', ['mailbox.name' => $mailbox->name]);
    }

    public static function authenticate($customer_id, $mailbox_id, $hash, $timestamp)
    {
        if (!is_numeric($timestamp) || time() - $timestamp > self::AUTH_LINK_LIFETIME) {
            throw new \Exception(__('Authentication link has expired'), 300);
        }

        $customer = Customer::find($customer_id);

        if ($customer && $hash == self::customerHash($customer->created_at)) {
            $cookie = cookie('enduserportal_auth', encrypt($customer_id.'|'.$hash), self::AUTH_PERIOD);
            return redirect()->route('enduserportal.tickets', ['mailbox_id' => \EndUserPortal::encodeMailboxId($mailbox_id)])
                ->withCookie($cookie);
        } else {
            return false;
        }
    }

    public static function customerHash($str)
    {
        return substr(md5($str), 0, 8);
    }

    /**
     * Get authenticated customer.
     */
    public static function authCustomer()
    {
        if (self::$auth_customer) {
            return self::$auth_customer;
        }

        $customer_id = null;
        $hash = null;
        $auth_data = request()->cookie('enduserportal_auth');

        if ($auth_data) {
            try {
                $auth_data_decrypted = decrypt($auth_data);
                list($customer_id, $hash) = explode('|', $auth_data_decrypted ?? '');
            } catch (\Exception $e) {
                return null;
            }
            if ($customer_id && $hash) {
                $customer = Customer::find($customer_id);
                if ($customer && self::customerHash($customer->created_at) == $hash) {
                    self::$auth_customer = $customer;
                }
            }
        }

        return self::$auth_customer;
    }

    public static function dateFormat($date, $format = 'M j, Y H:i')
    {
        if (is_string($date)) {
            // Convert string in to Carbon
            try {
                $date = Carbon::parse($date);
            } catch (\Exception $e) {
                $date = null;
            }
        }

        if (!$date) {
            return '';
        }


        // return $date->setTimezone($user->timezone)->format($format);
        return $date->format($format);
    }

    public static function hasNewReplies($conversation)
    {
        return !empty($conversation->has_new_replies);
    }

    public static function ticketUrl($conversation)
    {
        return route('enduserportal.ticket', [
            'mailbox_id' => \EndUserPortal::encodeMailboxId($conversation->mailbox_id),
            'conversation_id' => $conversation->id,
        ]);
    }

    public static function getStatusName($conversation)
    {
        if (in_array($conversation->status, [Conversation::STATUS_ACTIVE, Conversation::STATUS_PENDING]) && 
            $conversation->state != Conversation::STATE_DELETED
        ) {
            return __('Open');
        } else {
            return __('Closed');
        }
    }

    public static function getLocales($mailbox)
    {
        $meta_settings = $mailbox->meta['eup'] ?? [];
        return $meta_settings['locales'] ?? [];
    }

    public static function getLocale()
    {
        return session()->get('enduserportal.locale') ?: config('app.locale');
    }

    public static function urlToSwitchLocale($locale)
    {
        $url = \Request::getRequestUri();

        $url = $url.'?eup_locale='.$locale;

        return $url;
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->registerTranslations();
    }

    /**
     * Register config.
     *
     * @return void
     */
    protected function registerConfig()
    {
        $this->publishes([
            __DIR__.'/../Config/config.php' => config_path('enduserportal.php'),
        ], 'config');
        $this->mergeConfigFrom(
            __DIR__.'/../Config/config.php', 'enduserportal'
        );
    }

    /**
     * Register views.
     *
     * @return void
     */
    public function registerViews()
    {
        $viewPath = resource_path('views/modules/enduserportal');

        $sourcePath = __DIR__.'/../Resources/views';

        $this->publishes([
            $sourcePath => $viewPath
        ],'views');

        $this->loadViewsFrom(array_merge(array_map(function ($path) {
            return $path . '/modules/enduserportal';
        }, \Config::get('view.paths')), [$sourcePath]), 'enduserportal');
    }

    /**
     * Register translations.
     *
     * @return void
     */
    public function registerTranslations()
    {
        $this->loadJsonTranslationsFrom(__DIR__ .'/../Resources/lang');
    }

    /**
     * Register an additional directory of factories.
     * @source https://github.com/sebastiaanluca/laravel-resource-flow/blob/develop/src/Modules/ModuleServiceProvider.php#L66
     */
    public function registerFactories()
    {
        if (! app()->environment('production')) {
            app(Factory::class)->load(__DIR__ . '/../Database/factories');
        }
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [];
    }
}
