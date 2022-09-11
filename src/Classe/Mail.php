<?php

namespace App\Classe;

use Mailjet\Client;
use Mailjet\Resources;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class Mail
{
    private $api_key = 'bc402d472f65b5cecebe016e01d05d89';    

    
    public function send($to_email, $to_name, $subject, $content, $apiKey)
    { 
        
        $mj = new Client($this->api_key, $apiKey, true,['version' => 'v3.1']);
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