<?php

namespace App\Classe;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class RetServ
{    
    public function __construct(ParameterBagInterface $parameterBag)
    {
        $this->parameterBag = $parameterBag;
    }  
    
    public function getApiSecretKey(): string
    {
        return $this->parameterBag->get('MAILJET_SECRET_KEY');
    }
    
    
}