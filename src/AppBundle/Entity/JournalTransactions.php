<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="journal_transactions")
 * @JMS\ExclusionPolicy("all")
 *
 * @package AppBundle\Entity
 */
class JournalTransaction {

    /**
     * @ORM\Id
     * @ORM\Column(type="bigint")
     * @ORM\GeneratedValue(strategy="AUTO")
     * @JMS\Expose()
     */
    protected $id;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $date;

    /**
     * @ORM\Column(type="bigint")
     */
    protected $refId;

    /**
     * @ORM\Column(type="integer")
     */
    protected $refTypeId;

    /**
     * @ORM\Column(type="string", length=255)
     */
    protected $ownerName1;

    /**
     * @ORM\Column(type="integer")
     */
    protected $ownerId1;

    /**
     * @ORM\Column(type="string", length=255)
     */
    protected $ownerName2;

    /**
     * @ORM\Column(type="integer")
     */
    protected  $ownerId2;

    /**
     * @ORM\Column(type="string", length=255)
     */
    protected $argName1;

    /**
     * @ORM\Column(type="string", length=255)
     */
    protected $argId1;

    /**
     * @ORM\Column(type="decimal", precision=16, scale=2)
     */
    protected $amount;

    /**
     * @ORM\Column(type="decimal", precision=16, scale=2)
     */
    protected $balance;

    /**
     * @ORM\Column(type="string")
     */
    protected $reason;

    /**
     * @ORM\Column(type="string")
     */
    protected $taxReceiverId;

    /**
     * @ORM\Column(type="string")
     */
    protected $taxAmount;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Account", inversedBy="journal_transactions")
     */
    protected $account;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $created_at;
}
