<?php
namespace App\Traits\Testing; 

use Illuminate\Support\Facades\Artisan;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\RefreshDatabaseState;

trait FastRefreshDatabase{

    use RefreshDatabase;
    
    /**
    * Refresh a conventional test database.
    * 
    * @return void
    */
   protected function refreshTestDatabase()
   {

    Artisan::call('migrate:status',[]);
    $output =  Artisan::output(); 
    

    if( (stripos($output, 'pending') || stripos($output, 'Migration table not found'))
         && ! RefreshDatabaseState::$migrated
    )
    {
    
        $this->artisan('migrate:fresh', $this->migrateFreshUsing());
        
        $this->app[Kernel::class]->setArtisan(null);

        RefreshDatabaseState::$migrated = true;
        
    }
       $this->beginDatabaseTransaction();
   }

}