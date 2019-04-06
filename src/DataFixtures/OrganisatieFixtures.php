<?php

// https://symfony.com/doc/master/bundles/DoctrineFixturesBundle/index.html

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;


use App\Entity\Organisatie;
use App\Entity\Persoon;

class OrganisatieFixtures extends Fixture
{
	public const BRON_ORGANISATIE_REFERENCE = 'bron-organisatie';
	public const CONTACT_PERSOON_REFERENCE = 'contact-persoon';
	
    public function load(ObjectManager $manager)
    {
    	// hoogste ID: 1148
    	
    	$organisatie = new Organisatie();
    	$organisatie->rsin= "0022.20.647";
    	$organisatie->kvk= "30280353";
    	$organisatie->btw= "NL 0022.20.647.B01";
    	$organisatie->eori= "NL 0022.20.647";
    	$organisatie->telefoon = "";
    	$organisatie->email= "";
    	$organisatie->naam = "Gemeente Zuiddrecht";
    	$organisatie->beschrijving = "Gelegen in het prachtige zuidelijke deel van de provincie Drecht";
    	$manager->persist($organisatie);
    	
    	// other fixtures can get this object using the OrganisatieFixtures::BRON_ORGANISATIE_REFERENCEconstant
    	$this->addReference(self::BRON_ORGANISATIE_REFERENCE, $organisatie);
    	
    	$contactPersoon= new Persoon();
    	$contactPersoon->bronOrganisatie= $organisatie;
    	$contactPersoon->voornamen = "John";
    	$contactPersoon->geslachtsnaam= "Doh";
    	$contactPersoon->emailadres= "john@do.com";
    	$contactPersoon->telefoonnummer= "0645536677";
    	$contactPersoon->registratieDatum = new \ Datetime();
    	$contactPersoon->taal= "nl";
    	$manager->persist($contactPersoon);
    	    	
    	// other fixtures can get this object using the OrganisatieFixtures::CONTACT_PERSOON_REFERENCEconstant
    	$this->addReference(self::CONTACT_PERSOON_REFERENCE, $contactPersoon);
    	
    	// Lets save te created entities
    	$manager->flush();        
    }
}
