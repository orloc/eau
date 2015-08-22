<?php

namespace AppBundle\Entity;

use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
/**
 * @ORM\Entity
 * @ORM\Table(name="users")
 * @JMS\ExclusionPolicy("all")
 *
 * Class User
 * @package AppBundle\Entity
 */
class User extends BaseUser {


    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     * @JMS\Expose()
     */
    protected $id;


    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }
}
