<?php

namespace App\Entity\Huwelijk;

use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\MaxDepth;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use ActivityLogBundle\Entity\Interfaces\StringableInterface;


use App\Entity\Token;

/**
 * Persoon
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
 * @UniqueEntity(
 *     fields={"primair", "huwelijk"},
 *     message="Een huwelijk kan maar één primaire locatie hebben"
 * )
 */

class HuwelijkBijzonderAmbtenaar  implements StringableInterface
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
	 * Primair
	 *
	 * @var boolean
	 * @ORM\Column(
	 *     type     = "boolean",
	 *     nullable = true
	 * )
	 * @Groups({"read", "write"})
	 * @ApiProperty(
	 *     attributes={
	 *         "openapi_context"={
	 *             "title"="primair",
	 *             "type"="boolean",
	 *             "example"="true",
	 *             "description"="Bepaald of dit de huig gekozen ambtenaar is"
	 *         }
	 *     }
	 * )
	 */
	public $primair = false;
	
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
	 * @var \App\Entity\Agenda
	 * @ORM\ManyToOne(targetEntity="\App\Entity\Huwelijk", cascade={"persist", "remove"}, inversedBy="ambtenaren")
	 * @ORM\JoinColumn(name="huwelijk_id", referencedColumnName="id", nullable=true)
	 *
	 */
	public $huwelijk;
	
	/**
	 * Het type van dit issue <br /><b>Schema:</b> <a href="https://schema.org/additionalType">https://schema.org/additionalType</a>
	 *
	 * @var string
	 * @Assert\Choice({"voor een dag", "zelfstandig"})
	 * @ORM\Column(
	 *     type     = "string"
	 * )
	 * @Groups({"read", "write"})
	 * @ApiProperty(
	 *     attributes={
	 *         "openapi_context"={
	 *             "type"="string",
	 *             "enum"={"voor een dag", "zelfstandig"},
	 *             "example"="voor een dag",
	 *             "default"="voor een dag"
	 *         }
	 *     }
	 * )
	 * @Groups({"read", "write"})
	 */
	public $type = "voor een dag";
	
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
	 * De prePersist functie wordt aangeroepen wanneer de entiteit voor het eerst wordt opgeslagen in de database. Hierdoor kunnen we een set aan additionele initiÃ«le waardes toevoegen.
	 *
	 * @ORM\PrePersist
	 */
	public function prePersist()
	{
		$this->registratieDatum= new \ Datetime();
		// We want to add some default stuff here like products, productgroups, paymentproviders, templates, clientGroups, mailinglists and ledgers
		
		//Lets setup two tokens, one for the ambtenaar and one for the gemeente
		$this->token = new Token();
		$this->token->actie = 'Accepteer uitnodiging';
		$this->token->beschrijving = 'Accepteer de uitnodiging om dit huwelijk als bijzonder ambtenaar van de burgelijke stand te voltrekken'; 
		$this->token->persoon = $this->persoon; 
		$this->token->object  = 'App\Entity\Huwelijk\HuwelijkBijzonderAmbtenaar';
		$this->token->objectId= $this->id;
				
		return $this;
	}
	
	
	public function getId(): ?int
	{
		return $this->id;
	}
}
