<?php

use Illuminate\Support\Facades\Schedule;
use Modules\Billing\Jobs\AutoSuspensionJob;

/**
 * The System "Grim Reaper"
 * This checks every 60 seconds for users whose hotspot time has expired
 * and triggers the deprovisioning logic in the Network Module.
 */
Schedule::job(new AutoSuspensionJob)->everyMinute();