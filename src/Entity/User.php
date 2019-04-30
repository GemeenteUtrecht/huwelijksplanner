<?php
// api/src/Entity/User.php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;
use FOS\UserBundle\Model\User as BaseUser;
use FOS\UserBundle\Model\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use ActivityLogBundle\Entity\Interfaces\StringableInterface;

/**
 * Een user of applicatie is een unique geidentificeerde entiteit wat een onderdeel van een organisatie kan zijn.
 * 
 * @ORM\Entity
 * @ORM\Table(name="fos_user")
 * @Gedmo\Loggable(logEntryClass="ActivityLogBundle\Entity\LogEntry")
 * @ApiResource(
 *     normalizationContext={"groups"={"user", "user:read"}},
 *     denormalizationContext={"groups"={"user", "user:write"}}
 * )
 */
class User extends BaseUser implements StringableInterface
{
	/**
	 * @ORM\Id
	 * @ORM\Column(type="integer")
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	protected $id;
		
	/**
     * @Gedmo\Versioned
	 * @ORM\Column()
	 * @Groups({"user"})
	 */
	protected $fullname;
	
	/**
	 * @Groups({"user:write"})
	 */
	protected $plainPassword;/**
		
	/**
	 * De voorkeurstaal van deze gebruiker <br /><b>Schema:</b> <a href="https://www.ietf.org/rfc/rfc3066.txt">https://www.ietf.org/rfc/rfc3066.txt</a>
	 *
	 * @var string Een Unicode language identifier, ofwel RFC 3066 taalcode.
	 *
     * @Gedmo\Versioned
	 * @ORM\Column(
	 *     type     = "string",
	 *     length   = 17
	 * )
	 * @Groups({"read", "write"})
	 * @Assert\Language
	 * @Assert\Length(
	 *      min = 2,
	 *      max = 17,
	 *      minMessage = "De taal moet tenminste {{ limit }} tekens lang zijn",
	 *      maxMessage = "De taal kan niet langer dan {{ limit }} tekens zijn"
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
	 */
	public $taal = 'nl';
	
	/**
	 * De Organisatie waar aan deze gebruiker behoord
	 *
	 * @var \App\Entity\Organisatie
	 * @ORM\ManyToOne(targetEntity="\App\Entity\Organisatie", cascade={"persist", "remove"}, inversedBy="users")
	 * @ORM\JoinColumn(referencedColumnName="id")
	 *
	 */
	public $bronOrganisatie;
	
	/**
	 * @return string
	 */
	public function toString(){
		return $this->fullname;
	}
	
	/**
	 * Vanuit rendering perspectief (voor bijvoorbeeld logging of berichten) is het belangrijk dat we een entiteit altijd naar string kunnen omzetten.
	 */
	public function __toString()
	{
		return $this->toString();
	}
	
	public function setFullname(?string $fullname): void
	{
		$this->fullname = $fullname;
	}
	
	public function getFullname(): ?string
	{
		return $this->fullname;
	}
	
	public function isUser(?UserInterface $user = null): bool
	{
		return $user instanceof self && $user->id === $this->id;
	}
}
