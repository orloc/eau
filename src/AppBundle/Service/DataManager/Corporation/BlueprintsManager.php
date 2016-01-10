<?php

namespace AppBundle\Service\DataManager\Corporation;


use AppBundle\Entity\Account;
use AppBundle\Entity\AccountBalance;
use AppBundle\Entity\Corporation;
use Symfony\Component\OptionsResolver\Exception\OptionDefinitionException;
use AppBundle\Service\DataManager\AbstractManager;
use AppBundle\Service\DataManager\DataManagerInterface;
use AppBundle\Service\DataManager\MappableDataManagerInterface;

class AccountManager extends AbstractManager implements DataManagerInterface, MappableDataManagerInterface {

}
