<?php

namespace App\Entity\Huwelijk;

use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\DateFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\MaxDepth;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use ActivityLogBundle\Entity\Interfaces\StringableInterface;


use App\Entity\Token;

/**
 * Ambtenaar
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
 *     message="Een huwelijk kan maar ��n primaire ambtenaar hebben"
 * )
 */

class HuwelijkAmbtenaar  implements StringableInterface
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
	 * Het RSIN van de organisatie waartoe deze Ambtenaar behoord. Dit moet een geldig RSIN zijn van 9 nummers en voldoen aan https://nl.wikipedia.org/wiki/Burgerservicenummer#11-proef. <br> Het RSIN word bepaald aan de hand van de gauthenticeerde applicatie en kan niet worden overschreven
	 *
	 * @var integer
	 * @ORM\Column(
	 *     type     = "integer",
	 *     length   = 9
	 * )
	 * @Assert\Length(
	 *      min = 8,
	 *      max = 9,
	 *      minMessage = "Het RSIN moet ten minste {{ limit }} karakters lang zijn",
	 *      maxMessage = "Het RSIN kan niet langer dan {{ limit }} karakters zijn"
	 * )
	 * @Groups({"read"})
	 * @ApiFilter(SearchFilter::class, strategy="exact")
	 * @ApiFilter(OrderFilter::class)
	 * @ApiProperty(
	 *     attributes={
	 *         "openapi_context"={
	 *             "title"="bronOrganisatie",
	 *             "type"="string",
	 *             "example"="123456789",
	 *             "required"="true",
	 *             "maxLength"=9,
	 *             "minLength"=8
	 *         }
	 *     }
	 * )
	 */
	public $bronOrganisatie;	
	
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
	 * @var string A reprecentation for the status ofthis object
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
	 * ambtenaar
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
	public $ambtenaar;
	
	/**
	 * Het type van dit huwelijk
	 *
	 * @var string
	 * @Assert\Choice({"trouwambtenaar", "bode", "behandelaar"})
	 * @ORM\Column(
	 *     type     = "string"
	 * )
	 * @Groups({"read", "write"})
	 * @ApiProperty(
	 *     attributes={
	 *         "swagger_context"={
	 *             "type"="string",
	 *             "enum"={"trouwambtenaar", "bode", "behandelaar"},
	 *             "example"="trouwambtenaar",
	 *             "default"="bode",
	 *             "default"="behandelaar"
	 *         }
	 *     }
	 * )
	 * @Groups({"read"})
	 */
	public $rol = "trouwambtenaar";
	
	/**
	 * Het tijdstip waarop dit Ambtenaren object is aangemaakt
	 *
	 * @var string Een "Y-m-d H:i:s" waarde bijvoorbeeld "2018-12-31 13:33:05" ofwel "Jaar-dag-maand uur:minuut:seconde"
	 * @Gedmo\Timestampable(on="create")
	 * @Assert\DateTime
	 * @ORM\Column(
	 *     type     = "datetime"
	 * )
	 * @Groups({"read"})
	 */
	public $registratiedatum;
	
	/**
	 * Het tijdstip waarop dit Ambtenaren object voor het laatst is gewijzigd.
	 *
	 * @var string Een "Y-m-d H:i:s" waarde bijvoorbeeld "2018-12-31 13:33:05" ofwel "Jaar-dag-maand uur:minuut:seconde"
	 * @Gedmo\Timestampable(on="update")
	 * @Assert\DateTime
	 * @ORM\Column(
	 *     type     = "datetime",
	 *     nullable	= true
	 * )
	 * @Groups({"read"})
	 */
	public $wijzigingsdatum;
	
	/**
	 * Het contact persoon voor deze ambtenaar
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
	 *             "format"="uri"
	 *         }
	 *     }
	 * )
	 * @Gedmo\Versioned
	 */
	public $contactPersoon;
	
	/**
	 * De eigenaar (applicatie) van dit object, wordt bepaald aan de hand van de geauthenticeerde applicatie die de ambtenaar heeft aangemaakt
	 *
	 * @var App\Entity\Applicatie $eigenaar
	 *
	 * @Gedmo\Blameable(on="create")
	 * @ORM\ManyToOne(targetEntity="App\Entity\Applicatie")
	 * @Groups({"read"})
	 */
	public $eigenaar;
	
	/**
	 * @return string
	 */
	public function toString(){
		// By convention, linking objects should render as the object they are linking to
		return strval($this->ambtenaar);
	}
	
	/**
	 * Vanuit rendering perspectief (voor bijvoorbeeld loging of berichten) is het belangrijk dat we een entiteit altijd naar string kunnen omzetten
	 */
	public function __toString()
	{
		return $this->toString();
	}
	
	/**
	 * De prePersist functie wordt aangeroepen wanneer de entiteit voor het eerst wordt opgeslagen in de database. Dit staat ons toe om een set aan additionele initiële waardes toe te voegen.
	 *
	 * @ORM\PrePersist
	 */
	public function prePersist()
	{
		$this->registratieDatum= new \ Datetime();
		// We want to add some default stuff here like products, productgroups, paymentproviders, templates, clientGroups, mailinglists and ledgers
				
		//Lets setup an token
		$this->token = new Token();
		$this->token->actie = 'Accepteer uitnodiging';
		$this->token->beschrijving = 'Accepteer de uitnodiging om als ambtenaar dit huwelijk te sluiten'; 
		$this->token->persoon = $this->ambtenaar->contactPersoon; // Niet van toepassing
		$this->token->objectType= 'App\Entity\Huwelijk\HuwelijkAmbtenaar';
		$this->token->objectId= $this->id;
		
		return $this;
	}
	
	
	public function getId(): ?int
	{
		return $this->id;
	}
}
