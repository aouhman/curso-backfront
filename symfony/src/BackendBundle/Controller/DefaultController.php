<?php

namespace BackendBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;

class DefaultController extends Controller
{
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $userRepo = $em->getRepository('BackendBundle:User');
        $users = $userRepo->findAll();
        return $this->json(array(
                'status' =>  'success',
                'users'  =>   $users[0]->getName()
            )
        );
  //      return $this->render('@Backend/Default/index.html.twig');

    }
}
