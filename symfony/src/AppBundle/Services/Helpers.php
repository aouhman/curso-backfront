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


    public function json($data){
        $normalizers = array(new \Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer());
        $encoders = array("json"=> new \Symfony\Component\Serializer\Encoder\JsonEncode());

        $serializer = new \Symfony\Component\Serializer\Serializer($normalizers,$encoders);
        $json = $serializer->serialize($data,'json');
        $response =  new \Symfony\Component\HttpFoundation\Response();
        $response->setContent($json);
        $response->headers->get('Content-Type','application/type');
        return $response;
    }


}