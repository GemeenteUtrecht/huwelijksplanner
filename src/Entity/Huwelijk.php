<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiSubresource;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\MaxDepth;
use Symfony\Component\Serializer\Annotation\SerializedName;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use ActivityLogBundle\Entity\Interfaces\StringableInterface;


use App\Controller\HuwelijkController;

/**
 * Huwelijk
 * 
 * Een huwelijk of partnerschap tussen twee personen
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
 * @ApiResource( 
 *  collectionOperations={
 *  	"get"={
 *  		"normalizationContext"={"groups"={"read"}},
 *  		"denormalizationContext"={"groups"={"write"}},
 *      	"path"="/huwelijken",
 *  		"openapi_context" = {
 *  		}
 *  	},
 *  	"post"={
 *  		"normalizationContext"={"groups"={"read"}},
 *  		"denormalizationContext"={"groups"={"write"}},
 *      	"path"="/huwelijken",
 *  		"openapi_context" = {
 *  		}
 *  	},   
 *     "on_bsn"={
 *     		"defaults"={
 *     			"_api_persist" = false,	
 *     	   },
 *         "method"="POST",
 *         "path"="/huwelijk_bsn",
 *         "controller"= HuwelijkController::class,
 *     	   "normalization_context"={"groups"={"read"}},
 *     	   "denormalization_context"={"groups"={"BSN"}},
 *     	   
 *         "openapi_context" = {
 *         		"summary" = "Haal huwelijk op BSN",
 *         		"description" = "Haal een huwelijk op aan de hand van een BSN nummer, als er voor dit BSN nog geen huwelijk is, wordt er een huwelijk aangemaakt",
 *          	"consumes" = {
 *              	"application/json",
 *               	"text/html",
 *            	},
 *             	"produces" = {
 *         			"application/json"
 *            	},
 *             	"responses" = {
 *         			"200" = {
 *         				"description" = "Huwelijk gevonden op BSN"
 *         			},	
 *         			"201" = {
 *         				"description" = "Huwelijk aangemaakt voor BSN"
 *         			},	
 *         			"400" = {
 *         				"description" = "Ongeldige aanvraag"
 *         			}
 *            	}
 *         }
 *     },
 *  },
 * 	itemOperations={
 *     "get"={
 *  		"normalizationContext"={"groups"={"read"}},
 *  		"denormalizationContext"={"groups"={"write"}},
 *      	"path"="/huwelijk/{id}",
 *  		"openapi_context" = {
 *  		}
 *  	},
 *     "put"={
 *  		"normalizationContext"={"groups"={"read"}},
 *  		"denormalizationContext"={"groups"={"write"}},
 *      	"path"="/huwelijk/{id}",
 *  		"openapi_context" = {
 *  		}
 *  	},
 *     "delete"={
 *  		"normalizationContext"={},
 *  		"denormalizationContext"={},
 *      	"path"="/huwelijk/{id}",
 *  		"openapi_context" = {
 *  		}
 *  	},
 *     "add_partner"={
 *         "method"="POST",
 *         "path"="/huwelijk/{id}/addPartner",
 *         "controller"= HuwelijkController::class,
 *     	   "normalization_context"={"groups"={"invite"}},
 *     	   "denormalization_context"={"groups"={"invite"}},
 *         "openapi_context" = {
 *         		"summary" = "Voeg een partner toe",
 *         		"description" = "Nodig een partner uit om dit huwelijk te bevestigen en deel te nemen, er zijn 2 partners per huwelijk",
 *          	"consumes" = {
 *              	"application/json",
 *               	"text/html",
 *            	},
 *             	"produces" = {
 *         			"application/json"
 *            	},
 *             	"responses" = {
 *         			"201" = {
 *         				"description" = "Partner toegevoegd aan huwelijk en om bevestiging gevraagd"
 *         			},	
 *         			"400" = {
 *         				"description" = "Ongeldige aanvraag"
 *         			},
 *         			"404" = {
 *         				"description" = "Huwelijk niet gevonden"
 *         			}
 *            	}
 *         }
 *     },
 *     "pay"={
 *         "method"="GET",
 *         "path"="/huwelijk/{id}/pay",
 *         "controller"= HuwelijkController::class,
 *     	   "normalization_context"={"groups"={"payment"}},
 *     	   "denormalization_context"={"groups"={"payment"}},
 *         "openapi_context" = {
 *         		"summary" = "Haal betaallink op",
 *         		"description" = "Geeft een betaallink terug voor huwelijk, het aanvragen van een betaallink heeft gevolgen (aanmaak factuur etc), waardoor het huwelijk hierna niet meer kan worden aangpast",
 *          	"consumes" = {
 *              	"application/json",
 *               	"text/html",
 *            	},
 *             	"produces" = {
 *         			"application/json"
 *            	},
 *             	"responses" = {
 *         			"200" = {
 *         				"description" = "Betaallink gevonden"
 *         			},	
 *         			"201" = {
 *         				"description" = "Betaallink aangemaakt"
 *         			},	
 *         			"400" = {
 *         				"description" = "Ongeldige aanvraag"
 *         			},
 *         			"404" = {
 *         				"description" = "Huwelijk niet gevonden"
 *         			}
 *            	}
 *         }
 *     },
 *     "add_witness"={
 *         "method"="POST",
 *         "path"="/huwelijk/{id}/addWitness",
 *         "controller"= HuwelijkController::class,
 *     	   "normalization_context"={"groups"={"inviteGetuige"}},
 *     	   "denormalization_context"={"groups"={"inviteGetuige"}},
 *         "openapi_context" = {
 *         		"summary" = "Voeg een getuige toe",
 *         		"description" = "Voeg een getuige toe die voor een van de partners getuigt, er moeten/mogen per partner minimaal 1 en maximaal 2 getuigen zijn",
 *          	"consumes" = {
 *              	"application/json",
 *               	"text/html",
 *            	},
 *             	"produces" = {
 *         			"application/json"
 *            	},
 *             	"responses" = {
 *         			"201" = {
 *         				"description" = "Getuige toegevoegd en om bevestiging gevraagd"
 *         			},	
 *         			"400" = {
 *         				"description" = "Ongeldige aanvraag"
 *         			},
 *         			"404" = {
 *         				"description" = "Huwelijk niet gevonden"
 *         			}
 *            	}
 *         }
 *     },
 *     "remove_witness"={
 *         "method"="POST",
 *         "path"="/huwelijk/{id}/removeWitness",
 *         "controller"= HuwelijkController::class,
 *     	   "normalization_context"={"groups"={"remove"}},
 *     	   "denormalization_context"={"groups"={"remove"}},
 *         "openapi_context" = {
 *         		"summary" = "Verwijder een getuige",
 *         		"description" = "Verwijder een getuige van dit huwelijk",
 *          	"consumes" = {
 *              	"application/json",
 *               	"text/html",
 *            	},
 *             	"produces" = {
 *         			"application/json"
 *            	},
 *             	"responses" = {
 *         			"202" = {
 *         				"description" = "Getuige verwijderd van dit huwelijk"
 *         			},	
 *         			"400" = {
 *         				"description" = "Ongeldige aanvraag"
 *         			},
 *         			"404" = {
 *         				"description" = "Getuige of Huwelijk niet gevonden"
 *         			}
 *            	}
 *         }
 *     },
 *     "set_location"={
 *         "method"="POST",
 *         "path"="/huwelijk/{id}/setLocation",
 *         "controller"= HuwelijkController::class,
 *     	   "normalization_context"={"groups"={"setLocation"}},
 *     	   "denormalization_context"={"groups"={"setLocation"}},
 *         "openapi_context" = {
 *         		"summary" = "Stel een locatie in voor dit huwelijk",
 *         		"description" = "Geef de locatie op waar dit huwelijk gaat plaatsvinden",
 *          	"consumes" = {
 *              	"application/json",
 *               	"text/html",
 *            	},
 *             	"produces" = {
 *         			"application/json"
 *            	},
 *             	"responses" = {
 *         			"200" = {
 *         				"description" = "locatie opgegeven en om bevestiging gevraagd"
 *         			},	
 *         			"400" = {
 *         				"description" = "Ongeldige aanvraag"
 *         			},
 *         			"404" = {
 *         				"description" = "Huwelijk of locatie niet gevonden"
 *         			}
 *            	}
 *         }
 *     },
 *     "set_product"={
 *         "method"="POST",
 *         "path"="/huwelijk/{id}/setProduct",
 *         "controller"= HuwelijkController::class,
 *     	   "normalization_context"={"groups"={"setProduct"}},
 *     	   "denormalization_context"={"groups"={"setProduct"}},
 *         "openapi_context" = {
 *         		"summary" = "Kies het type van dit huwelijk",
 *         		"description" = "Kies het primaire type van dit huwelijk (bijvoorbeeld gratis). Het primaire product bepaald welke overige producten, diensten, ambtenaren en locaties kunnen worden gekozen",
 *          	"consumes" = {
 *              	"application/json",
 *               	"text/html",
 *            	},
 *             	"produces" = {
 *         			"application/json"
 *            	},
 *             	"responses" = {
 *         			"200" = {
 *         				"description" = "Primair product ingesteld"
 *         			},	
 *         			"400" = {
 *         				"description" = "Ongeldige aanvraag"
 *         			},
 *         			"404" = {
 *         				"description" = "Huwelijk of type niet gevonden"
 *         			}
 *            	}
 *         }
 *     },
 *     "add_product"={
 *         "method"="POST",
 *         "path"="/huwelijk/{id}/addProduct",
 *         "controller"= HuwelijkController::class,
 *     	   "normalization_context"={"groups"={"productAdd"}},
 *     	   "denormalization_context"={"groups"={"productAdd"}},
 *         "openapi_context" = {
 *         		"summary" = "Voeg een product toe",
 *         		"description" = "Voeg een extra product (bijvoorbeeld trouwboekje) toe aan dit huwelijk",
 *          	"consumes" = {
 *              	"application/json",
 *               	"text/html",
 *            	},
 *             	"produces" = {
 *         			"application/json"
 *            	},
 *             	"responses" = {
 *         			"201" = {
 *         				"description" = "Product toegevoegd"
 *         			},	
 *         			"400" = {
 *         				"description" = "Ongeldige aanvraag"
 *         			},
 *         			"404" = {
 *         				"description" = "Huwelijk of product niet gevonden"
 *         			}
 *            	}
 *         }
 *     },
 *     "remove_product"={
 *         "method"="POST",
 *         "path"="/huwelijk/{id}/removeProduct",
 *         "controller"= HuwelijkController::class,
 *     	   "normalization_context"={"groups"={"remove"}},
 *     	   "denormalization_context"={"groups"={"remove"}},
 *         "openapi_context" = {
 *         		"summary" = "Verwijder product",
 *         		"description" = "Verwijder een eerder gekozen product",
 *          	"consumes" = {
 *              	"application/json",
 *               	"text/html",
 *            	},
 *             	"produces" = {
 *         			"application/json"
 *            	},
 *             	"responses" = {
 *         			"202" = {
 *         				"description" = "Product verwijderd"
 *         			},	
 *         			"400" = {
 *         				"description" = "Ongeldige aanvraag"
 *         			},
 *         			"404" = {
 *         				"description" = "Huwelijk of product niet gevonden"
 *         			}
 *            	}
 *         }
 *     },
 *     "add_document"={
 *         "method"="POST",
 *         "path"="/huwelijk/{id}/addDocument",
 *         "controller"= HuwelijkController::class,
 *     	   "normalization_context"={"groups"={"documentAdd"}},
 *     	   "denormalization_context"={"groups"={"documentAdd"}},
 *         "openapi_context" = {
 *         		"summary" = "Voeg een document toe",
 *         		"description" = "Voeg een document toe aan dit huwelijk, bijvoorbeeld een geboorteakte",
 *          	"consumes" = {
 *              	"application/json",
 *               	"text/html",
 *            	},
 *             	"produces" = {
 *         			"application/json"
 *            	},
 *             	"responses" = {
 *         			"201" = {
 *         				"description" = "Document toegevoegd"
 *         			},	
 *         			"400" = {
 *         				"description" = "Ongeldige aanvraag"
 *         			},
 *         			"404" = {
 *         				"description" = "Huwelijk of document niet gevonden"
 *         			}
 *            	}
 *         }
 *     },
 *     "remove_document"={
 *         "method"="POST",
 *         "path"="/huwelijk/{id}/removeDocument",
 *         "controller"= HuwelijkController::class,
 *     	   "normalization_context"={"groups"={"remove"}},
 *     	   "denormalization_context"={"groups"={"remove"}},
 *         "openapi_context" = {
 *         		"summary" = "Verwijder document",
 *         		"description" = "Verwijder een eerder toegevoegd document",
 *          	"consumes" = {
 *              	"application/json",
 *               	"text/html",
 *            	},
 *             	"produces" = {
 *         			"application/json"
 *            	},
 *             	"responses" = {
 *         			"202" = {
 *         				"description" = "Document verwijderd"
 *         			},	
 *         			"400" = {
 *         				"description" = "Ongeldige aanvraag"
 *         			},
 *         			"404" = {
 *         				"description" = "Huwelijk of document niet gevonden"
 *         			}
 *            	}    
 *         }
 *     },
 *     "request_official"={
 *         "method"="POST",
 *         "path"="/huwelijk/{id}/requestOfficial",
 *         "controller"= HuwelijkController::class,
 *     		"normalization_context"={"groups"={"requestOfficial"}},
 *     		"denormalization_context"={"groups"={"requestOfficial"}},
 *         "openapi_context" = {
 *         		"summary" = "Vraag een trouwambtenaar aan",
 *         		"description" = "Vraag een (specifieke) trouwambtenaar aan om dit huwelijk te voltrekken",
 *          	"consumes" = {
 *              	"application/json",
 *               	"text/html",
 *            	},
 *             	"produces" = {
 *         			"application/json"
 *            	},
 *             	"responses" = {
 *         			"201" = {
 *         				"description" = "Trouwambtenaar toegevoegd en om bevestiging gevraagd"
 *         			},	
 *         			"400" = {
 *         				"description" = "Ongeldige aanvraag"
 *         			},
 *         			"404" = {
 *         				"description" = "Huwelijk of trouwambtenaar niet gevonden"
 *         			}
 *            	}
 *         }
 *     },
 *     "request_special"={
 *         "method"="POST",
 *         "path"="/huwelijk/{id}/requestSpecial",
 *         "controller"= HuwelijkController::class,
 *     		"normalization_context"={"groups"={"invite"}},
 *     		"denormalization_context"={"groups"={"invite"}},
 *         "openapi_context" = {
 *         		"summary" = "Vraag een bijzonder trouwambtenaar aan",
 *         		"description" = "Vraag een niet geregistreerde trouwambtenaar aan om dit huwelijk te voltrekken",
 *          	"consumes" = {
 *              	"application/json",
 *               	"text/html",
 *            	},
 *             	"produces" = {
 *         			"application/json"
 *            	},
 *             	"responses" = {
 *         			"201" = {
 *         				"description" = "Trouwambtenaar toegevoegd en om bevestiging gevraagd"
 *         			},	
 *         			"400" = {
 *         				"description" = "Ongeldige aanvraag"
 *         			},
 *         			"404" = {
 *         				"description" = "Huwelijk niet gevonden"
 *         			}
 *            	}
 *         }
 *     },
 *     "remove_official"={
 *         "method"="POST",
 *         "path"="/huwelijk/{id}/removeOfficial",
 *         "controller"= HuwelijkController::class,
 *     		"normalization_context"={"groups"={"remove"}},
 *     		"denormalization_context"={"groups"={"remove"}},
 *         "openapi_context" = {
 *         		"summary" = "Verwijder de trouwambtenaar",
 *         		"description" = "Verwijder de eerder aangevraagde trouwambtenaar van dit huwelijk",
 *          	"consumes" = {
 *              	"application/json",
 *               	"text/html",
 *            	},
 *             	"produces" = {
 *         			"application/json"
 *            	},
 *             	"responses" = {
 *         			"202" = {
 *         				"description" = "Trouwambentenaar verwijderd uit dit huwelijk"
 *         			},	
 *         			"400" = {
 *         				"description" = "Ongeldige aanvraag"
 *         			},
 *         			"404" = {
 *         				"description" = "Huwelijk of trouwambtenaar niet gevonden"
 *         			}
 *            	}       
 *         }
 *     },
 *     "validate"={
 *         "method"="POST",
 *         "path"="/huwelijk/{id}/validate",
 *         "controller"= HuwelijkController::class,
 *     		"normalization_context"={"groups"={"validate"}},
 *     		"denormalization_context"={"groups"={"validate"}},
 *         "openapi_context" = {
 *         		"summary" = "Valideer huwelijksdossier",
 *         		"description" = "Controleer of het huwelijksdossier juist en volledig is",
 *          	"consumes" = {
 *              	"application/json",
 *               	"text/html",
 *            	},
 *             	"produces" = {
 *         			"application/json"
 *            	},
 *             	"responses" = {
 *         			"200" = {
 *         				"description" = "Dossier gevalideerd, eventuele afwijkingen in resultaat"
 *         			},	
 *         			"400" = {
 *         				"description" = "Ongeldige aanvraag"
 *         			},
 *         			"404" = {
 *         				"description" = "Huwelijk of dossier niet gevonden"
 *         			}
 *            	}
 *         }
 *     },
 *     "melding"={
 *         	"method"="POST",
 *         	"path"="/huwelijk/{id}/melding",
 *          "controller"= HuwelijkController::class,
 *     		"normalization_context"={"groups"={"melding"}},
 *     		"denormalization_context"={"groups"={"melding"}},
 *         	"openapi_context" = {
 *         		"summary" = "Melding huwelijk",
 *         		"description" = "Doe een melding voorgenomen huwelijk bij de betreffende gemeente voor dit huwelijk, er kan geen aanvullende informatie worden verstrekt",
 *          	"consumes" = {
 *              	"application/json",
 *               	"text/html",
 *            	},
 *             	"produces" = {
 *         			"application/json"
 *            	},
 *             	"responses" = {
 *         			"200" = {
 *         				"description" = "Melding ontvangen"
 *         			},	
 *         			"400" = {
 *         				"description" = "Ongeldige melding"
 *         			},
 *         			"404" = {
 *         				"description" = "Huwelijk of melding niet gevonden"
 *         			}
 *            	}       
 *         }
 *     },
 *     "aanvraag"={
 *         	"method"="POST",
 *         	"path"="/huwelijk/{id}/aanvraag",
 *          "controller"= HuwelijkController::class,
 *     		"normalization_context"={"groups"={"aanvraag"}},
 *     		"denormalization_context"={"groups"={"aanvraag"}},
 *         	"openapi_context" = {
 *         		"summary" = "Aanvraag huwelijk",
 *         		"description" = "Doe een aanvraag huwelijk bij de betreffende gemeente voor dit huwelijk, er kan geen aanvullende informatie worden verstrekt",
 *          	"consumes" = {
 *              	"application/json",
 *               	"text/html",
 *            	},
 *             	"produces" = {
 *         			"application/json"
 *            	},
 *             	"responses" = {
 *         			"200" = {
 *         				"description" = "Aanvraag in behandeling genomen"
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
 *     "log"={
 *         	"method"="GET",
 *         	"path"="/huwelijk/{id}/log",
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
 *         	"path"="/huwelijk/{id}/revert/{version}",
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

class Huwelijk implements StringableInterface
{
	/**
	 * Het identificatie nummer van dit huwelijk <br /><b>Schema:</b> <a href="https://schema.org/identifier">https://schema.org/identifier</a><br /><br /><b>Note</b> This is for devolopment purposes, the INT ID wil be replaced by BLOB UUID on production
	 *
	 * @var int|null
	 *
	 * @ORM\Id
	 * @ORM\GeneratedValue()
	 * @ORM\Column(type = "integer",options={"unsigned": true})
	 * @ApiProperty(iri="https://schema.org/identifier", identifier=true)
	 * @Groups({"read"})
	 */
	private $id;
		
	/**
	 * De unieke identificatie van dit object binnen de organisatie die dit object heeft aangemaakt. <br /><b>Schema:</b> <a href="https://schema.org/identifier">https://schema.org/identifier</a>
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
	 *             "description"="De unieke identificatie van dit object de organisatie die dit object heeft aangemaakt.",
	 *             "maxLength"=40
	 *         }
	 *     }
	 * )
	 * @Gedmo\Versioned
	 */
	public $identificatie;
	
	/**
	 * Het Huwelijk waartoe deze partner behoort
	 *
	 * @var \App\Entity\Organisatie
	 * @ORM\ManyToOne(targetEntity="\App\Entity\Organisatie", cascade={"persist", "remove"}, inversedBy="huwelijken")
	 * @ORM\JoinColumn(referencedColumnName="id")
	 *
	 */
	public $bronOrganisatie;
	
	/**
	 * URL-referentie naar de melding "ZAAK" van dit huwelijk <br /><b>Schema:</b> <a href="https://schema.org/URL">https://schema.org/URL</a>
	 *
	 * @var string
	 * @ORM\Column(
	 *     type     = "string",
	 *     nullable = true
	 * )
	 * @ApiProperty(
	 *     attributes={
	 *         "openapi_context"={
	 *             "title"="ZAAK",
	 *             "type"="string",
	 *             "example"="https://ref.tst.vng.cloud/zrc/api/v1/zaken/24524f1c-1c14-4801-9535-22007b8d1b65",
	 *             "required"="true",
	 *             "maxLength"=200,
	 *             "format"="uri",
	 *             "description"="URL-referentie naar de melding ZAAK van dit huwelijk"
	 *         }
	 *     }
	 * )
	 * @Groups({"read"})
	 * @Gedmo\Versioned
	 */
	public $melding;
	
	/**
	 * URL-referentie naar de aanvraag "ZAAK" van dit huwelijk <br /><b>Schema:</b> <a href="https://schema.org/URL">https://schema.org/URL</a>
	 *
	 * @var string
	 * @ORM\Column(
	 *     type     = "string",
	 *     nullable = true
	 * )
	 * @Gedmo\Versioned
	 * @ApiProperty(
	 *     attributes={
	 *         "openapi_context"={
	 *             "title"="ZAAK",
	 *             "type"="string",
	 *             "example"="https://ref.tst.vng.cloud/zrc/api/v1/zaken/24524f1c-1c14-4801-9535-22007b8d1b65",
	 *             "required"="true",
	 *             "maxLength"=200,
	 *             "format"="uri",
	 *             "description"="URL-referentie naar de aanvraag ZAAK van dit huwelijk"
	 *         }
	 *     }
	 * )
	 * @Groups({"read"})
	 */
	public $aanvraag;
	
	/**
	 * Het type van dit huwelijk <br /><b>Schema:</b> <a href="https://schema.org/additionalType">https://schema.org/additionalType</a>
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
	 *             "enum"={"huwelijk", "partnerschap"},
	 *             "example"="huwelijk",
	 *             "default"="huwelijk"	              
	 *         }
	 *     }
	 * )
	 * @Groups({"read", "write"})
	 */
	public $type = "huwelijk";
	
	/**
	 * De dag waarop de huwelijksvoltrekking zal plaatsvinden
	 * 
	 * @var string Een "Y-m-d H:i:s" waarde bijv. "2018-12-31 13:33:05" ofwel "Jaar-dag-maan uur:minut:seconde"
	 * @Assert\Date
	 * @ORM\Column(
	 *     type     = "date",
	 *     nullable = true
	 * )
	 * @Groups({"read", "write"})
	 */
	public $datum;
	
	/**
	 * Het tijdstip waarop de huwelijksvoltrekking zal plaatsvinden
	 *
	 * @var string Een "Y-m-d H:i:s" waarde bijv. "2018-12-31 13:33:05" ofwel "Jaar-dag-maan uur:minut:seconde"
	 * @ORM\Column(nullable = true)
     * @Assert\NotBlank()
     * @Assert\Regex("/((?:[01]\d)|(?:2[0-3]))/")
	 * @Groups({"read", "write"})
	 */
	public $tijd;
	
	/**
	 * Datums die mogelijk zijn, binnen de door de gebruiker opgeven criteria
	 *
	 * @var array|null Een "Y-m-d H:i:s" waarde bijv. "2018-12-31 13:33:05" ofwel "Jaar-dag-maan uur:minut:seconde"
	 * @Groups({"read"})
	 */
	public $mogelijkeDatums;
	
	/**
	 * Tijdstippen die mogelijk zijn, binnen de door de gebruiker opgeven criteria
	 *
	 * @var array|null  Een "Y-m-d H:i:s" waarde bijv. "2018-12-31 13:33:05" ofwel "Jaar-dag-maan uur:minut:seconde"
	 * @Groups({"read"})
	 */
	public $mogelijkeTijden;
	
	/**
	 * De gekozen locatie voor dit huwelijk
	 *
	 * @var \Doctrine\Common\Collections\Collection|\App\Entity\Huwelijk\HuwelijkLocatie[]|null
	 *
     * @MaxDepth(3)
	 * @ORM\OneToMany(
	 * 		targetEntity="\App\Entity\Huwelijk\HuwelijkLocatie",
	 *		mappedBy="huwelijk")
	 * @Groups({"read"})
	 *
	 */
	public $locaties;
	
	
	/**
	 * De partners die in dit huwelijk treden 
	 *
	 * @var \Doctrine\Common\Collections\Collection|\App\Entity\Huwelijk\HuwelijkPartner[]|null
	 *
     * @MaxDepth(3)
	 * @ORM\OneToMany(
	 * 		targetEntity="\App\Entity\Huwelijk\HuwelijkPartner", 
	 * 		mappedBy="huwelijk")
	 * @Groups({"read"})
	 *
	 */
	public $partners;
	
	/**
	 * De personen die voor dit huwelijk gaan getuigen
	 *
	 * @var \Doctrine\Common\Collections\Collection|\App\Entity\Huwelijk\HuwelijkGetuigen[]|null
	 *
     * @MaxDepth(3)
	 * @ORM\OneToMany(
	 * 		targetEntity="\App\Entity\Huwelijk\HuwelijkGetuige", 
	 * 		mappedBy="huwelijk")
	 * @Groups({"read"})
	 *
	 */
	public $getuigen;
	
	/**
	 * De trouwambtenaren die betrokken zijn bij deze huwelijksvoltrekking
	 *
	 * @var \Doctrine\Common\Collections\Collection|\App\Entity\Huwelijk\HuwelijkAmbtenaar[]
	 *
     * @MaxDepth(3)
	 * @ORM\OneToMany(targetEntity="\App\Entity\Huwelijk\HuwelijkAmbtenaar", mappedBy="huwelijk")
	 * @Groups({"read"})
	 *
	 */
	public $ambtenaren;
	
	/**
	 * De bij dit huwelijk behorende/benodigde documenten
	 *
	 * @var \Doctrine\Common\Collections\Collection|\App\Entity\Huwelijk\HuwelijkDocument[]
	 *
     * @MaxDepth(1)
	 * @ORM\OneToMany(targetEntity="\App\Entity\Huwelijk\HuwelijkDocument", mappedBy="huwelijk")
	 * @Groups({"read"})
	 *
	 */
	public $documenten;
	
	/**
	 * Aan dit huwelijk verbonden issues,
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
	public $issues;
		
	/**
	 * Het soort huwelijk wat is gekozen, bijvoorbeeld gratis
	 *
	 * @todo eigenlijk setten met een primary flag op het onderliggende object en dan een collection filter
	 *
	 * @var \App\Entity\Soort
	 * @ORM\ManyToOne(targetEntity="\App\Entity\Soort", inversedBy="huwelijken")
	 * @Groups({"read"})
	 *
	 */
	public $soort;
	
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
	public $producten;
	
	/**
	 * Het tijdstip waarop deze entiteit is aangemaakt.
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
	 * Het tijdstip waarop deze entiteit voor het laatst is gewijzigd.
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
	 * De taal waarin dit object is opgesteld <br /><b>Schema:</b> <a href="https://schema.org/Language">https://schema.org/Language</a>
	 *
	 * @var string
	 * @Assert\Language
	 * @ORM\Column(
	 *     type     = "string"
	 * )
	 * @ApiProperty(
	 *     attributes={
	 *         "openapi_context"={
	 *             "type"="string",
	 *             "example"="nl",
	 *             "maxLength"=5,
	 *             "format"="Language",
	 *             "readOnly"=false
	 *         }
	 *     }
	 * )
	 * @Groups({"read","write"})
	 */
	public $taal = "nl";	
	
	/**
	 * @var integer The non decimal value for the price of this product (excluding tax)
	 *
	 * @Groups({"read"})
	 */
	public $exclAmount = 0;
	
	/**
	 * @var integer The the percentage of the on this product, in percentage so 1% = 1, and not 0,01
	 *
	 * @Groups({"read"})
	 */
	public $taxPercentage = 0;
	
	/**
	 * @var integer The non decimal value for the tax of this huwelijk
	 *
	 * @Groups({"read"})
	 */
	public $taxAmount = 0;
	
	/**
	 * @var integer The non decimal value for the price of this v (including tax)
	 *
	 * @Groups({"read"})
	 */
	public $inclAmount = 0;
	
	/**
	 * @var string The base currency of this huwelijk
	 *
	 * @Groups({"read"})
	 */
	public $currency = "EUR";	
	
	/*
	 * 
	 *  Hieronder staan eigenlijk veredelde DTO's waar nog een andere oplossing voor moet worden gevonden.
	 * 
	 * 
	 */
	
	/**
	 * Het emailadres van de persoon die je probeert te uit te nodigen <br /><b>Schema:</b> <a href="https://schema.org/email">https://schema.org/email</a>
	 *
	 * @var string
	 * @ApiProperty(
	 *     attributes={
	 *         "openapi_context"={
	 *             "type"="string",
	 *             "example"="john@do.com",
	 *             "required"="true",
	 *             "maxLength"=250,
	 *             "format"="email",
	 *             "readOnly"=false
	 *         }
	 *     }
	 * )
	 * @Groups({"invite","inviteGetuige"})
	 */
	public $emailadres;
	
	/**
	 * Het telefoonnummer van de persoon die je probeert te uit te nodigen, als je deze meegeeft zal er tevens een sms worden verzonden <br /><b>Schema:</b> <a href="https://schema.org/email">https://schema.org/email</a>
	 *
	 * @var string
	 * @ApiProperty(
	 *     attributes={
	 *         "openapi_context"={
	 *             "type"="string",
	 *             "example"="06-12345678",
	 *             "maxLength"=15,
	 *             "format"="phone",
	 *             "readOnly"=false
	 *         }
	 *     }
	 * )
	 * @Groups({"invite","inviteGetuige"})
	 */
	public $telefoonnummer;
	
	/**
	 * Het emailadres van de persoon die je wil uitnodigen. <br /><b>Schema:</b> <a href="https://schema.org/givenName">https://schema.org/givenName</a>
	 *
	 * @var string
	 * @ApiProperty(
	 *     attributes={
	 *         "openapi_context"={
	 *             "type"="string",
	 *             "example"="John",
	 *             "required"="true",
	 *             "maxLength"=250,
	 *             "readOnly"=false
	 *         }
	 *     }
	 * )
	 * @Groups({"invite","inviteGetuige"})
	 */
	public $voornamen;
	
	/**
	 * De familienaam van de persoon die je wil uitnodigen. <br /><b>Schema:</b> <a href="https://schema.org/familyName">https://schema.org/familyName</a>
	 *
	 * @var string
	 * @ApiProperty(
	 *     attributes={
	 *         "openapi_context"={
	 *             "type"="string",
	 *             "example"="Do",
	 *             "required"="true",
	 *             "maxLength"=250,
	 *             "readOnly"=false
	 *         }
	 *     }
	 * )
	 * @Groups({"invite","inviteGetuige"})
	 */
	public $geslachtsnaam;	
	
	/**
	 * Het id van de partner dat gekoppeld moet worden aan bijvoorbeeld zijn/haar getuige of document. <br /><b>Schema:</b> <a href="https://schema.org/identifier">https://schema.org/identifier</a>
	 *
	 * @var integer
	 * @Groups({"inviteGetuige","documentAdd"})
	 */
	public $partner;
	
	/**
	 * Het id van het object dat moet worden verwijderd <br /><b>Schema:</b> <a href="https://schema.org/identifier">https://schema.org/identifier</a>
	 *
	 * @var string
	 * @Groups({"remove"})
	 */
	public $removeId;
	
	/**
	 * Een link naar het te uploaden document of een base64 representatie van dat document  <br /><b>Schema:</b> <a href="https://schema.org/identifier">https://schema.org/identifier</a> or <b>Schema:</b> <a href="https://schema.org/URL">https://schema.org/URL</a> 
	 *
	 * @var string
	 * @ApiProperty(
	 * 	attributes={
	 *		"openapi_context"={
	 *			"type"="url or base64",
     *          "required"="true",
	 *          "readOnly"=false
	 *		}
	 *	}
	 * )
	 * @Groups({"documentAdd"})
	 */
	public $document;
	
	/**
	 * Een beschrijving van het type document dat je probeert te uploaden <br /><b>Schema:</b> <a href="https://schema.org/additionalType">https://schema.org/additionalType</a>
	 *
	 * @var string
	 * @SerializedName("type")
	 * @ApiProperty(
	 *     attributes={
	 *         "openapi_context"={
	 *             "type"="string",
	 *             "enum"={"geboorte akte", "paspoort"},
	 *             "example"="geboorte akte",
	 *             "readOnly"=false
	 *         }
	 *     }
	 * )
	 * @Groups({"documentAdd"})
	 */
	public $documentType;
	
	/**
	 * Het ID van de locatie waar het huwelijk plaatsvindt. <br /><b>Schema:</b> <a href="https://schema.org/identifier">https://schema.org/identifier</a>
	 *
	 * @var integer
	 * @ApiProperty(
	 * 	attributes={
	 *		"openapi_context"={
	 *			"type"="string",
	 *          "format"="UUID",
     *          "required"="true",
	 *          "readOnly"=false
	 *		}
	 *	}
	 * )
	 * @Groups({"setLocation"})
	 */
	public $setLocation;	
	
	/**
	 * Het ID van het primaire product van dit huwelijk. <br /><b>Schema:</b> <a href="https://schema.org/identifier">https://schema.org/identifier</a>
	 *
	 * @var integer
	 * @ApiProperty(
	 * 	attributes={
	 *		"openapi_context"={
	 *			"type"="string",
	 *          "format"="UUID",
	 *          "required"="true",
	 *          "readOnly"=false
	 *		}
	 *	}
	 * )
	 * @Groups({"setProduct"})
	 */
	public $setProduct;	
	
	/**
	 * Het ID van de ambtenaaar die je wilt verzoeken dit huwelijk te voltrekken. <br /><b>Schema:</b> <a href="https://schema.org/identifier">https://schema.org/identifier</a>
	 *
	 * @var integer
	 * @ApiProperty(
	 * 	attributes={
	 *		"openapi_context"={
	 *			"type"="string",
	 *          "format"="UUID",
	 *          "required"="true",
	 *          "readOnly"=false
	 *		}
	 *	}
	 * )
	 * @Groups({"requestOfficial"})
	 */
	public $setOfficial;	
	
	
	/**
	 * Het BSN nummer van een partner om een huwelijk op te vinden/checken. <br /><b>Schema:</b> <a href="https://schema.org/identifier">https://schema.org/identifier</a>
	 *
	 * @var string
	 * @ApiProperty(
	 * 	attributes={
	 *		"openapi_context"={
	 *			"type"="string",
	 *          "format"="UUID",
	 *          "required"="true",
	 *          "readOnly"=false
	 *		}
	 *	}
	 * )
	 * @Groups({"BSN"})
	 */
	public $bsn;	
	
	/**
	 * Een link, naar bijvoorbeeld een betaal scherm. <b>Schema:</b> <a href="https://schema.org/URL">https://schema.org/URL</a>
	 *
	 * @var string
	 * @ApiProperty(
	 * 	attributes={
	 *		"openapi_context"={
	 *			"type"="url",
	 *          "required"="true",
	 *          "readOnly"=true
	 *		}
	 *	}
	 * )
	 * @Groups({"payment"})
	 */
	public $url;
	
	/**
	 * @return string
	 */
	public function toString(){
		$renderPartners = implode(' en ', $this->partners);
		return printf("%s van %s.",$this->type, $renderPartners);
	}
	
	/**
	 * Vanuit rendering perspectief (voor bijvoorbeeld logging of berichten) is het belangrijk dat we een entiteit altijd naar string kunnen omzetten.
	 */
	public function __toString()
	{
		return $this->toString();
	}
	
	public function __construct()
	{
		$this->partners = new ArrayCollection();
		$this->getuigen = new ArrayCollection();
		$this->ambtenaren = new ArrayCollection();
		$this->producten = new ArrayCollection();
	}
	
	
	/**
	 * The pre persist function is called when the entity is first saved to the database and allows us to set some aditional first values
	 *
	 * @ORM\PrePersist
	 */
	public function prePersist()
	{
		$this->registratiedatum = new \ Datetime();
		// We want to add some default stuff here like products, productgroups, paymentproviders, templates, clientGroups, mailinglists and ledgers
		return $this;
	}
	
	/**
	 * Called afther the entity is retrieved from the database.
	 *
	 * @ORM\PostLoad 
	 */
	public function PostLoad()
	{
		// Get values forprimary product
		if($this->primairProduct){
			$this->exclAmount = $this->primairProduct->exclAmount;
			$this->inclAmount= $this->primairProduct->inclAmount;
			
		}
		
		// Get values for extra products
		foreach($this->additioneleProducten as $product){			
			$this->exclAmount = $this->exclAmount + $product->exclAmount;
			$this->inclAmount=  $this->inclAmount + $product->inclAmount;
		}
		// We want to add some default stuff here like products, productgroups, paymentproviders, templates, clientGroups, mailinglists and ledgers
		return $this;
	}
	
	
	/**
	 * De zaakrepresentatie maakt een array aan conform ZTC specs, die we kunnen gebruiken om een zaak in te schieten. 
	 * 
	 */
	public function getZaakRepresentation()
	{
		$array = [];
		/*
		$array['registratiedatum'] = \date_format($this->registratiedatum, "Y-m-d");
		$array['startdatum'] = \date_format($this->registratiedatum, "Y-m-d");
		$array['einddatumGepland'] = \date_format($this->trouwdatum, "Y-m-d");
		$array['uiterlijkeEinddatumAfdoening'] = \date_format($this->trouwdatum, "Y-m-d");
		$array['publicatiedatum'] = \date_format($this->registratiedatum, "Y-m-d");
		$array['vertrouwelijkheidaanduiding'] = "openbaar";
		$array['betalingsindicatie'] = "nog_niet";
		*/
		//$array['laatsteBetaaldatum'] = \date_format($this->trouwdatum, "Y-m-d"); /*@todo herschijven naar datetime, waarom GAMMA hier wel weer datetime gebruikt mogen de goden weten*/
		
		return $array;		
	}
	
	public function getId(): ?int
	{
		return $this->id;
	}
	
	
	public function getAanvraag()
	{
		return $this->aanvraag;
	}
	
	public function setAanvraag($aanvraag)
	{ 
		$this->aanvraag = $aanvraag;
		return $this;
	}
		
	public function getMelding()
	{
		return $this->melding;
	}
	
	public function setMelding($melding)
	{
		$this->melding= $melding;
		return $this;
	}
		
	public function getRegistratiedatum()
	{
		if(!$this->registratiedatum){
			return new \Datetime();
		}
		
		return $this->registratiedatum;
	}
	
	
	public function getTrouwdatum()
	{		
		if(!$this->datum){
			return new \Datetime();
		}
		
		return $this->datum;
	}
	
	/**
	 * Add HuwelijkPartner
	 *
	 * @param  \App\Entity\Huwelijk\HuwelijkPartner $partner
	 * @return Huwelijk
	 */
	public function addPartner(\App\Entity\Huwelijk\HuwelijkPartner $partner)
	{
		$this->partners[] = $partner;
		
		return $this;
	}
	
	/**
	 * Remove HuwelijkPartner
	 *
	 * @param \App\Entity\Partner $partner
	 * @return Huwelijk
	 */
	public function removePartner(\App\Entity\Huwelijk\HuwelijkPartner $partner)
	{
		$this->partners->removeElement($partner);
		
		return $this;
	}
	
	/**
	 * Get HuwelijkPartners
	 *
	 * @return \Doctrine\Common\Collections\Collection
	 */
	public function getPartners()
	{
		return $this->partners;
	}
}
