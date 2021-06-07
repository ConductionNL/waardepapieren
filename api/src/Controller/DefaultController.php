<?php

// src/Controller/DefaultController.php

namespace App\Controller;

use Conduction\CommonGroundBundle\Service\CommonGroundService;
use GuzzleHttp\Client;
use Ramsey\Uuid\Uuid;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
/**
 * The Procces test handles any calls that have not been picked up by another test, and wel try to handle the slug based against the wrc.
 *
 * Class DefaultController
 *
 * @Route("/")
 */
class DefaultController extends AbstractController
{
    /**
     * @Route("/")
     * @Template
     */
    public function indexAction(CommonGroundService $commonGroundService, Request $request, ParameterBagInterface $params)
    {
        // On an index route we might want to filter based on user input
        $variables['query'] = array_merge($request->query->all(), $variables['post'] = $request->request->all());

        $variables['uid'] = Uuid::uuid4();

        $variables['types'][] = [
            'name' => 'Akte van geboorte',
            'type' => 'akte_van_geboorte',
            'price' => '14'
        ];
//        $variables['types'][] = [
//            'name'=> 'Akte van overlijden',
//            'type'=> 'akte_van_overlijden',
//        ];
        $variables['types'][] = [
            'name' => 'Verklaring van in leven zijn',
            'type' => 'verklaring_van_in_leven_zijn',
            'price' => '14'
        ];
//        $variables['types'][] = [
//            'name'=> 'Verklaring van Nederlanderschap',
//            'type'=> 'verklaring_van_nederlanderschap',
//        ];
//        $variables['types'][] = [
//            'name'=> 'Uittreksel basis registratie personen',
//            'type'=> 'uittreksel_basis_registratie_personen',
//        ];
        $variables['types'][] = [
            'name' => 'Historisch uittreksel basis registratie personen',
            'type' => 'historisch_uittreksel_basis_registratie_personen',
            'price' => '14'
        ];

        if ($this->getUser() && $this->getUser()->getPerson()) {
            $variables['certificates'] = $commonGroundService->getResourceList(['component' => 'wari', 'type' => 'certificates'], ['person' => $this->getUser()->getPerson()])['hydra:member'];
            $variables['certificates'][] = array('type' => 'geboorte akte', 'created' => '17-09-2020', 'id' => '1');
        }

        if ($request->isMethod('POST')) {
            $variables['certificate'] = $request->request->all();
            $typeinfo = json_decode($variables['certificate']['typeinfo']);
            $variables['certificate']['type'] = $typeinfo->type;
            $variables['certificate']['organization'] = '001516814';
//            $variables['certificate']['person'] = 'testpersoonbarry';

//            Re enable
//            $variables['certificate'] = $commonGroundService->createResource($variables['certificate'], ['component' => 'waar', 'type' => 'certificates']);

//            $variables['certificate']['claim'] = base64_encode(json_encode($variables['certificate']['claim']));
//            if (isset($typeinfo->price)) {
//                $variables['certificate']['price'] = $typeinfo->price;
//                Guzzle call to ... ?
//                $headers = [
//                    'Accept'        => 'application/ld+json',
//                    'Content-Type'  => 'application/json',
//                    'PSPID' => 'gemhoorn',
//                    'shaSignature' => 'ZabNz@ASFrZy5Hg6',
//                    'signatureHashAlgorithm' => 'Sha256',
//                    'signatureMethod' => 'AllParameters'
//                ];
//
//                $client = new Client([
//                    'headers'  => $headers,
//                    'timeout'  => 30.0,
//                ]);
//
//                $body = [
//                    'price' => $variables['certificate']['price']
//                ];
//
//                $response = $client->request('POST', 'https://secure.ogone.com/ncol/test/orderstandard.asp', [
//                    'json'         => $body,
//                ]);
//
//                $response = json_decode($response->getBody()->getContents(), true);
//
//                var_dump($response);die;
//
//                header("Location: " + $response['paymentUrl?']);
//                exit;
//            }
        }

//        $variables['claim'] = base64_encode(json_encode(array("Peter"=>35, "Ben"=>37, "Joe"=>43)));

        return $variables;
    }

    /**
     * @Route("/certificate/{id}")
     * @Template
     */
    public function certificateAction(CommonGroundService $commonGroundService, Request $request, ParameterBagInterface $params, $id)
    {
        $variables['certificate'] = $commonGroundService->getResource(['component' => 'waar', 'type' => 'certificates', 'id' => $id]);


        return $variables;
    }

}
