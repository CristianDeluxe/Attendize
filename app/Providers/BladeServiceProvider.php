<?php

namespace App\Providers;

use App\Models\Organiser;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use JavaScript;

class BladeServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        Blade::directive('money', function ($expression) {
            return "<?php echo number_format($expression, 2); ?>";
        });

        /*
         * Share the organizers across all views
         */
        View::share('organisers', Organiser::scope()->get());

        /*
         * Set up JS across all views
         */
        JavaScript::put([
            'User'                => [
                'full_name'    => Auth::user()->full_name,
                'email'        => Auth::user()->email,
                'is_confirmed' => Auth::user()->is_confirmed,
            ],
            'DateTimeFormat'      => config('attendize.default_date_picker_format'),
            'DateSeparator'       => config('attendize.default_date_picker_seperator'),
            'GenericErrorMessage' => trans("Controllers.whoops"),
        ]);
    }
}
