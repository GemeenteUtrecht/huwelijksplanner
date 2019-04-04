<?php
/// https://symfony.com/doc/3.4/doctrine/repository.html

namespace App\Repository;

use Doctrine\ORM\EntityRepository;
use App\Entity\Agenda;

class AgendaRepository extends EntityRepository
{
	// Example funtion
	public function findAllOrderedByName()
	{
		
	}
		
	/**
	 * Gets the currently available slots for a agenda, taking bookings into consideration
	 *
	 * @param Agenda $agenda The agenda that we want to check for.
	 * @param DateTime $from The moment from when to check this agenda, default to NOW()
	 * @param DateTime $from The moment till when to check this agenda, defautls to NOW() + 1 month
	 *
	 * @return array Returns an array of available slots.
	 */
	public function getAvailability(Agenda $agenda,\DateTime $from = null, \DateTime $till  = null){
		if(!$from){ $from = New \Datetime();}
		if(!$till){ $till = New \Datetime('+1 month');}
		
		$availableSlots = $this->getAvailableSlots($agenda,$from,$till);
		$bookedSlots = $this->getABookedSlots($agenda,$from,$till);
	
	}
	
	
	/**
	 * Gets the currently available slots for a agenda, without taking bookings into consideration
	 *
	 * @param Agenda $agenda The agenda that we want to check for.
	 * @param DateTime $from The moment from when to check this agenda, default to NOW()
	 * @param DateTime $from The moment till when to check this agenda, defautls to NOW() + 1 month
	 *
	 * @return array Returns an array of availablity slots.
	 */
	public function getAvailableSlots(Agenda $agenda,\DateTime $from  = null, \DateTime $till  = null){
		if(!$from){ $from = New \Datetime();}
		if(!$till){ $till = New \Datetime('+1 month');}
		
		return $this->getEntityManager()
		->createQuery(
			'SELECT p FROM AppBundle:Product p ORDER BY p.name ASC'
			)
		->getResult();
		
	}
		
	/**
	 * Gets the currently booked slots for a agenda
	 *
	 * @param Agenda $agenda The agenda that we want to check for.
	 * @param DateTime $from The moment from when to check this agenda, default to NOW()
	 * @param DateTime $from The moment till when to check this agenda, defautls to NOW() + 1 month
	 *
	 * @return array Returns an array of booked slots.
	 */
	public function getBookedSlots(Agenda $agenda,\DateTime $from  = null, \DateTime $till  = null){
		if(!$from){ $from = New \Datetime();}
		if(!$till){ $till = New \Datetime('+1 month');}
		
		return $this->getEntityManager()
		->createQuery(
			'SELECT p FROM AppBundle:Product p ORDER BY p.name ASC'
			)
		->getResult();
	}
}
