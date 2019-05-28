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
use ActivityLogBundle\Entity\Interfaces\StringableInterface;


use App\Entity\Token;

/** 
 * Rol van een PERSOON of AMBTENAAR op een huwelijk
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

class Rol  implements StringableInterface
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
	public $id;
	
	/**
	 * Het RSIN van de organisatie waartoe deze Ambtenaar behoort. Dit moet een geldig RSIN zijn van 9 nummers en voldoen aan https://nl.wikipedia.org/wiki/Burgerservicenummer#11-proef. <br> Het RSIN wordt bepaald aan de hand van de geauthenticeerde applicatie en kan niet worden overschreven.
	 *
	 * @var integer
	 * @ORM\Column(
	 *     type     = "integer",
	 *     length   = 9
	 * )
	 * @Assert\Length(
	 *      min = 8,
	 *      max = 9,
	 *      minMessage = "Het RSIN moet minimaal {{ limit }} karakters lang zijn.",
	 *      maxMessage = "Het RSIN mag maximaal {{ limit }} karakters zijn."
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
	 *             "description"="URL-referentie naar de BRP inschrijving van deze persoon."
	 *         }
	 *     }
	 * )
	 */
	public $instemming;
	
	/**
	 * De rol 
	 *
	 * @var string
	 * @Assert\Choice({"huwelijk", "partnerschap"})
	 * @ORM\Column(
	 *     type     = "string"
	 * )
	 * @Groups({"read", "write"})
	 * @ApiProperty(
	 *     attributes={
	 *         "openapi_context"={
	 *             "type"="string",
	 *             "enum"={"partner", "getuige","ambtenaar"},
	 *             "example"="getuige",
	 *             "default"="getuige"
	 *         }
	 *     }
	 * )
	 * @Groups({"read", "write"})
	 */
	public $soort = "getuige";
	
	/**
	 * @var string Een representatie van de status van dit object.
	 * @Assert\DateTime
	 * @ORM\Column(
	 *     type     = "string",
	 *     nullable = true
	 * )
	 */
	public $status = "uitgenodigd";
	
	/**
	 * Het huwelijk waartoe deze partner behoort.
	 *
	 * @var \App\Entity\Huwelijk
	 * @ORM\ManyToOne(targetEntity="\App\Entity\Huwelijk", cascade={"persist", "remove"}, inversedBy="rollen")
	 * @ORM\JoinColumn(name="huwelijk_id", referencedColumnName="id", nullable=true)
	 *
	 */
	public $huwelijk;
	
	/**
	 * De eventuele rol waar deze rol betrekking op heeft, bijvoorbeeld getuigen voor een partner.
	 *
	 * @var \App\Entity\Huwelijk\HuwelijkPartner
	 * @ORM\ManyToOne(targetEntity="\App\Entity\Huwelijk\Rol", cascade={"persist", "remove"}, inversedBy="rollen")
	 * @ORM\JoinColumn(name="rol_id", referencedColumnName="id", nullable=true)
	 *
	 */
	public $rol;	
	
	/**
	 * Rollen die betrekking hebben op deze rol bijvoorbeel getuigen voor een partner.
	 *
	 * @var \Doctrine\Common\Collections\Collection|\App\Entity\Huwelijk\Rol[]|null
	 *
	 * @MaxDepth(3)
	 * @ORM\OneToMany(
	 * 		targetEntity="\App\Entity\Huwelijk\Rol",
	 * 		mappedBy="rol")
	 * @Groups({"read"})
	 *
	 */
	public $rollen;
		
	/**
	 * Houder van deze rol (B.v. persoon of ambtenaar).
	 *
	 * @ORM\Column(
	 *     type     = "string",
	 *     nullable = true
	 * )
	 * @Groups({"read", "write"})
	 * @ApiProperty(
	 *     attributes={
	 *         "openapi_context"={
	 *             "title"="Houder",
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
	public $houder;
	
	/**
	 * Het tijdstip waarop dit Rol object is aangemaakt.
	 *
	 * @var string Een "Y-m-d H:i:s" waarde bijvoorbeeld "2018-12-31 13:33:05" ofwel "Jaar-dag-maand uur:minuut:seconde."
	 * @Gedmo\Timestampable(on="create")
	 * @Assert\DateTime
	 * @ORM\Column(
	 *     type     = "datetime"
	 * )
	 * @Groups({"read"})
	 */
	public $registratiedatum;
	
	/**
	 * Het tijdstip waarop dit Rol object voor het laatst is gewijzigd.
	 *
	 * @var string Een "Y-m-d H:i:s" waarde bijvoorbeeld "2018-12-31 13:33:05" ofwel "Jaar-dag-maand uur:minuut:seconde."
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
	 * De eigenaar (applicatie) van dit object, wordt bepaald aan de hand van de geauthenticeerde applicatie die de ambtenaar heeft aangemaakt.
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
	public function toString()
	{
		// By convention, linking objects should render as the object they are linking to
		return "Rol: ".$this->id;
	}
	
	/**
	 * Vanuit rendering perspectief (voor bijvoorbeeld logging of berichten) is het belangrijk dat we een entiteit altijd naar string kunnen omzetten.
	 */
	public function __toString()
	{
		return $this->toString();
	}
}
