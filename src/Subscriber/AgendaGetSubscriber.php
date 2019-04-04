<?php
// api/src/Subscriber/HuwelijkAddPartnerSubscriber.php

namespace App\Subscriber;

use ApiPlatform\Core\EventListener\EventPriorities;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Serializer\SerializerInterface;
use Doctrine\ORM\EntityManagerInterface;


use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use App\Entity\Agenda;

final class AgendaGetSubscriber implements EventSubscriberInterface
{
	private $params;
	private $entityManager;
	private $serializer;
	
	public function __construct(ParameterBagInterface $params, EntityManagerInterface $entityManager, SerializerInterface $serializer)
	{
		$this->params = $params;
		$this->entityManager= $entityManager;
		$this->serializer= $serializer;
	}
	
	public static function getSubscribedEvents()
	{
		return [
				KernelEvents::VIEW => ['agenda', EventPriorities::PRE_VALIDATE]
		];
	}
	
	public function agenda(GetResponseForControllerResultEvent $event)
	{
		$agenda = $event->getControllerResult();
		$method = $event->getRequest()->getMethod();
				
		// Lats make sure that some one posts correctly
		if (!$agenda instanceof Agenda|| Request::METHOD_GET !== $method || $event->getRequest()->get('_route') != 'api_agendas_get_item') {
			return;
		}
				
		$json = $this->serializer->serialize(
				$agenda,
				'jsonld',['enable_max_depth' => true,'groups' => 'read']
				);
		
		$response = new Response(
			$json,
			Response::HTTP_OK,
			['content-type' => 'application/json+ld']
			);
		
		$event->setResponse($response);
		
		return;
	}
	
}