<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use ActivityLogBundle\Entity\Interfaces\StringableInterface;

/**
 * Organisatie
 * 
 * Een orginisatie of deeld daarvan dat deelneemt aan Common Grounds, organisatie objecten worden gebruikt voor het opslaan van configuratie instellingen.
 * 
 * @category   	Entity
 *
 * @author     	Ruben van der Linde <ruben@conduction.nl>
 * @license    	EUPL 1.2 https://opensource.org/licenses/EUPL-1.2 *
 * @version    	1.0
 *
 * @link   		http//:www.conduction.nl
 * @package		Common Ground
 * 
 *  @ApiResource( 
 *  collectionOperations={
 *  	"get"={
 *  		"normalizationContext"={"groups"={"read"}},
 *  		"denormalizationContext"={"groups"={"write"}},
 *      	"path"="/organisaties",
 *  		"openapi_context" = {
 *  		}
 *  	},
 *  	"post"={
 *  		"normalizationContext"={"groups"={"read"}},
 *  		"denormalizationContext"={"groups"={"write"}},
 *      	"path"="/organisaties",
 *  		"openapi_context" = {
 *  		}
 *  	}
 *  },
 * 	itemOperations={
 *     "get"={
 *  		"normalizationContext"={"groups"={"read"}},
 *  		"denormalizationContext"={"groups"={"write"}},
 *      	"path"="/organisaties/{id}",
 *  		"openapi_context" = {
 *  		}
 *  	},
 *     "put"={
 *  		"normalizationContext"={"groups"={"read"}},
 *  		"denormalizationContext"={"groups"={"write"}},
 *      	"path"="/organisaties/{id}",
 *  		"openapi_context" = {
 *  		}
 *  	},
 *     "delete"={
 *  		"normalizationContext"={"groups"={"read"}},
 *  		"denormalizationContext"={"groups"={"write"}},
 *      	"path"="/organisaties/{id}",
 *  		"openapi_context" = {
 *  		}
 *  	},
 *     "log"={
 *         	"method"="GET",
 *         	"path"="/organisaties/{id}/log",
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
 *         	"path"="/organisaties/{id}/revert/{version}",
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
 * @UniqueEntity("rsin")
 * @ORM\HasLifecycleCallbacks
 */
class Organisatie implements StringableInterface
{
	/**
	 * Het unieke identificatie nummer van deze organisatie binnen deze api <br /><b>Schema:</b> <a href="https://schema.org/identifier">https://schema.org/identifier</a>
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
	 * Het RSIN van deze organisatie. Dit moet een geldig RSIN zijn van 9 nummers en voldoen aan https://nl.wikipedia.org/wiki/Burgerservicenummer#11-proef
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
	 * @Groups({"read", "write"})
	 * @ApiProperty(
	 *     attributes={
	 *         "openapi_context"={
	 *             "title"="Organisatie",
	 *             "type"="string",
	 *             "example"="123456789",
	 *             "required"="true",
	 *             "maxLength"=9,
	 *             "minLength"=8,
	 *             "description"="Het RSIN van deze organisatie. Dit moet een geldig RSIN zijn van 9 nummers en voldoen aan https://nl.wikipedia.org/wiki/Burgerservicenummer#11-proef"
	 *         }
	 *     }
	 * )
	 */
	public $rsin;	
	
