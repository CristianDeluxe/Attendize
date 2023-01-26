<?php

namespace App\Http\Controllers;

use App\Models\Timezone;
use Exception;
use GuzzleHttp\Client;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\MessageBag;
use Illuminate\Support\Str;
use Utils;

class InstallerController extends Controller
{

    /**
     * @var array Stores the installation data
     */
    protected $installation_data = [];

    /**
     * @var array Stores the installer data
     */
    private $data;


    protected function constructInstallerData(): void
    {
        $this->addDefaultConfig();
        $this->addWritablePaths();
        $this->addPHPExtensions();
        $this->addPHPOptionalExtensions();
    }

    /**
     * Show the application installer
     *
     * @return mixed
     */
    public function showInstaller()
    {
        /**
         * If we're already installed display user friendly message and direct them to the appropriate next steps.
         *
         * @todo Check if DB is installed etc.
         * @todo Add some automated checks to see exactly what the state of the install is. Potentially would be nice to
         *       allow the user to restart the install process
         */
        $this->constructInstallerData();

        if (self::isInstalled()) {
            return view('Installer.AlreadyInstalled', $this->data);
        }

        return view('Installer.Installer', $this->data);
    }

    /**
     * Attempts to install the system
     *
     * @param  Request  $request
     * @return JsonResponse|RedirectResponse
     */
    public function postInstaller(Request $request)
    {
        //  Do not run the installation if it is already installed
        if (self::isInstalled()) {

            // Return 409 Conflict HTTP Code and a friendly message
            abort(409, trans('Installer.setup_completed_already_message'));
        }

        // Increase PHP time limit
        set_time_limit(300);

        // Check if we just have to test the database for JSON before doing anything else
        if ($request->get('test') === 'db') {
            return $this->checkDatabaseJSON($request);
        }

        // Construct the data for installer
        $this->constructInstallerData();

        // If the database settings are invalid, return to the installer page.
        if (!$this->validateConnectionDetails($request)) {
            return view('Installer.Installer', $this->data);
        }

        // Get and store data for installation
        $this->getInstallationData($request);

        // If a user doesn't use the default database details, enters incorrect values in the form, and then proceeds
        // the installation fails miserably. Rather check if the database connection details are valid and fail
        // gracefully
        if (!$this->testDatabase($this->installation_data['database'])) {
            return view('Installer.Installer', $this->data)->withErrors(
                new MessageBag(['Database connection failed. Please check the details you have entered and try again.']));
        }

        // Writes the new env file
        $this->writeEnvFile();

        // Force laravel to regenerate a new key (see key:generate sources)
        Config::set('app.key', $this->installation_data['app_key']);
        Artisan::call('key:generate', ['--force' => true]);

        // Run the migration
        Artisan::call('migrate', ['--force' => true]);
        if (Timezone::count() === 0) {
            Artisan::call('db:seed', ['--force' => true]);
        }

        // Create the "installed" file
        $this->createInstalledFile();

        // Reload the configuration file (very useful if you use php artisan serve)
        Artisan::call('config:clear');

        return redirect()->route('showSignup', ['first_run' => 'yup']);
    }

    /**
     * Get data needed before upgrading the system
     *
     * @return array
     */
    protected function constructUpgraderData()
    {
        $data = [
            'remote_version' => null,
            'local_version' => null,
            'installed_version' => null,
            'upgrade_done' => false
        ];

        try {
            $http_client = new Client();
            $response = $http_client->get('https://raw.githubusercontent.com/Attendize/Attendize/master/VERSION');
            $data["remote_version"] = Utils::parse_version((string)$response->getBody());
        } catch (\Exception $exception) {
            \Log::warn("Error retrieving the latest Attendize version. InstallerController.getVersionInfo()");
            \Log::warn($exception);
        }

        $data["local_version"] = trim(file_get_contents(base_path('VERSION')));
        $data["installed_version"] = trim(file_get_contents(base_path('installed')));
        return $data;
    }

    /**
     * Show the application upgrader
     *
     * @return mixed
     */
    public function showUpgrader()
    {
        /**
         * If we haven't yet installed, redirect to installer page.
         */

        if (!self::isInstalled()) {
            $this->constructInstallerData();
            return view('Installer.Installer', $this->data);
        }

        $data = $this->constructUpgraderData();

        return view('Installer.Upgrader', $data);
    }

    /**
     * Attempts to upgrade the system
     *
     * @param  Request  $request
     * @return JsonResponse|RedirectResponse
     */
    public function postUpgrader(Request $request)
    {
        //  Do not run the installation if it is already installed
        if (!$this->canUpgrade()) {
            // Return 409 Conflict HTTP Code and a friendly message
            abort(409, trans('Installer.no_updgrade'));
        }

        // Increase PHP time limit
        set_time_limit(300);

        // Run any migrations
        Artisan::call('migrate', ['--force' => true]);

        // Update the "installed" file with latest version
        $this->createInstalledFile();

        // Reload the configuration file (very useful if you use php artisan serve)
        Artisan::call('config:clear');

        $data = $this->constructUpgraderData();
        $data["upgrade_done"] = true;

        return view('Installer.Upgrader', $data);
    }

