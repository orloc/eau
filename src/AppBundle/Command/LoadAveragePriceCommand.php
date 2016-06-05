<?php

namespace AppBundle\Command;

use AppBundle\Entity\AveragePrice;
use GuzzleHttp\Client;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class LoadAveragePriceCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('eau:load_averageprices')
            ->setDescription('Loads average prices from the crest API.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $registry = $this->getContainer()->get('doctrine');
        $em = $registry->getManager();

        $client = new Client();
        
        $result = $client->get($this->getContainer()->getParameter('crest_market_url'));
        
        if ($result->getStatusCode() === 200) {
            $json = $result->getBody()->getContents();
            $data = $this->getContainer()->get('jms_serializer')->deserialize($json, 'array', 'json');
            
            foreach ($data['items'] as $p) {
                $e = $this->createAvgPrice($p);
                $em->persist($e);
            }

            $em->flush();
        }
    }

    private function createAvgPrice(array $data)
    {
        $item = new AveragePrice();

        $item->setTypeId($data['type']['id'])
            ->setAdjustedPrice(isset($data['adjustedPrice']) ? $data['adjustedPrice'] : null)
            ->setAveragePrice(isset($data['averagePrice'])  ? $data['averagePrice'] : null);

        return $item;
    }
}
