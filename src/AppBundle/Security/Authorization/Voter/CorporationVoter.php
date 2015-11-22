<?php

namespace AppBundle\Security\Authorization\Voter;


use AppBundle\Entity\User;
use AppBundle\Security\AccessTypes;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Symfony\Component\Security\Core\Authorization\Voter\AbstractVoter;

class CorporationVoter extends AbstractVoter {

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
          return [ 'AppBundle\Entity\Corporation' ];
     }

     protected function isGranted($attribute, $object, $user = null)
     {
          if (!$user instanceof User){
               return false;
          }

          switch ($attribute){
               case AccessTypes::VIEW:
                    if ($user->hasRole('ROLE_SUPER_ADMIN') || $user->hasRole('ROLE_ADMIN') ){
                         return  true;
                    }

                    $char = $this->doctrine->getRepository('AppBundle:Character')
                        ->getMainCharacter($user);

                    if ($user->hasRole('ROLE_ALLIANCE_LEADER')) {
                         $leaderCorp = $this->doctrine->getRepository('AppBundle:Corporation')
                             ->findByCorpName($char->getCorporationName());

                         if ($leaderCorp === null){
                              // this is bad
                              return false;
                         }

                         $registeredAllianceCorps = $this->doctrine->getRepository('AppBundle:Corporation')
                             ->findCorporationsByAlliance($leaderCorp->getCorporationDetails()->getAllianceName());

                         // is this corp in this alliance?
                         foreach ($registeredAllianceCorps as $registeredAllianceCorp) {
                              if ($registeredAllianceCorp->getId() === $object->getId()){
                                   return true;
                              }
                         }
                    }

                    if ($user->hasRole('ROLE_CEO')){
                         // is the main character the CEO of this corp?
                         $corpCeo = $object->getCorporationDetails()
                             ->getCeoName();

                         $charIsCeo = strcmp($corpCeo, $char->getName()) === 0;
                         $accHasChar = false;

                         // well they got the role somehow
                         if (!$charIsCeo){
                              // @TODO implement an additonal flag
                              $charIsCeo = true;
                         }

                         // @TODO Test me when you implement that flag as this never gets run atm
                         if (!$charIsCeo){
                              // check if this account has a character this IS the CEO of this corp
                              foreach ($user->getCharacters() as $c){
                                   if ($accHasChar){ continue; }
                                   $accHasChar = strcmp($corpCeo, $char->getName()) === 0;
                              }
                         }

                         return $charIsCeo || $accHasChar;
                    }

                  break;
               case AccessTypes::EDIT:
                  break;
          }

          return false;
     }
}