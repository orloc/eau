<?php

namespace EveBundle\Command;

use EveBundle\Entity\ItemType;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Exception\FileNotFoundException;
use Symfony\Component\Filesystem\Filesystem;

class YamlLoaderCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('evedata:load_types')
            ->addArgument('filename', InputArgument::REQUIRED)
            ->setDescription('Loads item types from yaml configuration file.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $file = $input->getArgument('filename');

        $fs = new Filesystem();

        $filePath = __DIR__.'/../../../'.$file;

        if (!$fs->exists($filePath)) {
            throw new FileNotFoundException(sprintf('%s does not exist', $filePath));
        }

        $registry = $this->getContainer()->get('doctrine');
        $em = $registry->getManager('eve_data');

        $value = yaml_parse_file($filePath);

        foreach ($value as $k => $v){
            $i = $this->createItemType($v, $k);

            if ($i){
                $em->persist($i);
            }
        }

        $em->flush();

    }

    private function createItemType(array $data, $item_id){
        try {
            $item = new ItemType();
            $item->setName(isset($data['name']) ? $data['name']['en'] : null)
                ->setMass(isset($data['mass']) ? $data['mass'] : null)
                ->setGroupId(isset($data['groupID']) ? $data['groupID'] : null)
                ->setPortionSize(isset($data['portionSize']) ? $data['portionSize'] : null)
                ->setPublished(isset($data['published']) ? $data['published'] : null)
                ->setRadius(isset($data['radius']) ? $data['radius'] : null)
                ->setVolume(isset($data['volume']) ? $data['volume'] : null)
                ->setMarketGroupId(isset($data['marketGroupID']) ? $data['marketGroupID'] : null)
                ->setBasePrice(isset($data['basePrice']) ? $data['basePrice'] : null)
                ->setDescription(isset($data['description']) ? $data['description']['en'] : null)
                ->setTypeId($item_id);

        } catch (\Exception $e){
            var_dump($data);

            return false;
        }

        return $item;
    }

}
