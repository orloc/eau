<?php
/*
The MIT License (MIT)

Copyright (c) 2014 eve-seat

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.
*/

namespace Seat\EveApi\Eve;

use DB;
use Seat\EveApi\BaseApi;
use App\Services\Settings\SettingHelper as Settings;
use TeamSpeak3;

class TeamspeakTransfer extends BaseApi
{
    protected $tsClient;

    public function __construct(){
        $this->tsClient = TeamSpeak3::factory("serverquery://serveradmin:avcu8twj@178.62.121.126:10011/?server_port=9987");
    }

    protected function getValidCorps($users){
        $validCorps = [];
        $invalid = [];
        foreach($users as $u){
            $valid_keys = \SeatKey::where('user_id', $u->id)->lists('keyID');
            if (!empty($valid_keys)) {
                $corporation_affiliation = \EveAccountAPIKeyInfoCharacters::whereIn('keyID', $valid_keys)->groupBy('corporationID')->lists('corporationID');
                if (!empty($corporation_affiliation)) {
                    $viable = \EveCorporationCorporationSheet::whereIn('corporationID', $corporation_affiliation)->lists('corporationName');
                    if (!empty($viable)){
                        $validCorps[] = [
                            'corps' => $viable,
                            'user' => $u,
                            'user_groups' => \Auth::getUserGroups($u)
                        ];
                    }
                }
            } else { 
                $invalid[] = $u;

            }
        }

        return [ $validCorps, $invalid ];
    }

    protected function addClient($server_group, $user) { 
        $alreadyin = false;
        $client_list = $server_group->clientList();
        foreach ($client_list as $client) {
            if ($client["client_unique_identifier"] == $user->tsid){
                $alreadyin = true;
            }
        }
        if (!$alreadyin){ 
            try {
                $usr_client = $this->tsClient->clientFindDb($user->tsid, true);
                if (!empty($usr_client))
                    $server_group->clientAdd($usr_client);
            } catch (TeamSpeak3_Adapter_ServerQuery_Exception $e) {}
        }
    }

    protected function groupCompare($group, $sG){
        if ($sG->__toString() == $group->name 
            && $sG->__toString() != "Guest Server Query" 
            && $sG->__toString() != "Admin Server Query" 
            && $sG->__toString() != "Server Admin"
            && $sG->__toString() != "Guest"
            && $sG->__toString() != "epicness"
            && $sG->__toString() != "Normal")
        {
            return true;
        }
        return false;
    }

    protected function seatGroupCompare($sG){
        if ($sG->__toString() != "Guest Server Query"
            && $sG->__toString() != "Admin Server Query"
            && $sG->__toString() != "Server Admin"
            && $sG->__toString() != "Guest"
            && $sG->__toString() != "epicness"
            && $sG->__toString() != "Admin"
            && $sG->__toString() != "Normal")
        {
            return true;
        }
        return false;
    }

    protected function updateSeatGroup($server_group, $group, $user){
        if ($group->name == $server_group->__toString())
        {
            $client_list = $server_group->clientList();
            foreach ($client_list as $client)
            {
                if ($client["client_unique_identifier"] == $user->tsid)
                {
                    $user_groups = \Auth::getUserGroups($user);
                    $allowed = false;
                    foreach ($user_groups as $group)
                    {
                        if ($group->name == $server_group->__toString())
                        {
                            $allowed = true;
                        }
                    }
                    if ($allowed == false)
                    {
                        try {
                            $usr_client = self::$tsClient->clientFindDb($user->tsid, true);
                            if (!empty($usr_client))
                                $server_group->clientDel($usr_client);
                        } catch (Exception $e) {}
                    }
                }
            }
        }

    }

    protected function updateServerGroup($uGroup, $sg, $user){
        if ($this->groupCompare($uGroup, $sg )) { 
            $client_list = $sg->clientList();
            $ingroup = false;
            foreach ($client_list as $client) {
                if ($client["client_unique_identifier"] == $user->tsid) {
                    $ingroup = true;
                }
            }
            if ($ingroup == false) {
                try {
                    $usr_client = self::$tsClient->clientFindDb($user->tsid, true);
                    if (!empty($usr_client)) {
                        $sg->clientAdd($usr_client);
                    }
                } catch (Exception $e) {}
            }
        }
    }

    public function Update()
    {
        parent::bootstrap();
        
        $server_groups = $this->tsClient->serverGroupList();
        
        $users = \User::all();
        $seat_groups = \Auth::findAllGroups();
        $corps = \EveCorporationCorporationSheet::all();

        list($validCorps, $invalidUsers) = $this->getValidCorps($users);

        foreach ($server_groups as $sg) { 
            foreach ($validCorps as $c){
                $corp = array_shift($c['corps']);
                $groups = $c['user_groups'];
                if ($sg->toString() === $corp) {
                    $this->addClient($sg, $c['user']);
                }

                foreach ($groups as $uGroup) { 
                    $this->updateServerGroup($uGroup, $sg, $c['user']);
                }

                if ($this->seatGroupCompare($sg)){
                    foreach ($seat_groups as $seatGroup) { 
                        $this->updateSeatGroup($sg, $seatGroup, $c['user']);
                    }
                }
                
            }

            $client_list = $sg->clientList();

            foreach ($invalidUsers as $user){
                foreach ($client_list as $client)
                {
                    if ($client["client_unique_identifier"] == $user->tsid)
                    {
                        try {
                            $usr_client = self::$tsClient->clientFindDb($user->tsid, true);
                            if (!empty($usr_client))
                                $sg->clientDel($usr_client);
                        } catch (Exception $e) {}
                    }
                }

                if ($sg->__toString() != "Guest Server Query" 
                    && $sg->__toString() != "Admin Server Query" 
                    && $sg->__toString() != "Server Admin"
                    && $sg->__toString() != "Guest"
                    && $sg->__toString() != "epicness"
                    && $sg->__toString() != "Friends"
                    && $sg->__toString() != "Normal")
                {
                    $client_list = $sg->clientList();
                    foreach ($client_list as $client)
                    {
                        if ($client["client_unique_identifier"] == $user->tsid)
                        {
                            try {
                                $sg->clientDel($client["cldbid"]);
                            } catch (Exception $e) {}
                        }
                    }
                }
            }
        }
    }
}
