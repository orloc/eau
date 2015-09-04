<?php

namespace EveBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Exception\FileNotFoundException;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Yaml\Parser;

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

        $yaml = new Parser();

        $value = $yaml->parse(file_get_contents($filePath));

        var_dump($value);
        die;
    }

}
