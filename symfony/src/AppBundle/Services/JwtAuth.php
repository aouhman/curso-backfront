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
        $this->key = "abdssasatetst123UnexpectedValueExceptionUnexpectedValueException";
    }

    public function signup($email,$password,$getHash=null){

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

              $jwt = JWT::encode($token,$this->key,'HS256');
              $decoded = JWT::decode($jwt,$this->key,array('HS256'));
              if(!$getHash){
                  $data = $jwt;
              }else{
                  $data = $decoded;
              }

         } else{
             $data = array(
                 "data"=>'Login failed!!',
                 'status' => 'error'
             );
         }
        return $data;
    }

    /**
     * @param string $jwt var $JWT représenter la valeur de token déja passer en parametre pour recuprer l'object
     * @param bool   $getIdentity pour recuprer object connecter a
     **@return bool|object
     */
    public function checkToken($jwt,$getIdentity = false ){
        $auth = false;
      try{
        $decoded = JWT::decode($jwt,$this->key,array('HS256'));
      }catch (\UnexpectedValueException $e){
         $auth = false;
      }catch(\DomainException $e){
         $auth = false;
      }

        if(isset($decoded) && is_object($decoded) && isset($decoded->sub)){
            $auth = true;
        }else{
            $auth = false;
        }
        if(!$getIdentity){
            return $auth;
        }else{

            return $decoded;
        }
    }


}