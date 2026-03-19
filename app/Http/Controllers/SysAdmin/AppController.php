<?php
/**
 * Created by PhpStorm.
 * User: dhfusion
 * Date: 1/29/2018
 * Time: 8:17 PM
 */

namespace App\Http\Controllers\SysAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Redirect;
use App\Http\Controllers\Traits\PermissionsGenerator;

class AppController extends Controller
{
    use PermissionsGenerator;

    public function __construct() {
        $this->middleware(['auth', 'verifier'])->except('ArtisanMigrate');
    }

    /**
     * Generate application key
     */
    public function KeyGenerate()
    {
        Artisan::call('key:generate');
    }

    /**
     * php artisan migrate
     */
    public function ArtisanMigrate()
    {
        try {
            Artisan::call('migrate',['--force' => true]);
            $this->CreatePermissions();
            $this->CreateMiddlewarePermission();
            return Redirect::to('../../index.php?migrate=2');
        } catch (Exception $e) {
            Response::make($e->getMessage(), 500);
        }
    }
    /**
     * php artisan migrate:generate
     * or php artisan migrate:generate table1,table2,table3,table4,table5
     * ignore="table3,table4,table5"
     * Run php artisan help migrate:generate for a list of options.
     */
    public function ArtisanGenerate()
    {
        //Artisan::call('migrate:generate');
        //dd('Migration Generated');
    }

    /**
     * Artisan Commands
     */
    public function ArtisanCommands()
    {
        return view('sysadmin.settings.commands');
    }
    /**
     * Artisan Commands
     */
    public function RunArtisanCommands($command)
    {
        Artisan::call(''.$command.'');
        return back()->withInput()->with('status', 'Command executed successfully!');
    }

    public function ArtisanClearCache()
    {
        Artisan::call('cache:clear');
    }

    // Create a cache file for faster configuration loading
    public function ArtisanConfigCache()
    {
        Artisan::call('config:cache');
    }

    // Remove the configuration cache file
    public function ArtisanConfigClear()
    {
        Artisan::call('config:clear');
    }
}