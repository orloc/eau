<?php

namespace AppBundle\Command;

use AppBundle\Entity\AveragePrice;
use AppBundle\Entity\Corporation;
use AppBundle\Entity\Starbase;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\Input;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Validator\Constraints\DateTime;

class SlackNotificationCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('eau:slack:notifications')
            ->addArgument('corp', InputArgument::REQUIRED, 'The corporation to query data against' )
            ->setDescription('Polls the api for various events to notify in game.');
    }

    protected function execute(InputInterface $input, OutputInterface $output) {

        $corpName = $input->getArgument('corp');
        $orm = $this->getContainer()->get('doctrine');
        
        $corporation = $orm->getRepository('AppBundle:Corporation')
            ->findByCorpName(ucfirst(str_replace('-', ' ', $corpName)));
        
        if (!$corporation instanceof Corporation) {
            $output->writeln('Corporation does not exist');
            return;
        }
        
        $bases = $this->getContainer()->get('app.starbase.manager')
            ->getUpdatedStarbaseList($corporation);
        
        $onlineBases = array_filter($bases, function($base) {
            return ($base->getState() === Starbase::STATE_ONLINE) === true;
        });
        

        $needsFuel = [];
        foreach ($onlineBases as $base){
            $fuel = array_filter($base->getFuel(), function($item){
                return $item['typeID'] !== "16275"; // stront
            });
            $perCycle = $base->getDescriptors()['fuel_consumption']['quantity'];
            $remaining = array_shift($fuel)['quantity'] / $perCycle;
            $offlineThreshold = null; // days
            
            if (($remaining / 24) <= 2) { 
                array_push($needsFuel, [$base, $remaining]);
            }
        }

        if (!empty($needsFuel)) {
            $client = new Client();
            $client->request('POST', $this->getContainer()->getParameter('slack_webhook_url'), [
                'json' => [
                    "username" => "EVE Bot",
                    "attachments" => $this->getAttachments($needsFuel)
                ]
            ]);
        }
    }
    
    protected function getAttachments(array $stations) {
        $attachments = [];
        foreach ($stations as $s){
            array_push($attachments, [
                'title' => 'Low Fuel Notification',
                'ts' => time(),
                'color' => $s[1] < 48 ? 'danger' : 'warning',
                'fields' => $this->generateFields($s)
            ]);
        }

        return $attachments;
    }
    
    protected function generateFields(array $s){
        list($station, $remaining) = $s;
        $moon = $station->getDescriptors()['moon'];
        $type = $station->getDescriptors()['name'];
        return [
            [
                'title' => 'Location',
                'value' => $moon['itemName'],
                'short' => true
            ],
            [
                'title' => 'Tower Type',
                'value' => $type,
                'short' => true
            ],
            [
                'title' => 'Fuel Remaining (hours)',
                'value' => $remaining,
                'short' => true
            ]
        ];
    }
}
