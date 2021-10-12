<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Model\User;
use AppBundle\Form\UserFormType;
use AppBundle\Form\UserFormEditType;


/**
 * @Route("/user")
 */
class UserController extends Controller{

	protected $model, $formType, $formEditType;

	public function __construct(){
		$this->model = User::class;
        $this->formType = UserFormType::class;
        $this->formEditType = UserFormEditType::class;
	}

	/**
     * @Route("/",name="user.index")
     * @Method("GET")
     */
	public function indexAction(Request $request){

        $user_id = $this->getUser()->getId();
        $search = $request->query->get('search');
		$query = $this->getDoctrine()
                ->getRepository($this->model)
                ->createQueryBuilder('x');

        $filterData = $query;   
        if ($search) {
            $or = $filterData->expr()->orX();
            $or->add($filterData->expr()->like('x.username', $filterData->expr()->literal('%' . $search . '%')));
            $or->add($filterData->expr()->like('x.email', $filterData->expr()->literal('%' . $search . '%')));
            $or->add($filterData->expr()->like('x.roles', $filterData->expr()->literal('%' . $search . '%')));
            $filterData->andWhere($or);
        }
        $result = $filterData->where('x.id != '.$user_id)->addOrderBy('x.id', 'desc')->getQuery();

        $limit = 10;
        $page = $request->query->getInt('page', 1);
        $num = ($page * $limit) - ($limit-1);
        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate($result,$page,$limit);
        $config = array(
            "data"=>$pagination,
            "num"=>$num,
            "page"=>$page,
            "search"=>$search
        );
		return $this->render('@App/User/index.html.twig',$config);
	}

    /**
     * @Route("/create",name="user.create")
     * @Method({"GET", "POST"})
     */
    public function createAction(Request $request){

        $model = new $this->model;
        $form = $this->createForm($this->formType);
        $form->handleRequest($request);

        $userManager = $this->get('fos_user.user_manager');
        $username = $form['username']->getData();
        $email = $form['email']->getData();

        $_username = $this->findUserByUsernameOrEmail($username);
        $_email = $this->findUserByUsernameOrEmail($email);

        if ($form->isSubmitted() && $form->isValid()) {

            if($_username){
                $this->addFlash('error', 'Username already exists !!');
            }else if($_email){
                $this->addFlash('error', 'Email already exists !!');
            }else{
                $password = $form['plainPassword']->getData();
                $groups = $form['groups']->getData();
                $enabled = $form['enabled']->getData();
                $user = $userManager->createUser();
                $user->setUsername($username);
                $user->setEmail($email);
                $user->setEmailCanonical($email);
                $user->setEnabled($enabled);
                $user->setPlainPassword($password);
                $user->setGroups($groups);
                $userManager->updateUser($user);
                $this->addFlash('notice', 'Data has been saved !!');
                return $this->redirectToRoute("user.index");
            }

        }

        
        $config = array("form"=>$form->createView());
        return $this->render('@App/User/create.html.twig',$config);
    }


    /**
     * @Route("/edit/{id}",name="user.edit")
     * @Method({"GET", "POST"})
     */
    public function editAction($id, Request $request){

        $userManager = $this->container->get('fos_user.user_manager');
        $user = $userManager->findUserBy(array('id'=>$id));
        $form = $this->createForm($this->formEditType, $user);
        $form->handleRequest($request);

        $username = $form['username']->getData();
        $email = $form['email']->getData();

        if ($form->isSubmitted() && $form->isValid()) {
            
            $_username = $this->findUserByUsernameOrEmail($username);
            $_email = $this->findUserByUsernameOrEmail($email);

            if($_username && $_username->getId() != $id){
                $this->addFlash('error', 'Username already exists !!');
            }else if($_email && $_username->getId() != $id){
                $this->addFlash('error', 'Email already exists !!');
            }else{

                $password = $form['plainPassword']->getData();
                $groups = $form['groups']->getData();
                $enabled = $form['enabled']->getData();
                $user->setUsername($username);
                $user->setEmail($email);
                $user->setEmailCanonical($email);
                $user->setEnabled($enabled);

                if($password){
                    $user->setPlainPassword($password);
                }
                    
                $user->setGroups($groups);
                $userManager->updateUser($user);
                $this->addFlash('notice', 'Data has been updated !!');
                return $this->redirectToRoute("user.index");

            }


        }

       

        $config = array("form"=>$form->createView());
        return $this->render('@App/User/edit.html.twig',$config);

    }


    /**
     * @Route("/show/{id}",name="user.show")
     * @Method("GET")
     */
    public function showAction($id){
        $userManager = $this->container->get('fos_user.user_manager');
        $user = $userManager->findUserBy(array('id'=>$id));
        $config = array("data"=>$user);
        return $this->render('@App/User/show.html.twig',$config);
    }


    /**
     * @Route("/delete/{id}",name="user.delete")
     * @Method("GET")
     */
    public function deleteAction($id){
    	$em = $this->getDoctrine()->getManager();
        $data = $em->getRepository($this->model)->find($id);
        $em->remove($data);
        $em->flush();
        $this->addFlash('notice', 'Data has been deleted !!');
        return $this->redirectToRoute("user.index");
    }


    private function findUserByUsernameOrEmail($value){
        $userManager = $this->container->get('fos_user.user_manager');
        $user = $userManager->findUserByUsernameOrEmail($value);
        return $user;
    }

}