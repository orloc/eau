<?php

namespace AppBundle\Security\Authorization\Voter;

use AppBundle\Entity\User;
use AppBundle\Security\AccessTypes;
use AppBundle\Security\SecurityHelperTrait;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Symfony\Component\Security\Core\Authorization\Voter\AbstractVoter;

class CorporationVoter extends AbstractVoter
{
    use SecurityHelperTrait;

    private $doctrine;

    public function __construct(Registry $registry)
    {
        $this->doctrine = $registry;
    }

    protected function getSupportedAttributes()
    {
        return [AccessTypes::EDIT, AccessTypes::VIEW];
    }

    protected function getSupportedClasses()
    {
        return ['AppBundle\Entity\Corporation'];
    }

    protected function isGranted($attribute, $object, $user = null)
    {
        if (!$user instanceof User) {
            return false;
        }

        $char = $this->doctrine->getRepository('AppBundle:Character')
              ->getMainCharacter($user);

        switch ($attribute) {
               case AccessTypes::VIEW:
                    if ($user->hasRole('ROLE_SUPER_ADMIN') || $user->hasRole('ROLE_ADMIN')) {
                        return  true;
                    }

                    if ($user->hasRole('ROLE_ALLIANCE_LEADER')) {
                        $registeredAllianceCorps = $this->getAllianceCorps($char, $this->doctrine);

                         // is this corp in this alliance?
                         foreach ($registeredAllianceCorps as $registeredAllianceCorp) {
                             if ($registeredAllianceCorp->getId() === $object->getId()) {
                                 return true;
                             }
                         }
                    }

                    if ($user->hasRole('ROLE_CEO')) {
                        // is the main character the CEO of this corp?
                         $corpCeo = $object->getCorporationDetails()
                             ->getCeoName();

                        $charIsCeo = strcmp($corpCeo, $char->getName()) === 0;
                        $accHasChar = false;

                         // well they got the role somehow
                         if (!$charIsCeo) {
                             // @TODO implement an additonal flag
                              $charIsCeo = true;
                         }

                         // @TODO Test me when you implement that flag as this never gets run atm
                         if (!$charIsCeo) {
                             // check if this account has a character this IS the CEO of this corp
                              foreach ($user->getCharacters() as $c) {
                                  if ($accHasChar) {
                                      continue;
                                  }
                                  $accHasChar = strcmp($corpCeo, $c->getName()) === 0;
                              }
                         }

                        return $charIsCeo || $accHasChar;
                    }

                    if ($user->hasRole('ROLE_DIRECTOR')) {
                        // check that the user is in this corporation
                         $corpMembers = $object->getCorporationMembers();
                        foreach ($corpMembers as $m) {
                            if (intval($m->getCharacterId()) === intval($char->getEveId())) {
                                return true;
                            }
                        }
                    }

                  break;
               case AccessTypes::EDIT:
                    if ($user->hasRole('ROLE_CEO')) {
                        $corp = $this->doctrine->getRepository('AppBundle:Corporation')
                             ->findByCorpName($char->getCorporationName());

                        return $corp instanceof $corp && $corp->getId() === $object->getId();
                    }

                    return false;

                  break;
          }

        return false;
    }
}
