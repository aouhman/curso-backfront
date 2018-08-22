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

    public function testAction()
    {
     echo "Bonjour tout le monde Voila Symfony 3";
        die();
    }

    public function loginAction(Request $request)
    {
        $helpers =  $this->get(Helpers::class);
        $json = $request->get("json",null);


        $data = array(
            'status' => 'error',
             'data'  => 'Send json via post !!'
        );

        if($json != null){
            //
            $params = json_decode($json);
            $email = (isset($params->email)) ? $params->email : null;
            $password = (isset($params->password)) ? $params->password : null;

            $emailConstraint = new Assert\Email();
            $emailConstraint->message = "Cet email n'est pas valide";
            $validate_email = $this->get('validator')->validate($email,$emailConstraint);


            if( $email && count($validate_email) == 0 && $password){

                $jwt_auth = $this->get(JwtAuth::class);
                $signup =  $jwt_auth->signup($email,$password);
                $data = array(
                    'status' => 'success',
                    'data'   => 'Email correct',
                    'signup' => $signup
                );
            }else{
                $data = array(
                    'status' => 'error',
                    'data'   => 'Email ou password incorrect'

                );
            }
        }
        return $helpers->json($data);
    }
}

