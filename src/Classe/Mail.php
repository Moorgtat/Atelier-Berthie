<?php

namespace App\Classe;

use Mailjet\Client;
use Mailjet\Resources;

Class Mail 
{
  private $api_key = "eb1d9a140b822c604dde18ec401d9c8f";
  private $api_key_secret =  "93fc677a3a8401370f5a85d084ca3c8c";

  public function send($to_email, $to_name, $subject, $content) 
  {
    $mj = new Client($this->api_key, $this->api_key_secret, true, ['version' => 'v3.1']);
    $body = [
        'Messages' => [
            [
                'From' => [
                    'Email' => "mat.lecardinal@gmail.com",
                    'Name' => "Le Studio Dev"
                ],
                'To' => [
                    [
                        'Email' => $to_email,
                        'Name' => $to_name
                    ]
                ],
                'TemplateID' => 1888183,
                'TemplateLanguage' => true,
                'Subject' => $subject,
                'Variables' => [
                    'content' => $content
                ]
            ]
        ]
    ];
    $response = $mj->post(Resources::$Email, ['body' => $body]);
    $response->success();
  }
}