<?php

namespace RachidLaasri\LaravelInstaller\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use RachidLaasri\LaravelInstaller\Events\EnvironmentSaved;
use RachidLaasri\LaravelInstaller\Helpers\InstalledFileManager;


class SettingsController extends Controller
{
    static $MARIA_DB = '10.2';
    static $MYSQL = '5.7.8';

    private function checkDatabaseConnection()
    {
        try {
            return DB::connection()->getPdo();
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Display the installer welcome page.
     *
     * @return \Illuminate\Http\Response
     */
    public function settings()
    {
        $engine = '';
        $results = DB::select(DB::raw("select version()"));
        $mysql_version = $results[0]->{'version()'};
        $mariadb_version = '';
        $pass = false;

        if (strpos($mysql_version, 'Maria') !== false) {
            $engine = 'MariaDB';
            $mysql_version = str_replace('-MariaDB', '', $mysql_version);
            $pass = (floatval($mysql_version) >= floatval(self::$MARIA_DB));
        } else {
            $engine = 'Mysql';
            $mysql_version = str_replace('-Mysql', '', $mysql_version);
            $pass = (floatval($mysql_version) >= floatval(self::$MYSQL));
        }
        $data = [
            'engine' => $engine,
            'version' => $mysql_version,
            'pass' => $pass
        ];

        return view('vendor.installer.settings', ['engine' => $data]);
    }

    private function saveSetting($fields)
    {
        $fileManager = new InstalledFileManager();
        $fiels = $fields->valid();
        $raw = $fields->valid();
        unset($raw['_token']);
        $raw = [
            'limit_item_shopping_cart' => $raw['app_limit_shopping'],
            'currency' => $raw['app_currency'],
            'feed_percentage' => $raw['app_feed'],
            'feed_amount' => $raw['app_feed_amount'],
            'feed_limit_price' => $raw['app_feed_limit'],
            'delivery_feed_min' => $raw['app_delivery'],
            'delivery_feed_tax' => $raw['app_delivery_tax'],
            'countries_available' => $raw['app_delivery_countries'],
            'stripe_auth_redirect' => $raw['app_redirect'],
            'search_range_km' => $raw['app_search_range'],
            'discount_to_supplier' => $raw['app_discount_supplier'],
            'auto_delivery' => $raw['app_delivery_auto'],
            'auto_sms' => $raw['app_sms'],
            'range_closed' => $raw['app_range_closed'],
            'google_vision' => $raw['app_google_vision'],
            'only_user_confirmed' => $raw['app_user_confirmed'],
            'marketplace' => $raw['app_market_place'],
            'schedule_active' => $raw['app_schedule'],
            'edge_time' => $raw['app_edge_time'],
        ];

        foreach ($raw as $key => $value) {
            DB::table('settings')
                ->where('meta', $key)
                ->update(['value' => $value]);
        }

        $finalStatusMessage = $fileManager->update();
    }

    public function saveWizard(Request $request, Redirector $redirect)
    {
        $rules = config('installer.settings.form.rules');
        $messages = [
            'environment_custom.required_if' => trans('installer_messages.environment.wizard.form.name_required'),
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return $redirect->route('LaravelInstaller::settings')->withInput()->withErrors($validator->errors());
        }

        if (!$this->checkDatabaseConnection()) {
            return $redirect->route('LaravelInstaller::environmentWizard')->withInput()->withErrors([
                'database_connection' => trans('installer_messages.environment.wizard.form.db_connection_failed'),
            ]);
        }

        $results = $this->saveSetting($validator);

//        event(new EnvironmentSaved($request));
//
        return $redirect->route('LaravelInstaller::welcome')
            ->with(['results' => $results]);
    }
}
