<?php

namespace AppBundle\Controller;

use Firebase\JWT\JWT;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Services\Helpers;
use AppBundle\Services\JwtAuth;

class DefaultController extends Controller
{

    public function indexAction(Request $request)
    {
        // replace this example code with whatever you need
        return $this->render('default/index.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.project_dir')).DIRECTORY_SEPARATOR,
        ]);
    }

    public function testAction(Request $request)
    {
        $token = $request->get("authorization", null);
        $helpers = $this->get(Helpers::class);
        $jwtAuth = $this->get(JwtAuth::class);

        if ($token && $jwtAuth->checkToken($token)) {
            $em = $this->getDoctrine()->getManager();
            $userRepo = $em->getRepository("BackendBundle:User");
            $users = $userRepo->findAll();
            return $helpers->json(array(
                'status' => 'success',
                'users' => $users
            ));
        } else {
            return $helpers->json(array(
                'status' => 'error',
                'code' => 400,
                'data' => 'authentification non valide '
            ));
        }

    }

    public function loginAction(Request $request)
    {
        $helpers = $this->get(Helpers::class);
        $json = $request->get("json", null);


        $data = array(
            'status' => 'error',
            'data' => 'Send json via post !!'
        );

        if ($json) {
            //
            $params = json_decode($json);
            $email = (isset($params->email)) ? $params->email : null;
            $password = (isset($params->password)) ? $params->password : null;
            $getHash = (isset($params->getHash)) ? $params->getHash : null;

            $emailConstraint = new Assert\Email();
            $emailConstraint->message = "Cet email n'est pas valide";
            $validate_email = $this->get('validator')->validate($email, $emailConstraint);


            if ($email && count($validate_email) == 0 && $password) {

                $jwtAuth = $this->get(JwtAuth::class);

                $pws = hash("sha256",$password);
                if (!$getHash) {
                    $signup = $jwtAuth->signup($email, $pws);
                } else {
                    $signup = $jwtAuth->signup($email, $pws, true);
                }
                return $this->json($signup);
            } else {
                $data = array(
                    'status' => 'error',
                    'data' => 'Email ou password incorrect'

                );
            }
        }
        return $helpers->json($data);
    }
}

