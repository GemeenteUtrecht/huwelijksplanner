<?php
// src/Entity/Apllicatie.php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\BooleanFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\DateFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use ActivityLogBundle\Entity\Interfaces\StringableInterface;

use App\Controller\UserController;

/**
 * Een Applicatie die is geidentificeerd en geautoriceerd om namens een organisatie wijzigingen uit te voeren
 *
 * @category   	Entity
 *
 * @author     	Ruben van der Linde <ruben@conduction.nl>
 * @license    	EUPL 1.2 https://opensource.org/licenses/EUPL-1.2 *
 * @version    	1.0
 *
 * @link   		http//:www.conduction.nl
 * @package		Commen Ground
 *
 *  @ApiResource(
 *  collectionOperations={
 *  	"get"={
 *  		"normalizationContext"={"groups"={"applicatie:lezen"}},
 *  		"denormalizationContext"={"groups"={"applicatie:schrijven"}},
 *      	"path"="/applicaties",
 *  		"openapi_context" = {
 * 				"summary" = "Verzameling",
 *         		"description" = "Haal een verzameling van Applicaties op, het is mogelijk om deze resultaten te filteren aan de hand van query parameters. <br><br>Lees meer over het filteren van resulaten onder [filteren](/#section/Filteren)."            
 *  		}
 *  	},
 *     "register"={
 *         "method"="POST",
 *         "path"="/registreer",
 *         "controller"= UserController::class,
 *     	   "normalization_context"={"groups"={"applicatie:lezen"}},
 *     	   "denormalization_context"={"groups"={"applicatie:maken"}},
 *
 *         "openapi_context" = {
 *         		"summary" = "Registreren",
 *         		"description" = "Registreer een nieuwe Applicatie voor dit component",
 *          	"consumes" = {
 *              	"application/json",
 *               	"text/html",
 *            	},
 *             	"produces" = {
 *         			"application/json"
 *            	},
 *             	"responses" = {
 *         			"201" = {
 *         				"description" = "Applicatie aangemaakt"
 *         			},
 *         			"400" = {
 *         				"description" = "Ongeldige aanvraag"
 *         			}
 *            	}
 *         }
 *     },
 *     "login"={
 *         "method"="POST",
 *         "path"="/login_check",
 *         "controller"= UserController::class,
 *     	   "normalization_context"={"groups"={"applicatie:lezen"}},
 *     	   "denormalization_context"={"groups"={"applicatie:inloggen"}}, 
 *         "openapi_context" = {
 *         		"summary" = "Token halen",
 *         		"description" = "Inloggen als Applicatie en JWT Token ophalen",
 *          	"consumes" = {
 *              	"application/json",
 *               	"text/html",
 *            	},
 *             	"produces" = {
 *         			"application/json"
 *            	},
 *             	"responses" = {
 *         			"200" = {
 *         				"description" = "Applicatie succesvol ingeloged"
 *         			},
 *         			"401" = {
 *         				"description" = "Applicatie niet ingeloged"
 *         			}
 *            	}
 *         }
 *     },
 *  },
 * 	itemOperations={
 *     "get"={
 *  		"normalizationContext"={"groups"={"applicatie:lezen"}},
 *  		"denormalizationContext"={"groups"={"applicatie:schrijven"}},
 *      	"path"="/applicatie/{id}",
 *  		"openapi_context" = {
 * 				"summary" = "Haal op",
 *         		"description" = "Haalt een Applicatie op"           
 *  		}
 *  	},
 *     "put"={
 *  		"normalizationContext"={"groups"={"applicatie:lezen"}},
 *  		"denormalizationContext"={"groups"={"applicatie:schrijven"}},
 *      	"path"="/applicatie/{id}",
 *  		"openapi_context" = {
 * 				"summary" = "Werk bij",
 *         		"description" = "Werk een Applicatie bij",
 *             	"responses" = {
 *         			"202" = {
 *         				"description" = "applicatie bijgewerkt"
 *         			},	
 *         			"400" = {
 *         				"description" = "Ongeldige aanvraag"
 *         			},
 *         			"404" = {
 *         				"description" = "Applicatie niet gevonden"
 *         			}
 *            	}            
 *  		}
 *  	},
 *     "log"={
 *         	"method"="GET",
 *         	"path"="/applicatie/{id}/log",
 *          "controller"= UserController::class,
 *     		"normalization_context"={"groups"={"applicatie:lezen"}},
 *     		"denormalization_context"={"groups"={"applicatie:schrijven"}},
 *         	"openapi_context" = {
 *         		"summary" = "Logboek",
 *         		"description" = "Bekijk de wijzigingen op dit Applicatie object",
 *          	"consumes" = {
 *              	"application/json",
 *               	"text/html",
 *            	}
 *         }
 *     }
 *  }
 * )
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Entity
 * @Gedmo\Loggable(logEntryClass="ActivityLogBundle\Entity\LogEntry")
 * @ORM\HasLifecycleCallbacks
 * @UniqueEntity(
 *     fields={"naam", "organisatie"},
 *     message="De naam van een Applicatie dient uniek te zijn voor een organisatie"
 * )
 */