	/**
	 * Het KVK nummer van deze organisatie
	 *
	 * @var integer
	 * @ORM\Column(
	 *     type     = "integer",
	 *     length   = 9
	 * )
	 * @Groups({"read", "write"})
	 * @Assert\Length(
	 *      min = 8,
	 *      max = 9,
	 *      minMessage = "Het KVK nummer moet ten minste {{ limit }} karakters lang zijn",
	 *      maxMessage = "Het KVK nummer kan niet langer dan {{ limit }} karakters zijn"
	 * )
	 * @ApiProperty(
	 *     attributes={
	 *         "openapi_context"={
	 *             "title"="kvk",
	 *             "type"="string",
	 *             "example"="12345678",
	 *             "required"="true",
	 *             "maxLength"=8,
	 *             "minLength"=9,
	 *             "description"="Het KVK nummer van deze organisatie"
	 *         }
	 *     }
	 * )
	 */
	public $kvk;
	
	
	/**
	 * Het BTW nummer van deze organisatie https://www.belastingdienst.nl/wps/wcm/connect/bldcontentnl/belastingdienst/zakelijk/btw/administratie_bijhouden/btw_nummers_controleren/uw_btw_nummer, het btw nummer moet het RSIN nummer bevatten.
	 *
	 * @var string
	 * @ORM\Column(
	 *     type     = "string",
	 *     length   = 14
	 * )
	 * @Groups({"read", "write"})
	 * @Assert\Length(
	 *      min = 14,
	 *      max = 14,
	 *      minMessage = "Het BTW nummer moet ten minste {{ limit }} karakters lang zijn",
	 *      maxMessage = "Het BTW nummer kan niet langer dan {{ limit }} karakters zijn"
	 * )
	 * @ApiProperty(
	 *     attributes={
	 *         "openapi_context"={
	 *             "title"="btw",
	 *             "type"="string",
	 *             "example"="NL123456789B01",
	 *             "required"="true",
	 *             "maxLength"=14,
	 *             "minLength"=14,
	 *             "description"="Het BTW nummer van deze organisatie https://www.belastingdienst.nl/wps/wcm/connect/bldcontentnl/belastingdienst/zakelijk/btw/administratie_bijhouden/btw_nummers_controleren/uw_btw_nummer"
	 *         }
	 *     }
	 * )
	 */
	public $btw;
	
	
	/**
	 * Het EORI (Europese Douane NR) van deze organisatie, zie ook https://www.belastingdienst.nl/wps/wcm/connect/bldcontentnl/belastingdienst/douane_voor_bedrijven/naslagwerken_en_overige_informatie/eori_nummer/
	 *
	 * @var string
	 * @ORM\Column(
	 *     type     = "string",
	 *     length   = 14
	 * )
	 * @Groups({"read", "write"})
	 * @Assert\Length(
	 *      min = 5,
	 *      max = 14,
	 *      minMessage = "Het EORI nummer moet ten minste {{ limit }} karakters lang zijn",
	 *      maxMessage = "Het EORI nummer  kan niet langer dan {{ limit }} karakters zijn"
	 * )
	 * @ApiProperty(
	 *     attributes={
	 *         "openapi_context"={
	 *             "title"="eori",
	 *             "type"="string",
	 *             "example"="123456789",
	 *             "required"="true",
	 *             "maxLength"=14,
	 *             "minLength"=5,
	 *             "description"="Het EORI (Europese Douane NR) van deze organisatie, zie ook https://www.belastingdienst.nl/wps/wcm/connect/bldcontentnl/belastingdienst/douane_voor_bedrijven/naslagwerken_en_overige_informatie/eori_nummer/"
	 *         }
	 *     }
	 * )
	 */
	public $eori;
	
	/**
	 * De naam van deze organisatie <br /><b>Schema:</b> <a href="https://schema.org/name">https://schema.org/name</a>
	 *
	 * @var string
	 *
	 * @ORM\Column(
	 *     type     = "string",
	 *     length   = 255
	 * )
	 * @Assert\NotNull
	 * @Assert\Length(
	 *      min = 5,
	 *      max = 255,
	 *      minMessage = "De naam moet tenminste {{ limit }} karakters lang zijn",
	 *      maxMessage = "De naam kan niet langer dan {{ limit }} karakters zijn"
	 * )
	 * @Groups({"read", "write"})
	 * @ApiProperty(
	 * 	   iri="http://schema.org/name",
	 *     attributes={
	 *         "openapi_context"={
	 *             "type"="string",
	 *             "maxLength"=255,
	 *             "minLength"=5,
	 *             "example"="Gemeente Zuiddrecht"
	 *         }
	 *     }
	 * )
	 **/
	public $naam;
	
