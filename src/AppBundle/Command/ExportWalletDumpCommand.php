<?php

namespace AppBundle\Command;

use Carbon\Carbon;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ExportWalletDumpCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('evetool:export_wallet_dump')
            ->setDescription('Exports wallets to a csv for excell spreadsheet');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $em = $this->getContainer()->get('doctrine.orm.entity_manager');
        $log = $this->getContainer()->get('logger');

        $corps = $em->getRepository('AppBundle:Corporation')->findAll();


        $jtRepo = $em->getRepository('AppBundle:JournalTransaction');
        $mtRepo = $em->getRepository('AppBundle:MarketTransaction');
        $accRepo = $em->getRepository('AppBundle:Account');

        $index = [];
        foreach ($corps as $c){
            $index[$c->getEveId()] = [];
            $accounts = $accRepo->findBy(['corporation' => $c]);
            foreach ($accounts as $ac){
                $index[$c->getId()][$ac->getName()] = [
                    'journal' => $jtRepo->getTransactionsByAccountInRange($ac, $this->getDateRange()),
                    'transactions' => $mtRepo->getTransactionsByAccountInRange($ac, $this->getDateRange())
                ];
            }
        }

        var_dump($index);die;
    }

    protected function getDateRange(){
        return [
            'start' => Carbon::now(),
            'end' => Carbon::create()->subDays(30)
        ];
    }
}