<?php

// src/Controller/DefaultController.php

namespace App\Controller;

use Conduction\CommonGroundBundle\Service\CommonGroundService;
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

        $variables['types'][] = [
            'name'=> 'Akte van geboorte',
            'type'=> 'akte_van_geboorte',
        ];
        $variables['types'][] = [
            'name'=> 'Akte van huwelijk',
            'type'=> 'akte_van_huwelijk',
        ];
        $variables['types'][] = [
            'name'=> 'Akte van overlijden',
            'type'=> 'akte_van_overlijden',
        ];
        $variables['types'][] = [
            'name'=> 'Akte van registratie van een partnerschap',
            'type'=> 'akte_van_registratie_van_een_parterschap',
        ];
        $variables['types'][] = [
            'name'=> 'Akte van omzetting van een huwelijk in een registratie van een partnerschap',
            'type'=> 'akte_van_omzetting_van_een_huwelijk_in_een_registratie_van_een_partnerschap',
        ];
        $variables['types'][] = [
            'name'=> 'Akte van omzetting van een registratie van een partnerschap',
            'type'=> 'akte_van_omzetting_van_een_registratie_van_een_partnerschap',
        ];
        $variables['types'][] = [
            'name'=> 'Verklaring van huwelijksbevoegdheid',
            'type'=> 'verklaring_van_huwelijksbevoegdheid',
        ];
        $variables['types'][] = [
            'name'=> 'Verklaring van in leven zijn',
            'type'=> 'verklaring_van_in_leven_zijn',
        ];
        $variables['types'][] = [
            'name'=> 'Verklaring van Nederlanderschap',
            'type'=> 'verklaring_van_nederlanderschap',
        ];
        $variables['types'][] = [
            'name'=> 'Uittreksel basis registratie personen',
            'type'=> 'uittreksel_basis_registratie_personen',
        ];
        $variables['types'][] = [
            'name'=> 'Uittreksel registratie niet ingezetenen',
            'type'=> 'uittreksel_registratie_niet_ingezetenen',
        ];
        $variables['types'][] = [
            'name'=> 'Historisch uittreksel basis registratie personen',
            'type'=> 'historisch_uittreksel_basis_registratie_personen',
        ];

        if ($this->getUser() && $this->getUser()->getPerson()) {
            $variables['certificates'] = $commonGroundService->getResourceList('https://waardepapieren-gemeentehoorn.commonground.nu/api/v1/wari/certificates', ['person' => $this->getUser()->getPerson()])['hydra:member'];
//            $variables['certificates'][] = array('type' => 'geboorte akte', 'created' => '17-09-2020', 'id' => '1');
        }

        if ($request->isMethod('POST')) {
            $variables['certificate'] = $request->request->all();
            var_dump($variables['certificate']);
            $variables['certificate'] = $commonGroundService->createResource($variables['certificate'], 'https://waardepapieren-gemeentehoorn.commonground.nu/api/v1/waar/certificates');
        }

//        $variables['claim'] = base64_encode(json_encode(array("Peter"=>35, "Ben"=>37, "Joe"=>43)));

        return $variables;
    }
}
