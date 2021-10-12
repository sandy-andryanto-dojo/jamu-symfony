<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Model\Category;
use AppBundle\Form\CategoryFormType;

/**
 * @Route("/category")
 */
class CategoryController extends Controller{

	protected $model, $formType;

	public function __construct(){
		$this->model = Category::class;
		$this->formType = CategoryFormType::class;
	}

	/**
     * @Route("/",name="category.index")
     * @Method("GET")
     */
	public function indexAction(Request $request){

        $search = $request->query->get('search');

		$query = $this->getDoctrine()
                ->getRepository($this->model)
                ->createQueryBuilder('x');

        $filterData = $query;   
        if ($search) {
            $or = $filterData->expr()->orX();
            $or->add($filterData->expr()->like('x.name', $filterData->expr()->literal('%' . $search . '%')));
            $or->add($filterData->expr()->like('x.description', $filterData->expr()->literal('%' . $search . '%')));
            $filterData->andWhere($or);
        }
        $result = $filterData->addOrderBy('x.id', 'desc')->getQuery();

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
		return $this->render('@App/Category/index.html.twig',$config);
	}

    /**
     * @Route("/create",name="category.create")
     * @Method({"GET", "POST"})
     */
    public function createAction(Request $request){

    	$model = new $this->model;
        $form = $this->createForm($this->formType);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
        	$em = $this->getDoctrine()->getManager();
        	$em->persist($form->getData());
            $em->flush();
            $this->addFlash('notice', 'Data has been saved !!');
            return $this->redirectToRoute("category.index");
        }

        $config = array("form"=>$form->createView());
        return $this->render('@App/Category/create.html.twig',$config);
    }


    /**
     * @Route("/edit/{id}",name="category.edit")
     * @Method({"GET", "POST"})
     */
    public function editAction($id, Request $request){

    	$em = $this->getDoctrine()->getManager();
        $data = $em->getRepository($this->model)->find($id);
        $form = $this->createForm($this->formType, $data);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
        	$em->flush();
            $this->addFlash('notice', 'Data has been updated !!');
            return $this->redirectToRoute("category.index");
        }

        $config = array("form"=>$form->createView());
        return $this->render('@App/Category/edit.html.twig',$config);

    }


    /**
     * @Route("/show/{id}",name="category.show")
     * @Method("GET")
     */
    public function showAction($id){
    	$data = $this->getDoctrine()->getRepository($this->model)->find($id);
    	$config = array("data"=>$data);
        return $this->render('@App/Category/show.html.twig',$config);
    }


    /**
     * @Route("/delete/{id}",name="category.delete")
     * @Method("GET")
     */
    public function deleteAction($id){
    	$em = $this->getDoctrine()->getManager();
    	$data = $em->getRepository($this->model)->find($id);
    	$em->remove($data);
    	$em->flush();
    	$this->addFlash('notice', 'Data has been deleted !!');
    	return $this->redirectToRoute("category.index");
    }

}