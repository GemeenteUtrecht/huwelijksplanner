<?php

namespace App\Swagger;

use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

final class SwaggerDecorator implements NormalizerInterface
{
	public $params;
	private $decorated;
	
	public function __construct(NormalizerInterface $decorated, array $params = [])
	{
		$this->decorated = $decorated;
		$this->params = $params;
	}
	
	public function normalize($object, $format = null, array $context = [])
	{
		$docs = $this->decorated->normalize($object, $format, $context);
		
		return array_merge($docs, $this->params);
	}
	
	public function supportsNormalization($data, $format = null)
	{
		return $this->decorated->supportsNormalization($data, $format);
	}
}