<?php

namespace AppBundle\Security\Authorization\Voter;


use AppBundle\Entity\Character;
use AppBundle\Entity\Corporation;
use AppBundle\Entity\User;
use AppBundle\Security\AccessTypes;
use AppBundle\Security\Authorization\SecurityVoterTrait;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Symfony\Component\Security\Core\Authorization\Voter\AbstractVoter;

class CharacterVoter extends AbstractVoter {

     use SecurityVoterTrait;

     private $doctrine;

     public function __construct(Registry $registry){
          $this->doctrine = $registry;
     }

     protected function getSupportedAttributes()
     {
          return [ AccessTypes::EDIT, AccessTypes::VIEW ];
     }

     protected function getSupportedClasses()
     {
          return [ 'AppBundle\Entity\Character' ];
     }

     protected function isGranted($attribute, $object, $user = null)
     {
          if (!$user instanceof User){
               return false;
          }

          switch ($attribute){
               case AccessTypes::VIEW:

                    if ($object->getUser() === $user) {
                         return true;
                    }

                    if ($user->hasRole('ROLE_SUPER_ADMIN') || $user->hasRole('ROLE_ADMIN') ){
                         return  true;
                    }

                    $char = $this->doctrine->getRepository('AppBundle:Character')
                        ->getMainCharacter($user);

                    if ($user->hasRole('ROLE_ALLIANCE_LEADER')) {
                         $registeredAllianceCorps = $this->getAllianceCorps($char, $this->doctrine);

                         foreach ($registeredAllianceCorps as $registeredAllianceCorp) {
                              if (strcmp($registeredAllianceCorp->getCorporationDetails()->getName(), $object->getCorporationName()) === 0){
                                   return true;
                              }
                         }
                    }

                    if ($user->hasRole('ROLE_CEO')){
                         $corp = $this->doctrine->getRepository('AppBundle:Corporation')
                             ->findByCorpName($object->getCorporationName());

                         return $corp instanceof Corporation
                         && $char->getCorporation()->getId() === $corp->getId();
                    }

                  break;
               case AccessTypes::EDIT:
                  break;
          }

          return false;
     }

}