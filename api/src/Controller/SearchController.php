<?php

// src/Controller/OrcController.php

namespace App\Controller;

//use App\Service\RequestService;
use Conduction\CommonGroundBundle\Service\CommonGroundService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;

/**
 * The Request test handles any calls that have not been picked up by another test, and wel try to handle the slug based against the wrc.
 *
 * Class RequestController
 *
 * @Route("/search")
 */
class SearchController extends AbstractController
{
    /**
     * @Route("/")
     * @Template
     */
    public function indexAction(Session $session, Request $request, CommonGroundService $commonGroundService)
    {
        // Lets provide this data to the template
        $variables['query'] = array_merge($request->query->all(), $request->request->all());

        // Bugy dit moeten de componenten zelf opvangen
        if (array_key_exists('search', $variables['query'])) {
            $variables['query']['name'] = $variables['query']['search'];
            $variables['query']['description'] = $variables['query']['search'];
            $variables['query']['content'] = $variables['query']['search'];
        }

        $variables['resources'] = $commonGroundService->getResourceList(['component'=>'wrc', 'type'=>'templates'], $variables['query'])['hydra:member'];

        return $variables;
    }
}
