<?php

namespace AppBundle\Service\Manager;


use Tarioch\PhealBundle\DependencyInjection\PhealFactory;

class AccountStatusManager {

    private $pheal;

    public function __construct(PhealFactory $pheal){
        $this->pheal = $pheal;
    }


    public function updateDetails(){
        $client = $this->pheal->createEveOnline(4624909, '67FGTUIkjVEAQSgNTTHP9F6k3tdoCNEasrujfISp2RJL63bJC9yC6ha0HiobypPr');

        var_dump($client->accountStatus()->logonMinutes / 60 / 24);die;
        var_dump($result);die;
    }
}
