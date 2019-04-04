<?php
// api/src/Controller/CreateBookPublication.php

namespace App\Controller;

use App\Entity\Huwelijk;

class HuwelijkController
{
	public function __invoke(Huwelijk $data): Huwelijk
	{
		//$this->bookPublishingHandler->handle($data);
		
		return $data;
	}
}