class Applicatie implements UserInterface, StringableInterface
{
	/**
	 * @ORM\Id
	 * @ORM\Column(type="integer")
	 * @ORM\GeneratedValue(strategy="AUTO")
     * @ApiFilter(SearchFilter::class, strategy="exact")
     * @ApiFilter(OrderFilter::class)
	 * @Groups({"applicatie:lezen"})
	 */
	public $id;
	
	/**
	 * @Gedmo\Versioned
	 * @Groups({"applicatie:schrijven","applicatie:lezen","applicatie:maken","applicatie:inloggen"})
	 *
	 * @ORM\Column(
	 *     type     = "string",
	 *     length   = 255,
	 *     unique=true
	 * )
	 * @Assert\Email(
	 *     message = "Het email addres '{{ value }}' is geen geldig email addres.",
	 *     checkMX = true
	 * )
	 * @Assert\Length(
	 *      min = 8,
	 *      max = 255,
	 *      minMessage = "De naam moet tenminste {{ limit }} tekens bevatten",
	 *      maxMessage = "De naam kan maximaal {{ limit }} tekens bevatten"
	 * )
	 * @Groups({"applicatie:write","user"})
     * @ApiFilter(SearchFilter::class, strategy="partial")
     * @ApiFilter(OrderFilter::class)
	 * @ApiProperty(
	 * 	   iri="http://schema.org/name",
	 *     attributes={
	 *         "openapi_context"={
	 *             "type"="naam",
	 *             "maxLength"=255,
	 *             "minLength"=8,
	 *             "example"="mijn-applicatie"
	 *         }
	 *     }
	 * )
	 */
	public $naam;
		
	/**
	 * Een door de organisatie opgegeven sleutel waarmee deze Applicatie zich identificeerd bij het ophalen van en JWT token.
	 * 
	 * @Groups({"applicatie:schrijven","applicatie:maken","applicatie:inloggen"})
	 * @ORM\Column(type="string", length=500)
	 * @Assert\Length(
	 *      min = 5,
	 *      max = 16,
	 *      minMessage = "De leutel moet minimaal {{ limit }} tekens lang zijn",
	 *      maxMessage = "De leutel mag maximaal {{ limit }} tekens land zijn"
	 * )
	 * @ApiProperty(
	 * 	   iri="https://schema.org/accessCode",
	 *     attributes={
	 *         "openapi_context"={
	 *             "type"="string",
	 *             "maxLength"=16,
	 *             "minLength"=5,
	 *             "example"="NietZoGeheim"
	 *         }
	 *     }
	 * )
	 */
	public $sleutel;
	
	/**
	 * De scopes (rechten) die deze Applicatie heeft. Zie [scopes](/#section/Scopes) voor meer informatie.
	 *
	 * @Groups({"applicatie:schrijven","applicatie:maken"})
	 * @ORM\Column(type="string", length=500)
	 * @ApiProperty(
	 *     attributes={
	 *         "openapi_context"={
	 *             "type"="array",
	 *             "example"="[]"
	 *         }
	 *     }
	 * )
	 */
	public $scopes;
	
	/**
	 * Het RSIN van de organisatie waartoe deze Applicatie behoord. Dit moet een geldig RSIN zijn van 9 nummers en voldoen aan https://nl.wikipedia.org/wiki/Burgerservicenummer#11-proef.
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
	 * @Groups({"applicatie:lezen", "applicatie:maken"})
     * @ApiFilter(SearchFilter::class, strategy="exact")
     * @ApiFilter(OrderFilter::class)
	 * @ApiProperty(
	 *     attributes={
	 *         "openapi_context"={
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
	public $organisatie;	
	
	/**
	 * Word gebruikt om aan te geven of deze applicatie actief is (en mag inloggen) of dat deze slechts wordt gebruikt voor archief doeleinden
	 * 
	 * @Groups({"applicatie:lezen","applicatie:schrijven"})
     * @ApiFilter(BooleanFilter::class)
     * @ApiFilter(OrderFilter::class)
	 * @ORM\Column(name="is_active", type="boolean")
	 */
	public $isActief;
	
	/**
	 * Het tijdstip waarop deze Applicatie is aangemaakt
	 *
	 * @var datetime Een "Y-m-d H:i:s" waarde bijvoorbeeld "2018-12-31 13:33:05" ofwel "Jaar-dag-maand uur:minuut:seconde"
	 * @Gedmo\Timestampable(on="create")
	 * @Assert\DateTime
     * @ApiFilter(DateFilter::class)
     * @ApiFilter(OrderFilter::class)
	 * @ORM\Column(
	 *     type     = "datetime"
	 * )
	 * @Groups({"applicatie:lezen"})
	 */
	public $registratiedatum;
	
