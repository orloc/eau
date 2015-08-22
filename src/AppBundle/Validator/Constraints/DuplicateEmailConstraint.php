<?php

namespace AppBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;


class DuplicateEmailConstraint extends Constraint {

    public $message = "The email %email% is already taken.";

    public function validatedBy(){
        return 'duplicate_email_validator';
    }

}