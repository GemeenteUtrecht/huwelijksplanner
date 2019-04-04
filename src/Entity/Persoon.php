<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\MaxDepth;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use ActivityLogBundle\Entity\Interfaces\StringableInterface;

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
 * @subpackage  BRP
 * 
 *  @ApiResource( 
 *  collectionOperations={
 *  	"get"={
 *  		"normalizationContext"={"groups"={"read"}},
 *  		"denormalizationContext"={"groups"={"write"}},
 *      	"path"="/personen",
 *  		"openapi_context" = {
 *  		}
 *  	},
 *  	"post"={
 *  		"normalizationContext"={"groups"={"read"}},
 *  		"personen"={"groups"={"write"}},
 *      	"path"="/personen",
 *  		"openapi_context" = {
 *  		}
 *  	}
 *  },
 * 	itemOperations={
 *     "get"={
 *  		"normalizationContext"={"groups"={"read"}},
 *  		"denormalizationContext"={"groups"={"write"}},
 *      	"path"="/personen/{id}",
 *  		"openapi_context" = {
 *  		}
 *  	},
 *     "put"={
 *  		"normalizationContext"={"groups"={"read"}},
 *  		"denormalizationContext"={"groups"={"write"}},
 *      	"path"="/personen/{id}",
 *  		"openapi_context" = {
 *  		}
 *  	},
 *     "delete"={
 *  		"normalizationContext"={"groups"={"read"}},
 *  		"denormalizationContext"={"groups"={"write"}},
 *      	"path"="/personen/{id}",
 *  		"openapi_context" = {
 *  		}
 *  	},
 *     "log"={
 *         	"method"="GET",
 *         	"path"="/personen/{id}/log",
 *          "controller"= HuwelijkController::class,
 *     		"normalization_context"={"groups"={"read"}},
 *     		"denormalization_context"={"groups"={"write"}},
 *         	"openapi_context" = {
 *         		"summary" = "Logboek inzien",
 *         		"description" = "Geeft een array van eerdere versies en wijzigingen van dit object",
 *          	"consumes" = {
 *              	"application/json",
 *               	"text/html",
 *            	},
 *             	"produces" = {
 *         			"application/json"
 *            	},
 *             	"responses" = {
 *         			"200" = {
 *         				"description" = "Een overzicht van versies"
 *         			},	
 *         			"400" = {
 *         				"description" = "Ongeldige aanvraag"
 *         			},
 *         			"404" = {
 *         				"description" = "Huwelijk of aanvraag niet gevonden"
 *         			}
 *            	}            
 *         }
 *     },
 *     "revert"={
 *         	"method"="POST",
 *         	"path"="/personen/{id}/revert/{version}",
 *          "controller"= HuwelijkController::class,
 *     		"normalization_context"={"groups"={"read"}},
 *     		"denormalization_context"={"groups"={"write"}},
 *         	"openapi_context" = {
 *         		"summary" = "Versie terugdraaid",
 *         		"description" = "Herstel een eerdere versie van dit object. Dit is een destructieve actie die niet ongedaan kan worden gemaakt",
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
class Persoon implements StringableInterface
{
	/**
	 * Het identificatie nummer van deze Persoon <br /><b>Schema:</b> <a href="https://schema.org/identifier">https://schema.org/identifier</a>
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
	 * De unieke identificatie van dit object binnen de organisatie die dit object heeft gecreeerd. <br /><b>Schema:</b> <a href="https://schema.org/identifier">https://schema.org/identifier</a>
	 *
	 * @var string
	 * @ORM\Column(
	 *     type     = "string",
	 *     length   = 40,
	 *     nullable=true
	 * )
	 * @Assert\Length(
	 *      max = 40,
	 *      maxMessage = "Het RSIN kan niet langer dan {{ limit }} karakters zijn"
	 * )
	 * @Groups({"read", "write"})
	 * @ApiProperty(
	 *     attributes={
	 *         "openapi_context"={
	 *             "type"="string",
	 *             "example"="6a36c2c4-213e-4348-a467-dfa3a30f64aa",
	 *             "description"="De unieke identificatie van dit object de organisatie die dit object heeft gecreeerd.",
	 *             "maxLength"=40
	 *         }
	 *     }
	 * )
	 * @Gedmo\Versioned
	 */
	public $identificatie;
	
	/**
	 * Het huwelijk waartoe deze partner behoort
	 *
	 * @var \App\Entity\Organisatie
	 * @ORM\ManyToOne(targetEntity="\App\Entity\Organisatie", cascade={"persist", "remove"}, inversedBy="personen")
	 * @ORM\JoinColumn(referencedColumnName="id")
	 *
	 */
	public $bronOrganisatie;
	
	/**
	 * De naam van deze persoon <br /><b>Schema:</b> <a href="https://schema.org/givenName">https://schema.org/givenName</a>
	 *
	 * @var string
	 * 
	 * @ORM\Column(
	 *     type     = "string",
	 *     length   = 255,
	 *     nullable = true,
	 * )
	 * @Assert\Length(
	 *      min = 2,
	 *      max = 255,
	 *      minMessage = "De voornaam moet tenminste {{ limit }} karakters lang zijn",
	 *      maxMessage = "De voornaam kan niet langer dan {{ limit }} karakters zijn"
	 * )
	 * @Groups({"read", "write"})
	 * @ApiProperty(
	 * 	   iri="http://schema.org/name",
	 *     attributes={
	 *         "openapi_context"={
	 *             "type"="string",
	 *             "maxLength"=255,
	 *             "minLength"=2,
	 *             "example"="John"
	 *         }
	 *     }
	 * )
	 * @Gedmo\Versioned
	 **/
	public $voornamen;
	
	/**
	 * Voorvoegsel van de achternaam <br /><b>Schema:</b> <a href="https://schema.org/givenName">https://schema.org/givenName</a>
	 *
	 * @var string
	 *
	 * @ORM\Column(
	 *     type     = "string",
	 *     length   = 255,
	 *     nullable = true,
	 * )
	 * @Assert\Length(
	 *      min = 2,
	 *      max = 255,
	 *      minMessage = "Het voorvoegsel moet tenminste {{ limit }} karakters lang zijn",
	 *      maxMessage = "Het voorvoegsel kan niet langer dan {{ limit }} karakters zijn"
	 * )
	 * @Groups({"read", "write"})
	 * @ApiProperty(
	 * 	   iri="http://schema.org/name",
	 *     attributes={
	 *         "openapi_context"={
	 *             "type"="string",
	 *             "maxLength"=255,
	 *             "minLength"=2,
	 *             "example"="van der"
	 *         }
	 *     }
	 * )
	 * @Gedmo\Versioned
	 **/	
	public $voorvoegselGeslachtsnaam;
	
	/**
	 * De achternaam van deze persoon <br /><b>Schema:</b> <a href="https://schema.org/familyName">https://schema.org/familyName</a>
	 *
	 * @var string
	 *
	 * @ORM\Column(
	 *     type     = "string",
	 *     length   = 255, 
	 *     nullable = true,
	 * )
	 * @Assert\Length(
	 *      min = 2,
	 *      max = 255,
	 *      minMessage = "De geslachtsnaam moet tenminste {{ limit }} karakters lang zijn",
	 *      maxMessage = "De geslachtsnaam kan niet langer dan {{ limit }} karakters zijn"
	 * )
	 * @Groups({"read", "write"})
	 * @ApiProperty(
	 * 	   iri="http://schema.org/name",
	 *     attributes={
	 *         "openapi_context"={
	 *             "type"="string",
	 *             "maxLength"=255,
	 *             "minLength"=2,
	 *             "example"="Do"
	 *         }
	 *     }
	 * )
	 * @Gedmo\Versioned
	 **/
	public $geslachtsnaam;	
	
	/**
	 * Het emailadres van deze persoon <br /><b>Schema:</b> <a href="https://schema.org/email">https://schema.org/email</a>
	 *
	 * @var string
	 *
	 * @ORM\Column(
	 *     type     = "string",
	 *     length   = 255, 
	 *     nullable = true,
	 * )
	 * @Assert\Email(
     *     message = "Het emailadres '{{ value }}' is geen geldig emailadres.",
     *     checkMX = true
     * )
	 * @Assert\Length(
	 *      min = 8,
	 *      max = 255,
	 *      minMessage = "Het emailadres moet minimaal  {{ limit }} tekens lang zijn",
	 *      maxMessage = "Het emailadres mag maximaal {{ limit }} tekens lang zijn"
	 * )
	 * @Groups({"read", "write"})
	 * @ApiProperty(
	 * 	   iri="http://schema.org/name",
	 *     attributes={
	 *         "openapi_context"={
	 *             "type"="email",
	 *             "maxLength"=255,
	 *             "minLength"=8,
	 *             "example"="john@do.nl"
	 *         }
	 *     }
	 * )
	 * @Gedmo\Versioned
	 **/
	public $emailadres;
	
	/**
	 * Het telefoonnummer van deze persoon <br /><b>Schema:</b> <a href="https://schema.org/telephone">https://schema.org/telephone</a>
	 *
	 * @var string
	 *
	 * @Assert\Length(
	 *      min = 10,
	 *      max = 255,
	 *      minMessage = "Het telefoonnummer moet minimaal {{ limit }} tekens lang zijn",
	 *      maxMessage = "Het telefoonnummer mag maximaal {{ limit }} tekens lang zijn"
	 * )
	 * @Groups({"read", "write"})
	 * @ApiProperty(
	 * 	   iri="http://schema.org/name",
	 *     attributes={
	 *         "openapi_context"={
	 *             "type"="string",
	 *             "maxLength"=255,
	 *             "minLength"=10,
	 *             "example"="+31(0)6-12345678"
	 *         }
	 *     }
	 * )
	 * @Gedmo\Versioned
	 **/
	public $telefoonnummer;
	
	/**
	 * URL-referentie naar de agenda van dit persoon
	 *
	 * @ORM\Column(
	 *     type     = "string",
	 *     nullable = true
	 * )
	 * @Groups({"read", "write"})
	 * @ApiProperty(
	 *     attributes={
	 *         "openapi_context"={
	 *             "type"="url",
	 *             "example"="https://ref.tst.vng.cloud/zrc/api/v1/zaken/24524f1c-1c14-4801-9535-22007b8d1b65",
	 *             "required"="true",
	 *             "maxLength"=255,
	 *             "format"="uri",
	 *             "description"="URL-referentie naar de agenda van deze persoon"
	 *         }
	 *     }
	 * )
	 * @Gedmo\Versioned
	 */
	public $agenda;
	
	/**
	 * Het tijdstip waarop dit object is aangemaakt.
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
	 * Het tijdstip waarop dit object voor het laatst is gewijzigd.
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
	 * De taal waarin de informatie van  dit object is opgesteld <br /><b>Schema:</b> <a href="https://www.ietf.org/rfc/rfc3066.txt">https://www.ietf.org/rfc/rfc3066.txt</a>
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
	 *      minMessage = "De taal moet tenminste {{ limit }} karakters lang zijn",
	 *      maxMessage = "De taal kan niet langer dan {{ limit }} karakters zijn"
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
	 * @return string
	 */
	public function toString(){
		// If there is a voorvoegselGeslachtsnaam we want to add a save between voorvoegselGeslachtsnaam and geslachtsnaam;
		$voorvoegselGeslachtsnaam = $this->voorvoegselGeslachtsnaam;
		if($voorvoegselGeslachtsnaam){$voorvoegselGeslachtsnaam=$voorvoegselGeslachtsnaam.' ';}
		// Lets render the name
		return "{$this->voornamen} {$voorvoegselGeslachtsnaam}{$this->geslachtsnaam}";
	}
	
	/**
	 * Vanuit rendering perspectief (voor bijvoorbeeld logging of berichten) is het belangrijk dat we een entiteit altijd naar string kunnen omzetten.
	 */
	public function __toString()
	{
		return $this->toString();
	}
	
	/* @todo registratie datum */
		
	public function getHuwelijkspartner()
	{
		return $this->huwelijkspartner;
	}
	
	public function setHuwelijkspartner($huwelijkspartner)
	{
		$this->huwelijkspartner = $huwelijkspartner;
		return $this;
	} 
	
	public function getEmailadres()
	{
		return $this->emailadres;
	}
	
	public function setEmailadres($emailadres)
	{
		$this->emailadres = $emailadres;
	}
	
	public function getTelefoonnummer()
	{
		return $this->telefoonnummer;
	}
	
	public function setTelefoonnummer($telefoonnummer)
	{
		$this->telefoonnummer = $telefoonnummer;
	}	
	
	
}