    /**
     * Adds default config values for the view blade to use
     */
    protected function addDefaultConfig(): void
    {
        $database_default = Config::get('database.default');
        $this->data['default_config'] = [
            'application_url'   => Config::get('app.url'),
            'database_type'     => $database_default,
            'database_host'     => Config::get('database.connections.' . $database_default . '.host'),
            'database_name'     => Config::get('database.connections.' . $database_default . '.database'),
            'database_username' => Config::get('database.connections.' . $database_default . '.username'),
            'database_password' => Config::get('database.connections.' . $database_default . '.password'),
            'mail_from_address' => Config::get('mail.from.address'),
            'mail_from_name'    => Config::get('mail.from.name'),
            'mail_driver'       => Config::get('mail.driver'),
            'mail_port'         => Config::get('mail.port'),
            'mail_encryption'   => Config::get('mail.encryption'),
            'mail_host'         => Config::get('mail.host'),
            'mail_username'     => Config::get('mail.username'),
            'mail_password'     => Config::get('mail.password')
        ];
    }

    /**
     * Adds to the checks the paths that should be writable
     */
    protected function addWritablePaths(): void
    {
        $this->data['paths'] = [
            storage_path('app'),
            storage_path('framework'),
            storage_path('logs'),
            public_path(config('attendize.event_images_path')),
            public_path(config('attendize.organiser_images_path')),
            public_path(config('attendize.event_pdf_tickets_path')),
            base_path('bootstrap/cache'),
            base_path('.env'),
            base_path(),
        ];
    }

    /**
     * Adds to the checks the required PHP extensions
     */
    protected function addPHPExtensions(): void
    {
        $this->data['requirements'] = [
            'openssl',
            'pdo',
            'mbstring',
            'fileinfo',
            'tokenizer',
            'gd',
            'zip',
        ];
    }

    /**
     * Adds to the checks the optional PHP extensions
     */
    protected function addPHPOptionalExtensions(): void
    {
        $this->data['optional_requirements'] = [
            'pdo_mysql',
            'pdo_pgsql',
        ];

        $database_default = Config::get('database.default');
        $this->data['default_config'] = [
            'application_url'   => Config::get('app.url'),
            'database_type'     => $database_default,
            'database_host'     => Config::get('database.connections.' . $database_default . '.host'),
            'database_name'     => Config::get('database.connections.' . $database_default . '.database'),
            'database_username' => Config::get('database.connections.' . $database_default . '.username'),
            'database_password' => Config::get('database.connections.' . $database_default . '.password'),
            'mail_from_address' => Config::get('mail.from.address'),
            'mail_from_name'    => Config::get('mail.from.name'),
            'mail_driver'       => Config::get('mail.driver'),
            'mail_port'         => Config::get('mail.port'),
            'mail_encryption'   => Config::get('mail.encryption'),
            'mail_host'         => Config::get('mail.host'),
            'mail_username'     => Config::get('mail.username'),
            'mail_password'     => Config::get('mail.password')
        ];
    }

    /**
     * Check if Attendize is already installed
     *
     * @return bool true if installed false if not
     */
    public static function isInstalled(): bool
    {
        return file_exists(base_path('installed'));
    }

    /**
     * Check if an upgrade is possible
     *
     * @return bool true if upgrade possible false if not
     */
    public function canUpgrade(): bool
    {
        $data = $this->constructUpgraderData();
        if (
        (version_compare($data['local_version'], $data['remote_version']) === -1) ||
        (
            version_compare($data['local_version'], $data['remote_version']) === 0 &&
            version_compare($data['installed_version'], $data['local_version']) === -1
        )) {
            return true;
        }
        return false;
    }

    /**
     * Checks the database and returns a JSON response with the result
     *
     * @param  Request  $request
     * @return JsonResponse
     */
    protected function checkDatabaseJSON(Request $request): JsonResponse
    {
        // If we can connect to database, return a success message
        if ($this->validateConnectionDetails($request) && $this->testDatabase($this->getDatabaseData($request))) {
            return response()->json([
                'status'  => 'success',
                'message' => trans('Installer.connection_success'),
                'test'    => 1,
            ]);
        }

        // If the database configuration is invalid or we cannot connect to the database, return an error message
        return response()->json([
            'status'  => 'error',
            'message' => trans('Installer.connection_failure'),
            'test'    => 1,
        ]);
    }

    /**
     * Checks whether the database connection data in the request is valid.
     *
     * @param  Request  $request
     * @return bool
     */
    protected function validateConnectionDetails(Request $request): bool
    {
        try {
            $this->validate($request, [
                'database_type'     => 'required',
                'database_host'     => 'required',
                'database_name'     => 'required',
                'database_username' => 'required',
                'database_password' => 'required'
            ]);
            return true;
        } catch (Exception $e) {
            Log::error('Please enter all app settings. '.$e->getMessage());
        }
        return false;
    }

