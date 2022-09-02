<?php

namespace App\Classe;

use Mailjet\Client;
use Mailjet\Resources;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;

// use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;

class Mail
{
    private $api_key = 'bc402d472f65b5cecebe016e01d05d89';
    private $secret_key =  '9e513947f81b1d7e42ad6f22e09c0ec0';

   
    
    public function send($to_email, $to_name, $subject, $content)
    {        
        
        
        $mj = new Client($this->api_key, $this->secret_key, true,['version' => 'v3.1']);
        $body = [
            'Messages' => [
                [
                    'From' => [
                        'Email' => "greg.marini@hotmail.fr",
                        'Name' => "Grégory MARINI"
                    ],
                    'To' => [
                        [
                            'Email' => $to_email,
                            'Name' => $to_name
                        ]
                    ],
                    'TemplateID' => 4153575,
                    'TemplateLanguage' => true,
                    'Subject' => $subject,
                    'Variables' => [
                        'content' => $content,
                        
                    ]
                ]
            ]
        ];
        $response = $mj->post(Resources::$Email, ['body' => $body]);
        $response->success();
    }
}