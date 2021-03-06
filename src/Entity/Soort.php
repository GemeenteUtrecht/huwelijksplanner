<?php

namespace App\Entity;

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

/**
 * Soort
 * 
 * Een bepaald type huwelijk, met daaraan gekoppelde locaties, ambtenaren en producten. Bijvoorbeeld gratis of simpel
 * 
 * @category   	Entity
 *
 * @author     	Ruben van der Linde <ruben@conduction.nl>
 * @license    	EUPL 1.2 https://opensource.org/licenses/EUPL-1.2 *
 * @version    	1.0
 *
 * @link   		http//:www.conduction.nl
 * @package		Common Ground
 * @subpackage  BRP
 * 
 *  @ApiResource( 
 *  collectionOperations={
 *  	"get"={
 *  		"normalizationContext"={"groups"={"read"},"enable_max_depth" = true, "circular_reference_handler"},
 *  		"denormalizationContext"={"groups"={"write"},"enable_max_depth" = true, "circular_reference_handler"},
 *      	"path"="/type",
 *  		"openapi_context" = {
 * 				"summary" = "Haalt een verzameling van huwelijkstypes op."
 *  		}
 *  	},
 *  	"post"={
 *  		"normalizationContext"={"groups"={"read"},"enable_max_depth" = true, "circular_reference_handler"},
 *  		"personen"={"groups"={"write"},"enable_max_depth" = true, "circular_reference_handler"},
 *      	"path"="/personen",
 *  		"openapi_context" = {
 * 				"summary" = "Voeg een persoon toe aan een huwelijk."
 *  		}
 *  	}
 *  },
 * 	itemOperations={
 *     "get"={
 *  		"normalizationContext"={"groups"={"read"},"enable_max_depth" = true, "circular_reference_handler"},
 *  		"denormalizationContext"={"groups"={"write"},"enable_max_depth" = true, "circular_reference_handler"},
 *      	"path"="/types/{id}",
 *  		"openapi_context" = {
 * 				"summary" = "Haalt een specifiek huwelijks type op."
 *  		}
 *  	},
 *     "put"={
 *  		"normalizationContext"={"groups"={"read"},"enable_max_depth" = true, "circular_reference_handler"},
 *  		"denormalizationContext"={"groups"={"write"},"enable_max_depth" = true, "circular_reference_handler"},
 *      	"path"="/types/{id}",
 *  		"openapi_context" = {
 * 				"summary" = "Vervang een specifiek issue huwelijks type."
 *  		}
 *  	},
 *     "delete"={
 *  		"normalizationContext"={"groups"={"read"},"enable_max_depth" = true, "circular_reference_handler"},
 *  		"denormalizationContext"={"groups"={"write"},"enable_max_depth" = true, "circular_reference_handler"},
 *      	"path"="/types/{id}",
 *  		"openapi_context" = {
 * 				"summary" = "Verwijder een specifiek huwelijks type."
 *  		}
 *  	},
 *     "log"={
 *         	"method"="GET",
 *         	"path"="/types/{id}/log",
 *          "controller"= HuwelijkController::class,
 *     		"normalization_context"={"groups"={"read"},"enable_max_depth" = true, "circular_reference_handler"},
 *     		"denormalization_context"={"groups"={"write"},"enable_max_depth" = true, "circular_reference_handler"},
 *         	"openapi_context" = {
 *         		"summary" = "Logboek inzien",
 *         		"description" = "Geeft een array van eerdere versies en wijzigingen van dit object.",
 *          	"consumes" = {
 *              	"application/json",
 *               	"text/html",
 *            	}           
 *         }
 *     },
 *     "revert"={
 *         	"method"="POST",
 *         	"path"="/types/{id}/revert/{version}",
 *          "controller"= HuwelijkController::class,
 *     		"normalization_context"={"groups"={"read"},"enable_max_depth" = true, "circular_reference_handler"},
 *     		"denormalization_context"={"groups"={"write"},"enable_max_depth" = true, "circular_reference_handler"},
 *         	"openapi_context" = {
 *         		"summary" = "Versie herstellen.",
 *         		"description" = "Herstel een eerdere versie van dit object. Dit is een destructieve actie die niet ongedaan kan worden gemaakt.",
 *          	"consumes" = {
 *              	"application/json",
 *               	"text/html",
 *            	},
 *             	"produces" = {
 *         			"application/json"
 *            	},
 *             	"responses" = {
 *         			"202" = {
 *         				"description" = "Teruggedraaid naar eerdere versie"
 *         			},	
 *         			"400" = {
 *         				"description" = "Ongeldige aanvraag"
 *         			},
 *         			"404" = {
 *         				"description" = "Huwelijk of aanvraag niet gevonden"
 *         			}
 *            	}            
 *         }
 *     }
 *  }
 * )
 * @ORM\Entity
 * @Gedmo\Loggable(logEntryClass="ActivityLogBundle\Entity\LogEntry")
 * @ORM\HasLifecycleCallbacks
 * @UniqueEntity(
 *     fields={"identificatie", "bronOrganisatie"},
 *     message="De identificatie dient uniek te zijn voor de bronOrganisatie"
 * )
 */
