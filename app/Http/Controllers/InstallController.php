<?php

namespace App\Http\Controllers;

use App\Models\User;
use GuzzleHttp\Client as HttpClient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class InstallController extends Controller
{
    /**
     * Show the Welcome page.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        return view('install.welcome');
    }

    /**
     * Show the Requirements page.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function requirements()
    {
        $requirements = config('install.extensions');

        $results = [];
        // Check the requirements
        foreach ($requirements as $type => $extensions) {
            if (strtolower($type) == 'php') {
                foreach ($requirements[$type] as $extensions) {
                    $results['extensions'][$type][$extensions] = true;

                    if (!extension_loaded($extensions)) {
                        $results['extensions'][$type][$extensions] = false;

                        $results['errors'] = true;
                    }
                }
            } elseif (strtolower($type) == 'apache') {
                foreach ($requirements[$type] as $extensions) {
                    // Check if the function exists
                    // Prevents from returning a false error
                    if (function_exists('apache_get_modules')) {
                        $results['extensions'][$type][$extensions] = true;

                        if (!in_array($extensions, apache_get_modules())) {
                            $results['extensions'][$type][$extensions] = false;

                            $results['errors'] = true;
                        }
                    }
                }
            }
        }

        // If the current php version doesn't meet the requirements
        if (version_compare(PHP_VERSION, config('install.php_version')) == -1) {
            $results['errors'] = true;
        }

        return view('install.requirements', ['results' => $results]);
    }

    /**
     * Show the Permissions page.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function permissions()
    {
        $permissions = config('install.permissions');

        $results = [];
        foreach ($permissions as $type => $files) {
            foreach ($files as $file) {
                if (is_writable(base_path($file))) {
                    $results['permissions'][$type][$file] = true;
                } else {
                    $results['permissions'][$type][$file] = false;
                    $results['errors'] = true;
                }
            }
        }

        return view('install.permissions', ['results' => $results]);
    }

    /**
     * Show the Database configuration page.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function database()
    {
        return view('install.database');
    }

    /**
     * Show the Admin credentials page.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function account()
    {
        return view('install.account');
    }

    /**
     * Show the Complete page.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function complete()
    {
        return view('install.complete');
    }

    /**
     * Validate the database credentials, and write the .env config file.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function storeConfig(Request $request)
    {
        $request->validate(
            [
                'database_hostname' => ['required', 'string', 'max:50'],
                'database_port' => ['required', 'numeric'],
                'database_name' => ['required', 'string', 'max:50'],
                'database_username' => ['required', 'string', 'max:50'],
                'database_password' => ['nullable', 'string', 'max:50'],
            ]
        );

        $validateDatabase = $this->validateDatabaseCredentials($request);
        if ($validateDatabase !== true) {
            return back()->with('error', lang('Invalid database credentials. ') . $validateDatabase)->withInput();
        }

        $validateConfigFile = $this->writeEnvFile($request);
        if ($validateConfigFile !== true) {
            return back()->with('error', lang('Unable to save .env file, check file permissions. ') . $validateConfigFile)->withInput();
        }

        return redirect()->route('install.account');
    }

    /**
     * Migrate the database, and add the default admin user.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function storeDatabase(Request $request)
    {
        $request->validate(
            [
                'firstname' => ['required', 'string', 'max:50'],
                'lastname' => ['required', 'string', 'max:50'],
                'username' => ['required', 'string', 'min:2', 'max:50'],
                'email' => ['required', 'string', 'email', 'max:100'],
                'password' => ['required', 'string', 'min:8', 'max:128', 'confirmed'],
            ]
        );

        $migrateDatabase = $this->migrateDatabase();
        if ($migrateDatabase !== true) {
            return back()->with('error', lang('Failed to migrate the database. ') . $migrateDatabase)->withInput();
        }

        $createDefaultUser = $this->createDefaultUser($request);
        if ($createDefaultUser !== true) {
            return back()->with('error', lang('Failed to create the default user. ') . $createDefaultUser)->withInput();
        }

        $saveInstalledFile = $this->writeEnvInstalledStatus();
        if ($saveInstalledFile !== true) {
            return back()->with('error', lang('Failed to finalize the installation. ') . $saveInstalledFile)->withInput();
        }

        return redirect()->route('install.complete');
    }

    /**
     * Validate the database credentials.
     *
     * @return bool|string
     */
    private function validateDatabaseCredentials(Request $request)
    {
        $settings = config("database.connections.mysql");

        config([
            'database' => [
                'default' => 'mysql',
                'connections' => [
                    'mysql' => array_merge($settings, [
                        'driver' => 'mysql',
                        'host' => $request->input('database_hostname'),
                        'port' => $request->input('database_port'),
                        'database' => $request->input('database_name'),
                        'username' => $request->input('database_username'),
                        'password' => $request->input('database_password'),
                    ]),
                ],
            ],
        ]);

        DB::purge();

        try {
            DB::connection()->getPdo();

            return true;
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     * Migrate the database.
     *
     * @return bool|string
     */
    private function migrateDatabase()
    {
        try {
            Artisan::call('migrate', ['--force' => true]);

            return true;
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     * Create the default admin user.
     *
     * @return bool|string
     */
    private function createDefaultUser(Request $request)
    {
        try {
            $ipInfo = user_ip_info();
            $country_name = $ipInfo->location->country;

            $user = User::create([
                'user_type' => 'admin',
                'name' => $request->input('firstname') . ' ' . $request->input('firstname'),
                'firstname' => $request->input('firstname'),
                'lastname' => $request->input('lastname'),
                'username' => $request->input('username'),
                'email' => $request->input('email'),
                'address' => ['address' => '', 'city' => '', 'state' => '', 'zip' => '', 'country' => $country_name],
                'avatar' => 'default.png',
                'password' => Hash::make($request->input('password')),
            ]);

            $user->markEmailAsVerified();
        } catch (\Exception $e) {
            return $e->getMessage();
        }

        return true;
    }

    /**
     * Write the .env file.
     *
     * @return bool|string
     */
    private function writeEnvFile(Request $request)
    {
        try {
            set_env('APP_KEY', 'base64:'.base64_encode(Str::random(32)));
            set_env('APP_URL', route('home'));
            set_env('APP_DEBUG', 'false');
            set_env('DB_HOST', $request->input('database_hostname'));
            set_env('DB_PORT', $request->input('database_port'));
            set_env('DB_DATABASE', $request->input('database_name'));
            set_env('DB_USERNAME', $request->input('database_username'));
            set_env('DB_PASSWORD', $request->input('database_password'));
        } catch (\Exception $e) {
            return $e->getMessage();
        }

        return true;
    }

    /**
     * Write the installed status to the .env file.
     *
     * @return bool|string
     */
    private function writeEnvInstalledStatus()
    {
        try {
            set_env('APP_INSTALLED', 'true');
        } catch (\Exception $e) {
            return $e->getMessage();
        }

        return true;
    }
}
