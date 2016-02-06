<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Character;
use AppBundle\Entity\Corporation;
use AppBundle\Entity\CorporationMember;
use AppBundle\Entity\User;
use Doctrine\ORM\EntityRepository;

class ApiCredentialRepository extends EntityRepository {

    public function getActiveKeyForUser(User $user){
        return $this->createQueryBuilder('api')
            ->select('api')
            ->leftJoin('api.characters', 'char')
            ->leftJoin('char.user', 'u')
            ->where('api.is_active = :active')
            ->andWhere('api.invalid != :invalid_bool')
            ->andWhere('u = :user')
            ->setParameters(['user' => $user, 'active' => true, 'invalid_bool' => true])
            ->getQuery()->getResult();
    }

    public function getKeysByCharacter(Character $char){

        return $this->createQueryBuilder('api')
            ->select('api')
            ->andWhere('api.eve_character_id = :char_id')
            ->andWhere('api.type IN (:types)')
            ->setParameters(['char_id' => $char->getEveId(), 'types' => ['Character', 'Account']])
            ->getQuery()->getResult();

    }

    public function findRelatedKeyByMember(CorporationMember $member){
        return $this->createQueryBuilder('api')
            ->leftJoin('api.characters', 'c')
            ->where('c.eve_id = :char_id')
            ->andWhere('api.type IN ( :api_types )')
            ->setParameters([
                'char_id' => $member->getCharacterId(),
                'api_types' => [ 'Character', 'Account']
            ])->getQuery()->getOneOrNullResult();
    }

    public function getActiveKey(Corporation $corp){

        return $this->createQueryBuilder('api')
            ->select('api')
            ->where('api.is_active = :active')
            ->andWhere('api.invalid != :invalid_bool')
            ->andWhere('api.corporation = :corp')
            ->setParameters(['corp' => $corp, 'active' => true, 'invalid_bool' => true])
            ->getQuery()->getOneOrNullResult();

    }
}