class Soort implements StringableInterface
{
	/**
	 * Het identificatienummer van deze Persoon. <br /><b>Schema:</b> <a href="https://schema.org/identifier">https://schema.org/identifier</a>.
	 * 
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
	 * De unieke identificatie van dit object binnen de organisatie die dit object heeft gecreëerd. <br /><b>Schema:</b> <a href="https://schema.org/identifier">https://schema.org/identifier</a>.
	 *
	 * @var string
	 * @ORM\Column(
	 *     type     = "string",
	 *     length   = 40,
	 *     nullable=true
	 * )
	 * @Assert\Length(
	 *      max = 40,
	 *      maxMessage = "Het RSIN kan niet langer dan {{ limit }} karakters zijn."
	 * )
	 * @Groups({"read", "write"})
	 * @ApiProperty(
	 *     attributes={
	 *         "openapi_context"={
	 *             "type"="string",
	 *             "example"="6a36c2c4-213e-4348-a467-dfa3a30f64aa",
	 *             "description"="De unieke identificatie van dit object de organisatie die dit object heeft gecreëerd.",
	 *             "maxLength"=40
	 *         }
	 *     }
	 * )
	 * @Gedmo\Versioned
	 */
	public $identificatie;
	
	/**
	 * Het RSIN van de organisatie waartoe dit soort object behoort. Dit moet een geldig RSIN zijn van 9 nummers en voldoen aan https://nl.wikipedia.org/wiki/Burgerservicenummer#11-proef. <br> Het RSIN wordt bepaald aan de hand van de geauthenticeerde applicatie en kan niet worden overschreven.
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
	 * De naam van deze huwelijks soort <br /><b>Schema:</b> <a href="https://schema.org/name">https://schema.org/name</a>.
	 *
	 * @var string
	 *
	 * @Gedmo\Translatable
	 * @Gedmo\Versioned
	 * @ORM\Column(
	 *     type     = "string",
	 *     length   = 255
	 * )
	 * @Assert\NotNull
	 * @Assert\Length(
	 *      min = 5,
	 *      max = 255,
	 *      minMessage = "De naam moet minimaal {{ limit }} karakters lang zijn.",
	 *      maxMessage = "De naam mag maximaal {{ limit }} karakters zijn."
	 * )
	 * @Groups({"read", "write"})
	 * @ApiProperty(
	 * 	   iri="http://schema.org/name",
	 *     attributes={
	 *         "swagger_context"={
	 *             "type"="string",
	 *             "minLength"=5,
	 *             "maxLength"=255,
	 *             "example"="Trouwzaal"
	 *         }
	 *     }
	 * )
	 **/
	public $naam;
	
	/**
	 * Een korte samenvattende tekst over deze huwelijk soort bedoeld ter introductie.  <br /><b>Schema:</b> <a href="https://schema.org/description">https://schema.org/description</a>.
	 *
	 * @var string
	 *
	 * @Gedmo\Translatable
	 * @Gedmo\Versioned
	 * @ORM\Column(
	 *     type     = "text"
	 * )
	 * @Assert\NotNull
	 * @Assert\Length(
	 *      min = 25,
	 *      max = 2000,
	 *      minMessage = "De sammnvatting moet minimaal {{ limit }} karakters lang zijn",
	 *      maxMessage = "De samenvatting mag maximaal {{ limit }} karakters zijn"
	 * )
	 *
	 * @Groups({"read", "write"})
	 * @ApiProperty(
	 * 	  iri="https://schema.org/description",
	 *     attributes={
	 *         "swagger_context"={
	 *             "type"="string",
	 *             "minLength"=25,
	 *             "maxLength"=2000,
	 *             "example"="Deze prachtige locatie is zeker het aanbevelen waard."
	 *         }
	 *     }
	 * )
	 **/
	public $samenvatting;
	
	/**
	 * Een uitgebreide beschrijvende tekst over deze huwelijk soort bedoeld ter verdere verduidelijking.  <br /><b>Schema:</b> <a href="https://schema.org/description">https://schema.org/description</a>.
	 *
	 * @var string
	 *
	 * @Gedmo\Translatable
	 * @Gedmo\Versioned
	 * @ORM\Column(
	 *     type     = "text"
	 * )
	 * @Assert\NotNull
	 * @Assert\Length(
	 *      min = 25,
	 *      max = 2000,
	 *      minMessage = "De beschrijving moet minimaal {{ limit }} karakters lang zijn.",
	 *      maxMessage = "De beschrijving mag maximaal {{ limit }} karakters zijn."
	 * )
	 * @Groups({"read", "write"})
	 * @ApiProperty(
	 * 	  iri="https://schema.org/description",
	 *     attributes={
	 *         "swagger_context"={
	 *             "type"="string",
	 *             "minLength"=25,
	 *             "maxLength"=2000,
	 *             "example"="Deze uitsterst sfeervolle trouwzaal is de droom van ieder koppel."
	 *         }
	 *     }
	 * )
	 **/
	public $beschrijving;	
	
