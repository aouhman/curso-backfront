<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 21/08/2018
 * Time: 21:40
 */
namespace AppBundle\Services;

use Firebase\JWT\JWT;
class JwtAuth{
    /**
     * Helpers constructor.
     */
    public $manager;
    public $key;
    public function __construct($manager)
    {
        $this->manager = $manager;
    }

    public function signup($email,$password){

        $user =  $this->manager->getRepository('BackendBundle:User')->findOneBy(array(
                              "email"    => $email,
                              "password" => $password,

                          )
                      );
         $signup = false;
         if(is_object($user)){
             $signup = true;
         }
         if($signup){
             $token= array(
               "sub" => $user->getId() ,
               "email" => $user->getEmail() ,
               "name" => $user->getName(),
               "surname" => $user->getSurname(),
               "iat" => time() ,
               "ext" => time() + (7*24*60*60),
             );

             $jwt = JWT::encode($token,$key);
              $data = array(
                  "user"=>$user,
                   'status' => 'success'
              );
         } else{
             $data = array(
                 "data"=>'Login failed!!',
                 'status' => 'error'
             );
         }
        return $data;
    }


}