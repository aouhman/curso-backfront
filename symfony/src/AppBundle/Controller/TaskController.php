<?php

namespace AppBundle\Controller;

use BackendBundle\Entity\Task;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Services\Helpers;
use AppBundle\Services\JwtAuth;

class TaskController extends Controller
{

    public function newAction(Request $request ,$id =null )
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
                    if($id){
                        $task =  $em->getRepository('BackendBundle:Task')->find($id);
                        if( $task && isset($identity->sub) && $identity->sub == $task->getUser()->getId()){
                            $task->setTitle($title);
                            $task->setDescription($description);
                            $task->setStatus($status);
                            $task->setUpdatedAt($createdAt);

                            $em->persist($task);
                            $em->flush();

                            $data = array(
                                "status" => "Success",
                                "code"   => 200,
                                "msg"    => "Tâche bien modifié",
                                "data"   => $task
                            );

                        }else{
                            $data = array(
                                "status" => "Error",
                                "code"   => 200,
                                "msg"    => "Vous n'avez pas l'autorisation de modifier une tâche déjà crée par un autre utilisateur"
                            );

                        }
                    }else{
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
                            "data"    => $task
                        );

                    }


                }else{
                    $data = array(
                        "status" => "Error",
                        "code"   => 400,
                        "msg"    => "Task not created"
                    );
                }
            }


        }else{
            $data = array(
                "status" => "Error",
                "code"   => 200,
                "msg"    => "Message"
            );
        }
        return $helpers->json($data);

    }




    public function tasksAction(Request $request){
        $helpers  = $this->get(Helpers::class);
        $jwtAuth = $this->get(JwtAuth::class);

        $token = $request->get("authorization", null);
        $authCheck = $jwtAuth->checkToken($token);

        if($authCheck){
            $identity = $jwtAuth->checkToken($token,true);

            $em = $this->getDoctrine()->getManager();

            $dql = "SELECT t FROM BackendBundle:Task t WHERE t.user= {$identity->sub} ORDER BY t.id DESC";
            $query = $em->createQuery($dql);
            $page = $request->query->getInt('page',1);
            $paginator = $this->get('knp_paginator');
            $items_per_page = 10;
            $pagination = $paginator->paginate($query, $page,$items_per_page);
            $total_itemes_count = $pagination->getTotalItemCount();
            $data = array(
                'status' => 'Success',
                'code' => 200,
                'total_itemes_count' => $total_itemes_count,
                'page_actual' => $page,
                'items_per_page' => $items_per_page,
                'total_pages' => ceil($total_itemes_count/$items_per_page),
                'data' => $pagination
            );
        }else{
            $data = array(
                'status' => 'Error',
                  'code' => 400,
                   'msg' => 'autorisation non valide'
             );
        }
         return $helpers->json($data);
    }

    public function taskAction(Request $request,$id ){
      $helpers = $this->get(Helpers::class);
      $jwtAuth = $this->get(JwtAuth::class);

      $token = $request->get("authorization", null);
      $authCheck = $jwtAuth->checkToken($token);

      if($authCheck){
           $em = $this->getDoctrine()->getManager();
           $task = $em->getRepository("BackendBundle:Task")->find($id);

           $data = array(
              'status' => 'Succes',
              'code' => 400,
              'msg' => '',
              'data' => $task
          );
      }else{
          $data = array(
              'status' => 'Error',
              'code' => 400,
              'msg' => 'autorisation non valide'
          );
      }
        return $helpers->json($data);
    }

    public function  searchAction(Request $request,$search = null){
        $helpers = $this->get(Helpers::class);
        $jwtAuth = $this->get(JwtAuth::class );

        $token = $request->get("authorization", null);
        $authCheck = $jwtAuth->checkToken($token);
        if($authCheck){
            $idenetity = $jwtAuth->checkToken($token,true);
            $em = $this->getDoctrine()->getManager();
            //filter

            $filter = $request->get('filter',null);
            if(empty($filter)){
                $filter=null;
            }elseif($filter==1){
                $filter="new";
            }elseif($filter==2){
                $filter="todo";
            }else{
                $filter="finished";
            }
            //order
            $order = $request->get("order",null);
            if(empty($order)|| $order ==2){
                $order = "DESC";
            }else{
                $order = "ASC";
            }

            if($search){
                 $dql = "select t from BackendBundle:Task t where t.user = $idenetity->sub and (t.title like :search or t.description like :search) ";
            }else{
                $dql = "select t from BackendBundle:Task t
                        where t.user = $idenetity->sub";
                if($filter){
                    $dql .= " and t.status = :filter";
                }

            }
            // Set order
            $dql .= " order by t.id $order";
            $query = $em->createQuery($dql);

            if($filter)
                $query->setParameter("filter","$filter");
            if(!empty($search)){
                $query->setParameter("search","%$search%");
            }
            $tasks = $query->getResult();


            $data = array(
                'status' => 'Success',
                'code'   => 400,
                'data'   => $tasks
            );

        }else{
            $data = array(
                'status' => 'Error',
                'code'   => 400,
                'msg'    => 'autorisation non valide'
            );
        }
        return $helpers->json($data);
    }

    public function removeAction(Request $request,$id=null){
        $helpers  = $this->get(Helpers::class);
        $jwtAuth = $this->get(JwtAuth::class);

        $token = $request->get("authorization", null);
        $authCheck = $jwtAuth->checkToken($token);
        $data = array();
        if($authCheck){
            $data = array(
                "status" => "Error",
                "code"   => 400,
                "msg"    => "tache introuvable"
            );

                    $em = $this->getDoctrine()->getManager();
                    if($id){
                        $task =  $em->getRepository('BackendBundle:Task')->find($id);
                        $identity = $jwtAuth->checkToken($token,true);
                        if( $task && $identity->sub == $task->getUser()->getId()){

                            $em->remove($task);
                            $em->flush();

                            $data = array(
                                "status" => "Success",
                                "code"   => 200,
                                "msg"    => "Tâche bien supprimé"
                            );

                        }else{
                            $data = array(
                                "status" => "Error",
                                "code"   => 200,
                                "msg"    => "Vous n'avez pas l'autorisation de supprimé une tâche déjà crée par un autre utilisateur"
                            );

                        }
                }
            }
        return $helpers->json($data);

    }
}

