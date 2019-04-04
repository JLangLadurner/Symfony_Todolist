<?php

/*prework material symfony day 1*/
namespace AppBundle\Controller;

use AppBundle\Entity\Todo;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/*needs to added for each type*/
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class TodoController extends Controller

{
   /**
    * @Route("/", name="todo_list")
    */

   public function listAction(Request $request)
   {
   		$todos = $this->getDoctrine()
   		->getRepository('AppBundle:Todo')
   		->findAll();


       return $this->render('todo/index.html.twig', array(
       	'todos'=>$todos
       ));
   }
    /**
    * @Route("/create", name="todo_create")
    */

   public function createAction(Request $request)
   {
   	$todo = new Todo;

   	$form = $this->createFormBuilder($todo)
   	  ->add('name', TextType::class, array('attr' => array('class' => 'form-control', 'style' => 'margin-bottom: 15px')))
   	  ->add('category', TextType::class, array('attr' => array('class' => 'form-control', 'style' => 'margin-bottom: 15px')))
   	  ->add('description', TextareaType::class, array('attr' => array('class' => 'form-control', 'style' => 'margin-bottom: 15px')))
      ->add('priority', ChoiceType::class, array('choices'=> array('Low'=>'Low','Normal' => 'Normal', 'High' => 'High' ),'attr' => array('class' => 'form-control', 'style' => 'margin-bottom: 15px'))) /*dont forget ',' between choices and attr*/
      ->add('due_date', DateTimeType::class, array('attr' => array('class' => 'form-control', 'style' => 'margin-bottom: 15px')))
      ->add('save', SubmitType::class, array('label' => 'Create ToDo','attr' => array('class' => 'btn btn-primary', 'style' => 'margin-bottom: 15px')))
      ->getForm(); /*dont forget arrow when calling the function*/

      $form->handleRequest($request);

      /*insert if function - or what should happen, wenn the form has been subitted*/

      /*dont forget to include the form in create...*/

      if ($form->issubmitted() && $form->isValid()) {
      	/*Get Data*/

      	$name = $form['name']->getData();
      	$category = $form['category']->getData();
      	$description = $form['description']->getData();
      	$priority = $form['priority']->getData();
      	$dueDate = $form['due_date']->getData();

      	$now = new\Datetime('now');

      	//the set.. refer to setters in entity...

      	$todo->setName($name);
      	$todo->setCategory($category);
      	$todo->setDescription($description);
      	$todo->setPriority($priority);
      	$todo->setDueDate($dueDate);
      	$todo->setCreateDate($now);

      	$em = $this->getDoctrine()->getManager();
      	$em->persist($todo);
      	$em->flush();

      	$this->addFlash(
      			'notice',
      			'ToDo added'
      	);

      	return $this->redirectToRoute('todo_list'); //redirects back to the list overview

      }

      /*after or before including it in the create file don't forget to add the form view to $form(createView)*/
       return $this->render('todo/create.html.twig', array(
       	'form' => $form->createView()
       ));
   }
   /**
    * @Route("/edit/{id}", name="todo_edit")
    */

   public function editAction($id, Request $request)
   {
       $todo = $this->getDoctrine()
   		->getRepository('AppBundle:Todo')
   		->find($id);
		//copied from the 'create' and edited to fit...
   		$now = new\Datetime('now');

   		  $todo->setName($todo->getName());
      	$todo->setCategory($todo->getCategory());
      	$todo->setDescription($todo->getDescription());
      	$todo->setPriority($todo->getPriority());
      	$todo->setDueDate($todo->getDueDate());
      	$todo->setCreateDate($now);
   	

   	$form = $this->createFormBuilder($todo)
   	  ->add('name', TextType::class, array('attr' => array('class' => 'form-control', 'style' => 'margin-bottom: 15px')))
   	  ->add('category', TextType::class, array('attr' => array('class' => 'form-control', 'style' => 'margin-bottom: 15px')))
   	  ->add('description', TextareaType::class, array('attr' => array('class' => 'form-control', 'style' => 'margin-bottom: 15px')))
      ->add('priority', ChoiceType::class, array('choices'=> array('Low'=>'Low','Normal' => 'Normal', 'High' => 'High' ),'attr' => array('class' => 'form-control', 'style' => 'margin-bottom: 15px'))) /*dont forget ',' between choices and attr*/
      ->add('due_date', DateTimeType::class, array('attr' => array('class' => 'form-control', 'style' => 'margin-bottom: 15px')))
      ->add('save', SubmitType::class, array('label' => 'EditToDo','attr' => array('class' => 'btn btn-primary', 'style' => 'margin-bottom: 15px')))
      ->getForm(); /*dont forget arrow when calling the function*/

      $form->handleRequest($request);

      /*insert if function - or what should happen, wenn the form has been subitted*/

      /*dont forget to include the form in create...*/

      if ($form->issubmitted() && $form->isValid()) {
      	/*Get Data*/

      	$name = $form['name']->getData();
      	$category = $form['category']->getData();
      	$description = $form['description']->getData();
      	$priority = $form['priority']->getData();
      	$dueDate = $form['due_date']->getData();

      	$now = new\Datetime('now');

      	$em = $this->getDoctrine()->getManager();
      	//add this line
      	$todo = $em->getRepository('AppBundle:Todo')->find($id);

      	

      	$todo->setName($name);
      	$todo->setCategory($category);
      	$todo->setDescription($description);
      	$todo->setPriority($priority);
      	$todo->setDueDate($dueDate);
      	$todo->setCreateDate($now);

      	//doctrine Manager moved up
      	//persist deleted
      	$em->flush();

      	$this->addFlash(
      			'notice',
      			'ToDo Updated'
      	);
      	return $this->redirectToRoute('todo_list');
      }

       return $this->render('todo/edit.html.twig', array(
       	'todo'=>$todo,
       	'form'=>$form->createView()
       ));
   }

   /**
    * @Route("/details/{id}", name="todo_details")
    */

   public function detailsAction($id)
   {
   		$todo = $this->getDoctrine()
   		->getRepository('AppBundle:Todo')
   		->find($id);


       return $this->render('todo/details.html.twig', array(
       	'todo'=>$todo
       ));


   }
   
   /**
    * @Route("/delete/{id}", name="todo_delete")
    */

   public function deleteAction($id)
   {
   		$em = $this->getDoctrine()->getManager();
      	//add this line
      	$todo = $em->getRepository('AppBundle:Todo')->find($id);

      	$em->remove($todo);
      	$em->flush();

      	$this->addFlash(
      			'notice',
      			'ToDo Removed'
      	);

      	return $this->redirectToRoute('todo_list');

      }
   }
   

   
