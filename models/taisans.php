<?php

namespace QLtaisanDB\Relia;

use Phalcon\Mvc\Model;
use Phalcon\Validation;
use Phalcon\Validation\Validator\Uniqueness;
use Phalcon\Validation\Validator\InclusionIn;

class taisans extends Model
{

    public $ID;
    public $Name;
    public $taisans;
    public function setId($ID)
    {
        $this->ID = $ID;
    }
    public function setname($Name)
    {
        $this->id = $Name;
    }
    public function getStatus()
    {
        return $this->status;
    }

    public function validation()
    {
        $validator = new Validation();

        $validator->add(
            'ID',
            new InclusionIn(
                [
                    'domain' => [
                                    1,
                                    2,
                                    3,
                                    66,
                    ]
                ]
            )
        );

        $validator->add(
            'Name',
            new Uniqueness(
                [
                    'message' => 'The robot name must be unique',
                ]
            )
        );

        return $this->validate($validator);
    }
}