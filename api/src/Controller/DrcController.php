<?php

// src/Controller/DashboardController.php

namespace App\Controller;

use Conduction\CommonGroundBundle\Service\CommonGroundService;
use Doctrine\ORM\EntityManagerInterface;
use GuzzleHttp\Client;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class DashboardController.
 *
 * @Route("/drc")
 */
class DrcController extends AbstractController
{
    /**
     * @Route("/download/{resource}")
     * @Template
     */
    public function downloadAction(Request $request, EntityManagerInterface $em, CommonGroundService $commonGroundService, $resource)
    {
        $token = $commonGroundService->getJwtToken('drc');

        $result = $commonGroundService->getResource(['component'=>'drc', 'type'=>'enkelvoudiginformatieobjecten', 'id'=>$resource]);

        $headers = ['Authorization'=>'Bearer '.$token];
        $guzzleConfig = [
            'http_errors' => false,
            'timeout'     => 4000.0,
            'headers'     => $headers,
            'verify'      => false,
        ];

        // Lets start up a default client
        $client = new Client($guzzleConfig);

        $data = $client->get($result['inhoud'])->getBody()->getContents();

        $response = new Response(
            $data,
            Response::HTTP_OK,
            ['content-type'=> $result['formaat'], 'Content-Disposition'=>'attachment; filename='.$result['bestandsnaam']],
        );

        $response->send();
    }
}
