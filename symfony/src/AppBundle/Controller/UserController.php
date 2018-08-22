<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Services\Helpers;
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
                $user->setPassword($password);

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

}

