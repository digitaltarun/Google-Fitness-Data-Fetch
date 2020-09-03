<?php
// src/Controller/LuckyController.php
namespace App\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use SevenShores\Hubspot\Factory;
use SevenShores\Hubspot\Resources\OAuth2;
use vendor\google\apiclient\src\Google;
use Symfony\Component\Routing\Annotation\Route;


class LuckyController extends AbstractController
{
    
    /**
     * @Route("/apiauth", name="api_index")
     */
    public function index(): Response
    {
        return $this->render('/apiauth/index.html.twig', [
            
        ]);
    }
    
    
    /**
     * @Route("/lucky", name="google_api")
     */

    public function number(): Response
    {
        
        $client = new \Google_Client();
        $client->setAuthConfig('client_secret.json');
        $client->addScope(array("https://www.googleapis.com/auth/fitness.activity.read","https://www.googleapis.com/auth/fitness.activity.write"));
        $client->setRedirectUri('http://' . $_SERVER['HTTP_HOST'] . '/lucky');
        // offline access will give you both an access and refresh token so that
        // your app can refresh the access token without user interaction.
        $client->setAccessType('offline');
        // Using "consent" ensures that your application always receives a refresh token.
        // If you are not using offline access, you can omit this.
        // $client->setApprovalPrompt("consent");
        $client->setIncludeGrantedScopes(true); 
        $auth_url = $client->createAuthUrl();
        if (isset($_GET['code'])) {
            $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
            foreach ($token as $key => $values) {
                    echo $key .' == '.$values."<BR />";
                }  
            $fitness = new \Google_Service_Fitness($client);
            $optParams = array(
                'userId' => 'me',
              );
            $response = $fitness->users_dataSources->listUsersDataSources("me");
            while($response->valid()) {
                $dataSourceItem = $response->next();
                echo $dataSourceItem['dataType']['name']. "<BR />";
            }
            return new Response(
                '<html><body>Lucky number: --</body></html>'
            );
        } else {
            
            return $this->redirect($client->createAuthUrl());
        }
        // header('Location: ' . filter_var($auth_url, FILTER_SANITIZE_URL)); 
          
        
    }
}