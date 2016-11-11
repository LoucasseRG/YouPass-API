<?php

/**
 * Version 0.1
 * Créé par Walky
 * Permet seulement de récupérer quelques informations sur l'utilisateur
 **/

require_once 'Zend/Http/Client.php';

class YouPassAPI
{
    protected $connectLink = 'https://www.youpass.com/fr/loginpopup'; // lien de la requête de conenxion

    protected $solde;
    protected $userId;
    protected $firstName;
    protected $lastName;
    protected $realTimeId;

    protected $error;

    protected $client;

    // connexion à YouPass
    public function loginRequest($email, $password)
    {
        $this->client = new Zend_Http_Client($this->connectLink);
        $this->client->setParameterPost(array( // données du formulaire de connexion
            'email_user_login'    => (string) $email,
            'password_user_login' => (string) $password,
            'rememberLoginPopup'  => false,
            'place'               => 'popup'
        ));

        $this->client->setConfig(array( // un peu de configurations
            'maxredirects' => 0,
            'keepalive' => true,
            'timeout' => 20
        ));

        $this->client->setHeaders(array( // les headers
            'X-Requested-With' => 'XMLHttpRequest',
            'Accept'           => '*/*',
            'Origin'           => 'https://www.youpass.com',
            'User-Agent'       => 'Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/54.0.2840.54 Safari/537.36',
            'Content-Type'     => 'application/x-www-form-urlencoded; charset=UTF-8',
            'Referer'          => 'https://www.youpass.com/fr',
            'Accept-Encoding'  => 'gzip, deflate, br',
            'Accept-Language'  => 'fr-FR,fr;q=0.8,en-US;q=0.6,en;q=0.4'
        ));

        $response = $this->client->request('POST'); // la requête de connexion sur YouPass passe par la méthode POST
        $response = json_decode($response->getBody(), true); // on décode la réponse json

        if ($response['error']['error'])
        {
            $this->error = $response['error']['messages'][0];
            return false;
        }
        else
        {
            $this->setData($response);
            return true;
        }
    }

    // on met les données dans des variables qu'on affichera à l'utilisateur
    public function setData($response)
    {
        // façon étrange de mettre les données dans l'array 'error'
        $this->solde      = $response['error']['user']['pay_usr']; // solde du compte
        $this->userId     = $response['error']['id_usr']; // ID de l'utilisateur
        $this->firstName  = $response['error']['user']['firstname_usr']; // prénom
        $this->lastName   = $response['error']['user']['lastname_usr']; // nom de famille
        $this->realTimeId = $response['error']['user']['real_time_id_usr'];
    }

    // permet de récupérer les données
    public function getData()
    {
        $data = array(
            'solde'      => $this->solde,
            'userId'     => $this->userId,
            'firstName'  => $this->firstName,
            'lastName'   => $this->lastName,
            'realTimeId' => $this->realTimeId
        );

        return $data;
    }

    // permet de récupérer l'erreur (si présente)
    public function getError()
    {
        return $this->error;
    }
}

$instance = new YouPassAPI();
$instance->loginRequest('xxxxxx@xxxxxxx.com', 'PassWord');

?>
