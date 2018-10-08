<?php

namespace App\Commands;

use Illuminate\Console\Scheduling\Schedule;
use LaravelZero\Framework\Commands\Command;

class VpnDockerFixDefaultRoutingCommand extends BaseCommand
{
    protected $signature = 'vpn-docker-fix-default-routing';

    protected $description = 'Fix the default routing when using openvpn and expressvpn';

    public function handle()
    {
        if(!$this->amIRoot()) {
            $this->error('You need to be root to run this command. Try running it with sudo');
            return 255;
        }

        if($this->isVPNActive()) {
            $this->info('Your vpn is connected');
            $this->info('VPN IP: ' . $this->getVPNIp());
            $this->info('VPN PEER: ' . $this->getVPNPeer());
            
            $this->info('Adding default route...');
            $this->addDefaultVPNRoute();

            $this->info('Removing old default route...');
            $this->removeDefaultVPNRoutes();
        }
    }

    public function isVPNActive()
    {
        if($this->getVPNIp()) { 
            return TRUE;
        }

        return FALSE;
    }

    public function addDefaultVPNRoute()
    {
        $ip = $this->getVPNPeer();
        if($ip) {
            exec($this->getCommand('ip') . ' route add default via ' . $ip);
            return TRUE;
        }

        return FALSE;
    }

    public function removeDefaultVPNRoutes()
    {
        $ip = $this->getVPNPeer();
        if($ip) {
            exec($this->getCommand('ip') . ' route del 0.0.0.0/1 via ' . $ip);
            exec($this->getCommand('ip') . ' route del 128.0.0.0/1 via ' . $ip);
            return TRUE;
        }

        return FALSE;
    }

    public function getVPNIp() 
    {
        $out = trim(exec($this->getCommand('ip') . ' addr ls dev tun0 | ' . $this->getCommand('grep') . ' inet'));
        if(preg_match("/inet\s+(.*)\s+peer/", $out, $MAT)) {
            return $MAT[1];
        }

        return NULL;
    }

    public function getVPNPeer() 
    {
        $out = trim(exec($this->getCommand('ip') . ' addr ls dev tun0 | ' . $this->getCommand('grep') . ' inet'));
        if(preg_match("/inet\s+(.*)\s+peer\s+(.*)\/(\d+)/", $out, $MAT)) {
            return $MAT[2];
        }

        return NULL;
    }
}
