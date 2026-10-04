<?php

namespace Modules\Network\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class NetworkExportController extends Controller
{
    /**
     * Generate and download MikroTik setup scripts or router configurations.
     */
    public function downloadConfig($nas)
    {
        // Placeholder configuration script generation
        $scriptContent = "# Custom ISP Multi-Tenant Router Configuration\n" .
                         "# Generated for NAS ID: " . e($nas) . "\n" .
                         "/interface wireless cap set enabled=yes\n" .
                         "/ip hotspot profile add name=isp_hotspot login-by=http-chap,cookie\n";

        return response($scriptContent, 200, [
            'Content-Type' => 'text/plain',
            'Content-Disposition' => 'attachment; filename="mikrotik_nas_' . $nas . '_config.rsc"',
        ]);
    }
}