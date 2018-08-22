<?php

namespace AppBundle\Controller;

use BackendBundle\Entity\Task;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Services\Helpers;
use AppBundle\Services\JwtAuth;
use BackendBundle\Entity\User;

class TaskController extends Controller
{

    public function newAction(Request $request)
    {
        $helpers  = $this->get(Helpers::class);
        $jwtAuth = $this->get(JwtAuth::class);

        $token = $request->get("authorization", null);
        $authCheck = $jwtAuth->checkToken($token);

        if($authCheck){
            $identity = $jwtAuth->checkToken($token,true);
            $json = $request->get("json",null);
            if($json){
                $params = json_decode($json);
                $createdAt = new \DateTime();
                $updatedAt = new \DateTime();

                $user_id = $identity->sub;

                $title = (isset($params->title)) ? $params->title : null;
                $description = (isset($params->description)) ? $params->description : null;
                $status = (isset($params->status)) ? $params->status : null;

                if($user_id && $title && $status){
                    $em = $this->getDoctrine()->getManager();
                    $user = $em->getRepository('BackendBundle:User')->find($identity->sub);
                    $task = new Task();
                    $task->setTitle($title);
                    $task->setDescription($description);
                    $task->setStatus($status);
                    $task->setCreatedAt($createdAt);
                    $task->setUpdatedAt($createdAt);
                    $task->setUser($user);

                    $em->persist($task);
                    $em->flush();

                    $data = array(
                        "status" => "Success",
                        "code"   => 200,
                        "msg"    => "task created"
                    );
                }else{
                    $data = array(
                        "status" => "Success",
                        "code"   => 400,
                        "msg"    => "Task not created"
                    );
                }





            }

            $data = array(
                "status" => "Success",
                "code"   => 200,
                "msg"    => "Message"
            );
        }else{
            $data = array(
                "status" => "Error",
                "code"   => 200,
                "msg"    => "Message"
            );
        }
        return $helpers->json($data);

    }

    /*
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
    */
}

