<?php

namespace BackendBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AppBundle\Services\Helpers;
class DefaultController extends Controller
{
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $userRepo = $em->getRepository('BackendBundle:User');
        $users = $userRepo->findAll();
        $helpers =  $this->get(Helpers::class);
        return $helpers->json(array(
                'status' =>  'success',
                'users'  =>   $users
            )
        );


  //      return $this->render('@Backend/Default/index.html.twig');

    }
}
