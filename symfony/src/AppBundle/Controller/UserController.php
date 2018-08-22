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
        echo "Hola mundo";die;
    }

}

