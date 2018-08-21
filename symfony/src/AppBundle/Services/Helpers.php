<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 21/08/2018
 * Time: 21:40
 */
namespace AppBundle\Services;
class Helpers{
    /**
     * Helpers constructor.
     */
    public $manager;
    public function __construct($manager)
    {
        $this->manager = $manager;
    }

    public  function  holaMundo(){

        echo "Hada Service mriid";die;
    }

}