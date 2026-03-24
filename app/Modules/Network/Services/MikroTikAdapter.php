<?php

namespace App\Modules\Network\Services;

use RouterOS\Client; // Using the 'pear2/net_routeros' or 'routeros-api' package
use RouterOS\Query;

class MikroTikAdapter
{
    protected $client;

    public function connect($ip, $user, $pass) {
        $this->client = new Client([
            'host' => $ip,
            'user' => $user,
            'pass' => $pass,
            'port' => 8728,
        ]);
    }

    public function removeHotspotActiveSession(string $mac)
    {
        // 1. Find the ID of the active session for this MAC
        $query = (new Query('/ip/hotspot/active/print'))->where('user', $mac);
        $response = $this->client->query($query)->read();

        // 2. Remove (Kick) the session if it exists
        if (!empty($response)) {
            $id = $response[0]['.id'];
            $this->client->query((new Query('/ip/hotspot/active/remove'))->equal('.id', $id))->read();
        }
    }

    public function updateHotspotUserSpeed(string $mac, $plan)
    {
        // Often, it's safer to just kick the user (removeHotspotActiveSession)
        // This forces the router to re-auth with RADIUS and pick up the new plan instantly.
        $this->removeHotspotActiveSession($mac);
    }
}