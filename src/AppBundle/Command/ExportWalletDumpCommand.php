<?php

namespace AppBundle\Command;

use Carbon\Carbon;
use League\Csv\Writer;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ExportWalletDumpCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('eau:export_wallet_dump')
            ->setDescription('Exports wallets to a csv for excell spreadsheet');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');
        $corps = $em->getRepository('AppBundle:Corporation')->findAll();

        $jtRepo = $em->getRepository('AppBundle:JournalTransaction');
        $mtRepo = $em->getRepository('AppBundle:MarketTransaction');
        $accRepo = $em->getRepository('AppBundle:Account');

        $index = [];
        foreach ($corps as $c) {
            $index[$c->getEveId()] = [
                'buy' => [],
                'sell' => [],
            ];
            $accounts = $accRepo->findBy(['corporation' => $c]);
            foreach ($accounts as $ac) {
                $index[$c->getEveId()][$ac->getName()] = $jtRepo->getTransactionsByAccountInRange($ac, $this->getDateRange());

                $index[$c->getEveId()]['buy'] = array_merge(
                    $index[$c->getEveId()]['buy'],
                    $mtRepo->getTransactionsByAccountInRange($ac, $this->getDateRange(), 'buy')
                );

                $index[$c->getEveId()]['sell'] = array_merge(
                    $index[$c->getEveId()]['sell'],
                    $mtRepo->getTransactionsByAccountInRange($ac, $this->getDateRange(), 'sell')
                );
            }
        }

        foreach ($index as $corp) {
            $buyData = $corp['buy'];
            unset($corp['buy']);
            $sellData = $corp['sell'];
            unset($corp['sell']);
            $accounts = $corp;

            if (!empty($buyData)){
                $this->makeTransactionCsv($buyData, 'buy');
            }

            if (!empty($sellData)){
                $this->makeTransactionCsv($sellData, 'sell');
            }

            foreach ($accounts as $a) {
                if(!empty($a)){
                    $this->makeJournalTransaction($a);
                }
            }
        }
    }

    protected function makeTransactionCsv(array $data, $name)
    {
        if (empty($data)){
            throw new \Exception('Empty data when making csv');
        }

        $corpName = strtolower(str_replace(' ', '_', $data[0]->getAccount()->getCorporation()->getCorporationDetails()->getName()));

        $fileName = __DIR__.sprintf('/../../../export/%s_%s_data.%s.csv', $corpName, $name, Carbon::now()->toDateTimeString());
        if (!file_exists($fileName)) {
            $fh = fopen($fileName, 'w');
            fclose($fh);
        }

        $csv = Writer::createFromPath($fileName);
        $csv->insertOne(['corp', 'account', 'date', 'time', 'transaction_id', 'quantity', 'item_name', 'item_id', 'price', 'client_id', 'client_name', 'character_id', 'character_name', 'station_id', 'station_name', 'transaction_type', 'transaction_for', 'journal_transaction_id', 'client_type_id']);

        $insertData = [];
        foreach ($data as $d) {
            $arr = [
                'corp' => $d->getAccount()->getCorporation()->getCorporationDetails()->getName(),
                'account' => $d->getAccount()->getName(),
                'date' => $d->getDate()->format('m/d/Y'),
                'time' => $d->getDate()->format('G:m'),
                'transaction_id' => $d->getTransactionId(),
                'quantity' => $d->getQuantity(),
                'item_name' => $d->getItemName(),
                'item_id' => $d->getItemId(),
                'price' => $d->getPrice(),
                'client_id' => $d->getClientId(),
                'client_name' => $d->getClientName(),
                'character_id' => $d->getCharacterId(),
                'character_name' => $d->getCharacterName(),
                'station_id' => $d->getStationId(),
                'station_name' => $d->getStationName(),
                'transaction_type' => $d->getTransactionType(),
                'transaction_for' => $d->getTransactionFor(),
                'journal_transaction_id' => $d->getJournalTransactionId(),
                'client_type_id' => $d->getClientTypeId(),
            ];

            $insertData[] = $arr;
        }

        $csv->insertAll($insertData);
    }

    protected function makeJournalTransaction(array $data)
    {
        if (empty($data)){
            throw new \Exception('Empty data when creating journal Transaction');
        }
        $corpName = strtolower(str_replace(' ', '_', $data[0]->getAccount()->getCorporation()->getCorporationDetails()->getName()));
        $accountName = strtolower(str_replace(' ', '_', $data[0]->getAccount()->getName()));

        $fileName = __DIR__.sprintf('/../../../export/%s_%s_data.%s.csv', $corpName, $accountName, Carbon::now()->toDateTimeString());
        if (!file_exists($fileName)) {
            $fh = fopen($fileName, 'w');
            fclose($fh);
        }

        $csv = Writer::createFromPath($fileName);
        $csv->insertOne(['corp', 'account', 'date', 'time', 'ref_id', 'ref_type_id', 'ref_type', 'owner_name1', 'owner_id1', 'owner_id2', 'arg_name1', 'arg_id1', 'amount', 'balance', 'reason', 'owner1_type_id', 'owner2_type_id']);

        $insertData = [];
        foreach ($data as $d) {
            $arr = [
                'corp' => $d->getAccount()->getCorporation()->getCorporationDetails()->getName(),
                'account' => $d->getAccount()->getName(),
                'date' => $d->getDate()->format('m/d/Y'),
                'time' => $d->getDate()->format('G:m'),
                'ref_id' => $d->getRefId(),
                'ref_type_id' => $d->getRefTypeId(),
                'ref_type' => $d->getRefType()->getRefTypeName(),
                'owner_name1' => $d->getOwnerName1(),
                'owner_id1' => $d->getOwnerId1(),
                'owner_id2' => $d->getOwnerId2(),
                'owner_name2' => $d->getOwnerName2(),
                'arg_name1' => $d->getArgName1(),
                'arg_id1' => $d->getArgId1(),
                'amount' => $d->getAmount(),
                'balance' => $d->getBalance(),
                'reason' => $d->getReason(),
                'owner1_type_id' => $d->getOwner1TypeId(),
                'owner2_type_id' => $d->getOwner2TypeId(),
            ];

            $insertData[] = $arr;
        }

        $csv->insertAll($insertData);
    }

    protected function getDateRange()
    {
        return [
            'end' => Carbon::now(),
            'start' => Carbon::create()->firstOfQuarter(),
        ];
    }
}
