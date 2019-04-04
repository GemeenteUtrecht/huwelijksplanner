<?php

namespace App\Entity\Huwelijk;

use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\MaxDepth;
use ActivityLogBundle\Entity\Interfaces\StringableInterface;


use App\Entity\Token;

/**
 * Getuigen
 * 
 * Beschrijving
 * 
 * @category   	Entity
 *
 * @author     	Ruben van der Linde <ruben@conduction.nl>
 * @license    	EUPL 1.2 https://opensource.org/licenses/EUPL-1.2 *
 * @version    	1.0
 *
 * @link   		http//:www.conduction.nl
 * @package		Common Ground
 * @subpackage  Trouwen
 * 
 * @ApiResource
 * @ORM\Entity
 * @Gedmo\Loggable(logEntryClass="ActivityLogBundle\Entity\LogEntry")
 * @ORM\HasLifecycleCallbacks
 */

class HuwelijkGetuige  implements StringableInterface
{
	/**
	 * @var int|null
	 *
	 * @ORM\Id
	 * @ORM\GeneratedValue
	 * @ORM\Column(type="integer", options={"unsigned": true})
	 * @Groups({"read", "write"})
	 * @ApiProperty(iri="https://schema.org/identifier")
	 */
	private $id;
	
	/**
	 * Instemming
	 *
	 * @ORM\Column(
	 *     type     = "string",
	 *     nullable = true
	 * )
	 * @Groups({"read", "write"})
	 * @ApiProperty(
	 *     attributes={
	 *         "openapi_context"={
	 *             "title"="Contactpersoon",
	 *             "type"="url",
	 *             "example"="https://ref.tst.vng.cloud/zrc/api/v1/zaken/24524f1c-1c14-4801-9535-22007b8d1b65",
	 *             "required"="true",
	 *             "maxLength"=255,
	 *             "format"="uri",
	 *             "description"="URL-referentie naar de BRP inschrijving van dit persoon"
	 *         }
	 *     }
	 * )
	 */
	public $instemming;
	
	/**
	 * @var string A representation for the status of this object
	 * @Assert\DateTime
	 * @ORM\Column(
	 *     type     = "string",
	 *     nullable = true
	 * )
	 */
	public $status = "Uitgenodigd";
	
	/**
	 * Het Huwelijk waartoe deze partner behoort
	 *
	 * @var \App\Entity\Huwelijk
	 * @ORM\ManyToOne(targetEntity="\App\Entity\Huwelijk", cascade={"persist", "remove"}, inversedBy="getuigen")
	 * @ORM\JoinColumn(name="huwelijk_id", referencedColumnName="id", nullable=true)
	 *
	 */
	public $huwelijk;
	
	/**
	 * De partner waarvoor de getuigen getuigt
	 *
	 * @var \App\Entity\Huwelijk\HuwelijkPartner
	 * @ORM\ManyToOne(targetEntity="\App\Entity\Huwelijk\HuwelijkPartner", cascade={"persist", "remove"}, inversedBy="getuigen")
	 * @ORM\JoinColumn(name="partner_id", referencedColumnName="id", nullable=true)
	 *
	 */
	public $partner;
		
	/**
	 * Persoon
	 *
	 * @ORM\Column(
	 *     type     = "string",
	 *     nullable = true
	 * )
	 * @Groups({"read", "write"})
	 * @ApiProperty(
	 *     attributes={
	 *         "openapi_context"={
	 *             "title"="Contactpersoon",
	 *             "type"="url",
	 *             "example"="https://ref.tst.vng.cloud/zrc/api/v1/zaken/24524f1c-1c14-4801-9535-22007b8d1b65",
	 *             "required"="true",
	 *             "maxLength"=255,
	 *             "format"="uri",
	 *             "description"="URL-referentie naar de BRP inschrijving van dit persoon"
	 *         }
	 *     }
	 * )
	 * @Gedmo\Versioned
	 */
	public $persoon;
	
	/**
	 * @var string Een "Y-m-d H:i:s" waarde bijv. "2018-12-31 13:33:05" ofwel "Jaar-dag-maand uur:minut:seconde"
	 * @Assert\DateTime
	 * @ORM\Column(
	 *     type     = "datetime"
	 * )
	 * @Groups({"read"})
	 */
	public $registratieDatum;
	
	/**
	 * @return string
	 */
	public function toString(){
		// By convention, linking objects should render as the object they are linking to
		return strval($this->persoon);
	}
	
	/**
	 * Vanuit rendering perspectief (voor bijvoorbeeld logging of berichten) is het belangrijk dat we een entiteit altijd naar string kunnen omzetten.
	 */
	public function __toString()
	{
		return $this->toString();
	}
	
	/**
	 * De prePersist functie wordt aangeroepen wanneer de entiteit voor het eerst wordt opgeslagen in de database. Dat stelt ons in staat om een set aan additionele initiële waardes toe te voegen.
	 *
	 * @ORM\PrePersist
	 */
	public function prePersist()
	{
		$this->registratieDatum = new \ Datetime();
		// We want to add some default stuff here like products, productgroups, paymentproviders, templates, clientGroups, mailinglists and ledgers		
		
		//Lets setup an token
		$this->token = new Token();
		$this->token->actie = 'Accepteer uitnodiging';
		$this->token->beschrijving = 'Accepteer de uitnodiging om als getuigen te getuigen voor dit huwelijk'; 
		$this->token->persoon = $this->persoon; 
		$this->token->objectType= 'App\Entity\Huwelijk\HuwelijkGetuige';
		$this->token->objectId= $this->id;
		
		return $this;
	}
	
	
	public function getId(): ?int
	{
		return $this->id;
	}
}
