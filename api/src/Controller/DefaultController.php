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

//        if ($this->getUser()) {
//            $person = $commonGroundService->getResource($this->getUser()->getPerson());
//            $personUrl = $commonGroundService->cleanUrl(['component' => 'cc', 'type' => 'people', 'id' => $person['id']]);
//        }

//        $variables['claim'] = base64_encode(json_encode(array("Peter"=>35, "Ben"=>37, "Joe"=>43)));

        if ($request->isMethod('POST')) {
            $variables['certificate'] = $request->request->all();
            $variables['certificate'] = $commonGroundService->createResource($variables['certificate'], 'https://waardepapieren-gemeentehoorn.commonground.nu/api/v1/waar/certificates');
        }

        return $variables;
    }
}