	/**
	 * Een beschrijvende tekst over deze organisatie  <br /><b>Schema:</b> <a href="https://schema.org/description">https://schema.org/description</a>
	 *
	 * @var string
	 *
	 * @ORM\Column(
	 *     type     = "text"
	 * )
	 * @Assert\NotNull
	 * @Assert\Length(
	 *      min = 25,
	 *      max = 2000,
	 *      minMessage = "De beschrijving moet minimaal {{ limit }} tekens lang zijn",
	 *      maxMessage = "De beschrijving kan maximaal {{ limit }} tekens lang zijn")
	 * @Groups({"read", "write"})
	 * @ApiProperty(
	 * 	  iri="https://schema.org/description",
	 *     attributes={
	 *         "openapi_context"={
	 *             "type"="string",
	 *             "maxLength"=2000,
	 *             "minLength"=25,
	 *             "example"="Deze prachtige organisatie staat voor de hoogste normen en waarden"
	 *         }
	 *     }
	 * )
	 **/
	public $beschrijving;
	
	
	/**
	 * Het emailadres van deze organisatie <br /><b>Schema:</b> <a href="https://schema.org/email">https://schema.org/email</a>
	 *
	 * @var string
	 *
     * @Gedmo\Versioned
	 * @ORM\Column(
	 *     type     = "string",
	 *     length   = 255, 
	 *     nullable = true,
	 * )
	 * @Assert\Length(
	 *      min = 8,
	 *      max = 255,
	 *      minMessage = "Het emailadres moet minimaal  {{ limit }} tekens lang zijn",
	 *      maxMessage = "Het emailadres mag maximaal {{ limit }} tekens lang zijn"
	 * )
	 * @Assert\Email(
     *     message = "Het email addres '{{ value }}' is geen geldig emailadres.",
     *     checkMX = true
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
	 **/
	public $emailadres;
	
	/**
	 * Het telefoonnummer van deze organisatie <br /><b>Schema:</b> <a href="https://schema.org/telephone">https://schema.org/telephone</a>
	 *
	 * @var string
	 *
     * @Gedmo\Versioned
	 * @ORM\Column(
	 *     type     = "string",
	 *     length   = 255,
	 *     nullable = true,
	 * )
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
	 **/
	public $telefoonnummer;
	
	/**
	 * De huwelijken verbonden aan deze organisatie
	 *
	 * @var \Doctrine\Common\Collections\Collection|\App\Entity\Huwelijk[]|null
	 *
	 * @ORM\OneToMany(
	 * 		targetEntity="\App\Entity\Huwelijk",
	 * 		mappedBy="bronOrganisatie", 
	 * 		fetch="EXTRA_LAZY"
	 * )
	 *
	 */
	public $huwelijken;
	
	/**
	 * De personen verbonden aan deze organisatie
	 *
	 * @var \Doctrine\Common\Collections\Collection|\App\Entity\Huwelijk[]|null
	 *
	 * @ORM\OneToMany(
	 * 		targetEntity="\App\Entity\Persoon",
	 * 		mappedBy="bronOrganisatie", 
	 * 		fetch="EXTRA_LAZY"
	 * )
	 *
	 */
	public $personen;
	
	/**
	 * De soorten huwelijken verbonden aan deze organisatie
	 *
	 * @var \Doctrine\Common\Collections\Collection|\App\Entity\Soort[]|null
	 *
	 * @ORM\OneToMany(
	 * 		targetEntity="\App\Entity\Soort",
	 * 		mappedBy="bronOrganisatie", 
	 * 		fetch="EXTRA_LAZY"
	 * )
	 *
	 */
	public $soorten;
	
	/**
	 * De bij deze organisatie horende gebruikers
	 *
	 * @var \Doctrine\Common\Collections\Collection|\App\Entity\User[]|null
	 *
	 * @ORM\OneToMany(
	 * 		targetEntity="\App\Entity\User",
	 * 		mappedBy="bronOrganisatie")
	 *
	 */
	public $users;
	
	/**
	 * De instellingen voor deze organisatie, kijk in de documentatie van deze api voor de mogelijke instellingen.
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
	 *             "title"="Instellingen",
	 *             "type"="array",
	 *             "example"="[]",
	 *             "description"="De instellingen voor deze organisatie, kijk in de documentatie van deze api voor de mogelijke instellingen"
	 *         }
	 *     }
	 * )
	 */	
	public $instellingen;
	
	/**
	 * @return string
	 */
	public function toString(){
		return $this->naam;
	}
	
	/**
	 * Vanuit rendering perspectief (voor bijvoorbeeld logging of berichten) is het belangrijk dat we een entiteit altijd naar string kunnen omzetten.
	 */
	public function __toString()
	{
		return $this->toString();
	}
}
