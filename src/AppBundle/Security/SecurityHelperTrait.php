<?php

namespace AppBundle\Security;

use AppBundle\Entity\Character;
use Doctrine\Bundle\DoctrineBundle\Registry;

trait SecurityHelperTrait
{
    public function getAllianceCorps(Character $char, Registry $doctrine)
    {
        $leaderCorp = $doctrine->getRepository('AppBundle:Corporation')
            ->findByCorpName($char->getCorporationName());

        if ($leaderCorp === null) {
            // this is bad
            return false;
        }

        $registeredAllianceCorps = $doctrine->getRepository('AppBundle:Corporation')
            ->findCorporationsByAlliance($leaderCorp->getCorporationDetails()->getAllianceName());

        return $registeredAllianceCorps;
    }
}
