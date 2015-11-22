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


                    if ($user->hasRole('ROLE_ALLIANCE_LEADER')) {
                         $char = $this->doctrine->getRepository('AppBundle:Character')
                             ->getMainCharacter($user);

                         var_dump($char);die;
                         // is this corp in the alliance?
                    }

                    if ($user->hasRole('ROLE_CEO')){

                    }

                  break;
               case AccessTypes::EDIT:
                  break;
          }

          return false;
     }
}