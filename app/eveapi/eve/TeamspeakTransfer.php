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
            }
        }

        return $validCorps;
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

    public function Update()
    {
        parent::bootstrap();
        
        $server_groups = $this->tsClient->serverGroupList();
        
        $users = \User::all();
        $seat_groups = \Auth::findAllGroups();

        $validCorps = $this->getValidCorps($users);

        foreach ($server_groups as $sg) { 
            foreach ($validCorps as $c){
                $corp = array_shift($c['corps']);
                $groups = $c['user_groups'];
                if ($sg->toString() === $corp) {
                    $this->addClient($sg, $c['user']);
                }

                foreach ($groups as $uGroup) { 
                    if ($sg->__toString() === $uGroup->name) {
                    }
                }
            }
        }
        die('here');

        foreach ($users as $user)
        {
            $valid_keys = \SeatKey::where('user_id', $user->id)->lists('keyID');
            if (!empty($valid_keys)) {
                $corporation_affiliation = \EveAccountAPIKeyInfoCharacters::whereIn('keyID', $valid_keys)->groupBy('corporationID')->lists('corporationID');
            }
            if (!empty($corporation_affiliation)) {
                $viable = \EveCorporationCorporationSheet::whereIn('corporationID', $corporation_affiliation)->lists('corporationName');
            }
            if (!empty($viable)) {
                
                foreach ($server_groups as $server_group)
                {
                    foreach ($viable as $corp)
                    {
                        if ($server_group == $corp)
                        {
                            $alreadyin = false;
                            $client_list = $server_group->clientList();
                            foreach ($client_list as $client)
                            {
                                if ($client["client_unique_identifier"] == $user->tsid)
                                {
                                    $alreadyin = true;
                                }
                            }
                            if (!$alreadyin)
                            {
                                try {
                                    $usr_client = self::$tsClient->clientFindDb($user->tsid, true);
                                    if (!empty($usr_client))
                                        $server_group->clientAdd($usr_client);
                                } catch (TeamSpeak3_Adapter_ServerQuery_Exception $e) {}
                            }
                        }
                    }
                }
                
                $user_groups = \Auth::getUserGroups($user);
                foreach ($user_groups as $group)
                {
                    foreach ($server_groups as $server_group)
                    {
                        if ($server_group->__toString() == $group->name 
                            && $server_group->__toString() != "Guest Server Query" 
                            && $server_group->__toString() != "Admin Server Query" 
                            && $server_group->__toString() != "Server Admin"
                            && $server_group->__toString() != "Guest"
                            && $server_group->__toString() != "epicness"
                            && $server_group->__toString() != "Normal")
                        {
                            $client_list = $server_group->clientList();
                            $ingroup = false;
                            foreach ($client_list as $client)
                            {
                                if ($client["client_unique_identifier"] == $user->tsid)
                                {
                                    $ingroup = true;
                                }
                            }
                            if ($ingroup == false)
                            {
                                try {
                                    $usr_client = self::$tsClient->clientFindDb($user->tsid, true);
                                    if (!empty($usr_client))
                                        $server_group->clientAdd($usr_client);
                                } catch (Exception $e) {}
                            }
                        }
                    }
                }
                
                foreach ($server_groups as $server_group)
                {
                    if ($server_group->__toString() != "Guest Server Query"
                        && $server_group->__toString() != "Admin Server Query"
                        && $server_group->__toString() != "Server Admin"
                        && $server_group->__toString() != "Guest"
                        && $server_group->__toString() != "epicness"
                        && $server_group->__toString() != "Admin"
                        && $server_group->__toString() != "Normal")
                    {
                        foreach ($seat_groups as $group)
                        {
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
                    }
                }
                
            }
            
            if (empty($valid_keys) || empty($corporation_affiliation) || empty($viable))
            {
                
                $corps = \EveCorporationCorporationSheet::all();
                foreach ($corps as $corp)
                {
                    foreach ($server_groups as $server_group)
                    {
                        if ($server_group == $corp->corporationName)
                        {
                            $client_list = $server_group->clientList();
                            foreach ($client_list as $client)
                            {
                                if ($client["client_unique_identifier"] == $user->tsid)
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
                
                foreach ($server_groups as $server_group)
                {
                    if ($server_group->__toString() != "Guest Server Query" 
                        && $server_group->__toString() != "Admin Server Query" 
                        && $server_group->__toString() != "Server Admin"
                        && $server_group->__toString() != "Guest"
                        && $server_group->__toString() != "epicness"
                        && $server_group->__toString() != "Friends"
                        && $server_group->__toString() != "Normal")
                    {
                        $client_list = $server_group->clientList();
                        foreach ($client_list as $client)
                        {
                            if ($client["client_unique_identifier"] == $user->tsid)
                            {
                                try {
                                    $server_group->clientDel($client["cldbid"]);
                                } catch (Exception $e) {}
                            }
                        }
                    }
                }
            }
        }

        return true;
    }
}