	/**
	 * Het primaire product dat wordt gebruikt om dit huwelijk te verrekenen.
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
	 *             "description"="URL-referentie naar een product"
	 *         }
	 *     }
	 * )
	 * @Gedmo\Versioned
	 */
	public $product;
	
	/**
	 *  Additionele producten (zoals trouwboekje) die voor dit soort huwelijk kunnen worden gekozen.
	 *
	 * @var array
	 * @ORM\Column(
	 *  	type="array",
	 *  	nullable=true
	 *  )
	 * @Groups({"read", "write"})
	 * @ApiProperty(
	 *     attributes={
	 *         "openapi_context"={
	 *             "title"="issues",
	 *             "type"="array",
	 *             "example"="[]",
	 *             "description"="Additionele producten (zoals trouwboekje) die voor dit soort huwelijk kunnen worden gekozen."
	 *         }
	 *     }
	 * )
	 */
	public $extraProducten;
	
	/**
	 *  Beschikbare locaties voor dit soort huwelijk.
	 *
	 * @var array
	 * @ORM\Column(
	 *  	type="array",
	 *  	nullable=true
	 *  )
	 * @Groups({"read", "write"})
	 * @ApiProperty(
	 *     attributes={
	 *         "openapi_context"={
	 *             "title"="issues",
	 *             "type"="array",
	 *             "example"="[]",
	 *             "description"="Beschikbare locaties voor dit soort huwelijk"
	 *         }
	 *     }
	 * )
	 */
	public $locaties;	
	
	/**
	 * Beschikbare ambtenaren voor dit soort huwelijk.
	 *
	 * @var array
	 * @ORM\Column(
	 *  	type="array",
	 *  	nullable=true
	 *  )
	 * @Groups({"read", "write"})
	 * @ApiProperty(
	 *     attributes={
	 *         "openapi_context"={
	 *             "title"="issues",
	 *             "type"="array",
	 *             "example"="[]",
	 *             "description"="De instellingen voor deze organisatie, kijk in de documentatie van deze api voor de mogelijke instellingen"
	 *         }
	 *     }
	 * )
	 */
	public $ambtenaren;
	
	/**
	 * De huwelijken die gebruik maken van deze huwelijk soort.
	 *
	 * @todo eigenlijk setten met een primary flag op het onderliggende object en dan een collection filter
	 *
	 * @var \App\Entity\Huwelijk
	 * @ORM\OneToMany(
	 * 		targetEntity="\App\Entity\Huwelijk",
	 *  	mappedBy="soort", 
	 * 		fetch="EXTRA_LAZY"
	 * )
	 *
	 */
	public $huwelijken;
		
	/**
	 * De taal waarin de informatie van  dit object is opgesteld <br /><b>Schema:</b> <a href="https://www.ietf.org/rfc/rfc3066.txt">https://www.ietf.org/rfc/rfc3066.txt</a>.
	 *
	 * @var string Een Unicode language identifier, ofwel RFC 3066 taalcode.
	 *
	 * @ORM\Column(
	 *     type     = "string",
	 *     length   = 17
	 * )
	 * @Groups({"read", "write"})
	 * @Assert\Language
	 * @Assert\Length(
	 *      min = 2,
	 *      max = 17,
	 *      minMessage = "De taal moet minimaal {{ limit }} karakters lang zijn.",
	 *      maxMessage = "De taal mag maximaal {{ limit }} karakters zijn."
	 * )
	 * @ApiProperty(
	 *     attributes={
	 *         "openapi_context"={
	 *             "type"="string",
	 *             "maxLength"=17,
	 *             "minLength"=2,
	 *             "example"="NL"
	 *         }
	 *     }
	 * )
	 * @Gedmo\Versioned
	 **/
	public $taal = 'nl';
	
	/**
	 * Het tijdstip waarop dit Soort huwelijk is aangemaakt.
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
	 * Het tijdstip waarop dit Soort huwelijk voor het laatst is gewijzigd.
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
	 * De contactpersoon voor het Soort huwelijk.
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
	 * Met eigenaar wordt bijgehouden welke applicatie verantwoordelijk is voor het object, en daarvoor de rechten beheert en uitgeeft. De eigenaar kan dan ook worden gezien in de trant van autorisatie en configuratie, in plaats van als onderdeel van het datamodel.
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
		// Lets render the name
		return $this->naam;
	}
	
	/**
	 * Vanuit rendering perspectief (voor bijvoorbeeld logging of berichten) is het belangrijk dat we een entiteit altijd naar string kunnen omzetten.
	 */
	public function __toString()
	{
		return $this->toString();
	}
	
	public function getUrl()
	{
		return 'http://trouwen.demo.zaakonline.nl/type/'.$this->id;
	}	
	
	
}