	/**
	 * Het tijdstip waarop deze Applicatie voor het laatst is gewijzigd.
	 *
	 * @var datetime Een "Y-m-d H:i:s" waarde bijvoorbeeld "2018-12-31 13:33:05" ofwel "Jaar-dag-maand uur:minuut:seconde"
	 * @Gedmo\Timestampable(on="update")
	 * @Assert\DateTime
     * @ApiFilter(DateFilter::class)
     * @ApiFilter(OrderFilter::class)
	 * @ORM\Column(
	 *     type     = "datetime",
	 *     nullable	= true
	 * )
	 * @Groups({"applicatie:lezen"})
	 */
	public $wijzigingsdatum;
	
	/**
	 * De contactpersoon voor deze Applicatie, die bijvoorbeeld word verwittigd bij misbruik.
	 *
	 * @ORM\Column(
	 *     type     = "string",
	 *     nullable = true
	 * )
	 * @Groups({"applicatie:lezen", "applicatie:schrijven","applicatie:maken"})
     * @ApiFilter(SearchFilter::class, strategy="exact")
     * @ApiFilter(OrderFilter::class)
	 * @ApiProperty(
	 *     attributes={
	 *         "openapi_context"={
	 *             "title"="Ambtenaar",
	 *             "type"="url",
	 *             "example"="https://ref.tst.vng.cloud/zrc/api/v1/zaken/24524f1c-1c14-4801-9535-22007b8d1b65",
	 *             "required"="true",
	 *             "maxLength"=255,
	 *             "format"="uri",
	 *             "description"="URL-referentie naar de Ambtenaar verantwoordelijk voor deze Applicatie"
	 *         }
	 *     }
	 * )
	 * @Gedmo\Versioned
	 */
	public $contactPersoon;
	
	
	/**
	 * API Specifieke parameters
	 *
	 * De onderstaande parameters worden alleen gebruikt bij api specifieke calls en hebben geen context tot het overige datamodel
	 */
	
	/**
	 * Username wordt door symfony gebruikt voor de gebruikersnaam maar in de context commonground component api gebruken we hem niet en onderdruken we hem door hem aan geen groupen toe te wijzen
	 *
	 * @Groups({"none"})
	 */
	private $username;
		
	/**
	 * Een JWT Token waarmee de Applicatie zich kan identificeren op de API
	 * 
	 * @Groups({"login_check"})
	 * @ApiProperty(
	 * 	   iri="https://schema.org/accessCode",
	 *     attributes={
	 *         "openapi_context"={
	 *             "description"="The security token, that needs to be set on the Authorization header, prefixed with with Bearer and a space (e.g.Authorization: Bearer [TOKEN]) in order to identify a request as being made by a specific user",
	 *             "type"="string",
	 *             "maxLength"=800,
	 *             "minLength"=850,
	 *             "example"="NotSoSecret"
	 *         }
	 *     }
	 * )
	 */
	private $token;
	
	/**
	 * Een refresh token die kan worden gebruikt om de geldigheid van een JWT token de verlengen
	 * 
	 * @Groups({"login_check"})
	 * @ApiProperty(
	 * 	   iri="https://schema.org/accessCode",
	 *     attributes={
	 *         "openapi_context"={
	 *             "description"="The refresh token, can be used on the token refresh action",
	 *             "type"="string",
	 *             "maxLength"=100,
	 *             "minLength"=130,
	 *             "example"="NotSoSecret"
	 *         }
	 *     }
	 * )
	 */
	private $refresh_token;		
	
	/**
	 * Het versie nummer van een eerdere versie die moet worden hersted (e.g. de huidige versie overschrijft)
	 *
	 * @Groups({"herstel"})
	 * @ApiProperty(
	 * 	   iri="https://schema.org/identifier",
	 *     attributes={
	 *         "openapi_context"={
	 *            "type"="integer",
	 *             "maxLength"=1,
	 *             "minLength"=255,
	 *             "example"="1"
	 *         }
	 *     }
	 * )
	 */
	private $versie;
	
	/**
	 * @return string
	 */
	public function toString(){
		return $this->name;
	}
	
	/**
	 * Vanuit rendering perspectief (voor bijvoorbeeld logging of berichten) is het belangrijk dat we een entiteit altijd naar string kunnen omzetten.
	 */
	public function __toString()
	{
		return $this->toString();
	}
	
	// We need a full name atribute for the loging bundle
	public function getFullname(): ?string
	{
		return $this->name;
	}
	
	public function isUser(?UserInterface $applicatie = null): bool
	{
		return $applicatie instanceof self && $applicatie->id === $this->id;
	}
	
	public function __construct($username)
	{
		$this->isActive = true;
		$this->username = $username;
	}
	public function getUsername()
	{
		return $this->name;
	}	
	
	public function getPassword()
	{
		return $this->sleutel;
	}
	
	public function getSalt()
	{
		return null;
	}
	
	public function getRoles()
	{
		return array('ROLE_USER');
	}
	public function eraseCredentials()
	{
	}
}
