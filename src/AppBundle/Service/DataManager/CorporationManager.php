<?php

namespace AppBundle\Service\DataManager;

use AppBundle\Entity\Account;
use AppBundle\Entity\ApiCredentials;
use AppBundle\Entity\Corporation;
use AppBundle\Entity\CorporationDetail;
use AppBundle\Entity\CorporationMember;
use Doctrine\Bundle\DoctrineBundle\Registry;
use EveBundle\Repository\Registry as EveRegistry;
use Monolog\Logger;
use Tarioch\PhealBundle\DependencyInjection\PhealFactory;

class CorporationManager extends AbstractManager implements DataManagerInterface {

    private $api_manager;

    public function __construct(PhealFactory $pheal, Registry $registry, EveRegistry $eveRegistry, Logger $logger, ApiKeyManager $apiManager ){
        parent::__construct($pheal, $registry, $eveRegistry, $logger);
        $this->api_manager = $apiManager;
    }

    public function createNewCorporation(ApiCredentials $key){
        $corp  = new Corporation();
        $corp->addApiCredential($key);

        return $corp;
    }

    public function getCorporationDetails(Corporation $corporation){

        $apiKey = $this->getApiKey($corporation);

        $client = $this->getClient($apiKey, 'account');
        $details = $client->APIKeyInfo()->key->characters[0];
        $result =  [ 'id' => $details->corporationID ];

        return $result;
    }

    public function getMembers(Corporation $corporation){
        $apiKey = $this->getApiKey($corporation);

        $client = $this->getClient($apiKey);

        $members = $client->MemberTracking()->members;

        $repo = $this->doctrine->getRepository('AppBundle:CorporationMember');

        $existing_members = $repo->findBy(['corporation' => $corporation]);

        $ids = [];
        foreach ($existing_members as $m) {
            $ids[(int)$m->getCharacterId()] = $m;
        }

        foreach ($members as $m) {
            $intId = (int)$m->characterID;
            if (!isset($ids[$intId])){
                $mem = new CorporationMember();

                $mem->setCharacterId($m->characterID)
                    ->setCharacterName($m->name)
                    ->setStartTime(new \DateTime($m->startDateTime));
                /**
                 * @TODO you forgot the homebase..
                 */

                $corporation->addCorporationMember($mem);
            } else {
                /*
                 * @TODO just lost their history
                 */
                if ($ids[$intId]->getDisbandedAt() !== null){
                    $ids[$intId]->setDisbandedAt(null)
                        ->setStartTime(new \Datetime($m->startDateTime));
                }
                unset($ids[$intId]);
            }
        }

        // any that were left over are not in our list anymore...
        foreach ($ids as $deleted_member){
            $deleted_member->setDisbandedAt(new \DateTime());
        }

    }

    public function getCorporationSheet(Corporation $corporation){
        $apiKey = $this->getApiKey($corporation);
        $corpClient = $this->getClient($apiKey);

        $corpSheet = $corpClient->CorporationSheet();

        $this->initializeAccounts($corpSheet->walletDivisions, $corporation);

        if (!($entity = $corporation->getCorporationDetails()) instanceof CorporationDetail){
            $entity = new CorporationDetail();
        }

        $entity->setName($corpSheet->corporationName)
            ->setTicker($corpSheet->ticker)
            ->setCeoName($corpSheet->ceoName)
            ->setCeoId($corpSheet->ceoID)
            ->setHeadquartersId($corpSheet->stationID)
            ->setHeadquartersName($corpSheet->stationName)
            ->setDescription($corpSheet->description)
            ->setUrl($corpSheet->url)
            ->setAllianceId($corpSheet->allianceID)
            ->setAllianceName($corpSheet->allianceName)
            ->setTaxRate($corpSheet->taxRate)
            ->setMemberCount($corpSheet->memberCount)
            ->setMemberLimit($corpSheet->memberLimit)
            ->setShares($corpSheet->shares);

        return $entity;

    }

    public function initializeAccounts($accounts, Corporation $corp){
        foreach ($accounts as $a){
            if (intval($a->accountKey) <= 1006) {
                $exists = $this->doctrine->getRepository('AppBundle:Account')
                    ->findOneBy(['corporation' => $corp, 'division' => $a->accountKey]);


                if ($exists instanceof Account){
                    $exists->setName($a->description);
                } else {
                    $account = new Account();

                    $account->setDivision($a->accountKey)
                        ->setName($a->description);

                    $corp->addAccount($account);

                }
            }
        }
    }

    public static function getName(){
        return 'corporation_manager';
    }

}