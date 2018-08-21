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
        $helpers->holaMundo();die;
        return $this->json(array(
                'status' =>  'success',
                'users'  =>   $users[0]->getName()
            )
        );
  //      return $this->render('@Backend/Default/index.html.twig');

    }
}