    /**
     * Test the database connection
     *
     * @param $database
     * @return bool
     */
    private function testDatabase($database): bool
    {
        // Replaces database configuration
        Config::set('database.default', $database['type']);
        Config::set('database.connections.'.$database['type'].'.host', $database['host']);
        Config::set('database.connections.'.$database['type'].'.database', $database['name']);
        Config::set('database.connections.'.$database['type'].'.username', $database['username']);
        Config::set('database.connections.'.$database['type'].'.password', $database['password']);

        // Try to connect
        try {
            DB::reconnect();
            $pdo = DB::connection()->getPdo();
            if (!empty($pdo)) {
                return true;
            }
        } catch (Exception $e) {
            Log::error('Database connection details invalid'.$e->getMessage());
        }

        return false;
    }

    /**
     * Get the database data from request
     *
     * @param  Request  $request
     * @return array
     */
    protected function getDatabaseData(Request $request): array
    {
        return [
            'type'     => $request->get('database_type'),
            'host'     => $request->get('database_host'),
            'name'     => $request->get('database_name'),
            'username' => $request->get('database_username'),
            'password' => $request->get('database_password')
        ];
    }

    /**
     * @param  Request  $request
     */
    protected function getInstallationData(Request $request): void
    {
        // Create the database data array
        $this->installation_data['database'] = $this->getDatabaseData($request);

        // Create the mail data array
        $this->installation_data['mail'] = $this->getMailData($request);

        $this->installation_data['app_url'] = $request->get('app_url');
        $this->installation_data['app_key'] = Str::random(32);
    }

    /**
     * @param  Request  $request
     * @return array
     */
    protected function getMailData(Request $request): array
    {
        return [
            'driver'       => $request->get('mail_driver'),
            'port'         => $request->get('mail_port'),
            'username'     => $request->get('mail_username'),
            'password'     => $request->get('mail_password'),
            'encryption'   => $request->get('mail_encryption'),
            'from_address' => $request->get('mail_from_address'),
            'from_name'    => $request->get('mail_from_name'),
            'host'         => $request->get('mail_host')
        ];
    }

    /**
     * Write the .env file
     */
    protected function writeEnvFile(): void
    {
        // Get the example env data
        $example_env = file_get_contents(base_path().DIRECTORY_SEPARATOR.'.env.example');
        $env_lines = explode(PHP_EOL, $example_env);

        // Parse each line
        foreach ($env_lines as $key => $line) {
            $env_lines[$key] = explode('=', $line, 2);
        }

        // Installer data to be stored in the new env file
        $config = [
            'APP_ENV'           => 'production',
            'APP_DEBUG'         => 'false',
            'APP_URL'           => $this->installation_data['app_url'],
            'APP_KEY'           => $this->installation_data['app_key'],
            'DB_TYPE'           => $this->installation_data['database']['type'],
            'DB_HOST'           => $this->installation_data['database']['host'],
            'DB_DATABASE'       => $this->installation_data['database']['name'],
            'DB_USERNAME'       => $this->installation_data['database']['username'],
            'DB_PASSWORD'       => $this->installation_data['database']['password'],
            'MAIL_DRIVER'       => $this->installation_data['mail']['driver'],
            'MAIL_PORT'         => $this->installation_data['mail']['port'],
            'MAIL_ENCRYPTION'   => $this->installation_data['mail']['encryption'],
            'MAIL_HOST'         => $this->installation_data['mail']['host'],
            'MAIL_USERNAME'     => $this->installation_data['mail']['username'],
            'MAIL_FROM_NAME'    => $this->installation_data['mail']['from_name'],
            'MAIL_FROM_ADDRESS' => $this->installation_data['mail']['from_address'],
            'MAIL_PASSWORD'     => $this->installation_data['mail']['password'],
        ];

        // Merge new config data with example env
        foreach ($config as $key => $val) {
            $replaced_line = false;

            // Check if config already exist in example env and replace it
            foreach ($env_lines as $line_number => $line) {
                if ($line[0] === $key) {
                    $env_lines[$line_number][1] = $val;
                    $replaced_line = true;
                }
            }

            // If no replaced is a new config key/value set
            if (!$replaced_line) {
                $env_lines[] = [$key, $val];
            }
        }

        // Creates the new env file
        $new_env = '';
        foreach ($env_lines as $line) {
            $new_env .= implode(count($line) > 1 ? '=' : '', $line).PHP_EOL;
        }

        // Store the env file
        $fp = fopen(base_path().DIRECTORY_SEPARATOR.'.env', 'wb');
        fwrite($fp, $new_env);
        fclose($fp);
    }

    /**
     * Creates the installation file
     */
    protected function createInstalledFile(): void
    {
        $version = trim(file_get_contents(base_path('VERSION')));
        $fp = fopen(base_path().DIRECTORY_SEPARATOR.'installed', 'wb');
        fwrite($fp, $version);
        fclose($fp);
    }
}
