<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Services\Helpers;
use AppBundle\Services\JwtAuth;
use BackendBundle\Entity\User;

class UserController extends Controller
{

    public function newAction(Request $request)
    {
        $helpers = $this->get(Helpers::class);
        $json = $request->get("json", null);
        $data = array(
            "status" => "Error",
            "code" => 400,
            "msg" => "Veuillez remplir tous les champs obligatoires"
        );

        if ($json) {
            $params = json_decode($json,null);
            $createdAt = new \DateTime("now");
            $role = 'user';

            $email = (isset($params->email)) ? $params->email : null;

            $name = (isset($params->name)) ? $params->name : null;
            $password = (isset($params->password)) ? $params->password : null;
            $surname = (isset($params->surname)) ? $params->surname : null;

            $emailConstraint = new Assert\Email();
            $emailConstraint->message = "Cet email n'est pas valide";
            $validate_email = $this->get('validator')->validate($email, $emailConstraint);
            if ($name && $surname && $email && count($validate_email) == 0 && $password) {
                $user = new User();
                $user->setCreatedAt($createdAt);
                $user->setEmail($email);
                $user->setRole($role);
                $user->setName($name);
                $user->setSurname($surname);

                $pws = hash("sha256",$password);
                $user->setPassword($pws);

                $em = $this->getDoctrine()->getManager();
                $issetUser = $em->getRepository('BackendBundle:User')->findOneBy(array('email' => $email));

                if ($issetUser) {
                    $data = array(
                        "status" => "Error",
                        "code" => 200,
                        "msg" => "Utilisateur existe déja"
                    );

                } else {
                    $data = array(
                        "status" => "Success",
                        "code" => 200,
                        "msg" => "Utilisateur bien été crée"
                    );
                    $em->persist($user);
                    $em->flush();
                }

            }
        }


        return $helpers->json($data);
    }


    public function editAction(Request $request)
    {
        $helpers = $this->get(Helpers::class);
        $jwtAuth = $this->get(JwtAuth::class);

        $json = $request->get("json", null);
        $token = $request->get("authorization", null);
        $authCheck = $jwtAuth->checkToken($token);

        $data = array(
            "status" => "Error",
            "code" => 400,
            "msg" => "Vous ne disposez pas des autorisations nécessaires pour effectuer cette opération"
        );

        if($authCheck) {

            if ($json) {
                $params = json_decode($json, null);

                $email = (isset($params->email)) ? $params->email : null;
                $name = (isset($params->name)) ? $params->name : null;
                $password = (isset($params->password)) ? $params->password : null;
                $surname = (isset($params->surname)) ? $params->surname : null;

                $emailConstraint = new Assert\Email();
                $emailConstraint->message = "Cet email n'est pas valide";
                $validate_email = $this->get('validator')->validate($email, $emailConstraint);

                $em = $this->getDoctrine()->getManager();
                //find user to edit $user
                $identity = $jwtAuth->checkToken($token,true);
                $user = $em->getRepository('BackendBundle:User')->findOneBy(array('id' => $identity->sub));

                if ($name && $surname && $email && count($validate_email) == 0 && $user) {

                    $user->setName($name);
                    $user->setSurname($surname);
                    if($password){
                        $pws = hash("sha256",$password);
                        $user->setPassword($pws);
                    }
                    $em->persist($user);
                    $em->flush();

                    $data = array(
                        "status" => "Success",
                        "code" => 200,
                        "msg" => "Utilisateur pour modifier avec success"
                    );

                }else{
                    $data = array(
                        "status" => "Error",
                        "code" => 200,
                        "msg" => "Impossible de modifier cet utilisateur"
                    );
                }
            }
        }else{
            $data = array(
                "status" => "Error",
                "code" => 200,
                "msg" => "Vous n'êtes pas autorisé à modifier un utilisateur"
            );
        }


        return $helpers->json($data);
    }

}

