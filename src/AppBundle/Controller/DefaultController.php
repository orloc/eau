<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Yaml\Parser;
use Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="default")
     */
    public function indexAction(Request $request)
    {
        $file = 'app/data/typeIDs.yaml';

        $fs = new Filesystem();

        $filePath = __DIR__.'/../../../'.$file;

        if (!$fs->exists($filePath)) {
            throw new FileNotFoundException(sprintf('%s does not exist', $filePath));
        }

        $yaml = new Parser();

        $value = file_get_contents($filePath);

        echo '<pre>';
        echo $value;
        echo '</pre>';
        die;
        // replace this example code with whatever you need
        return $this->render('::base.html.twig');
    }
}
