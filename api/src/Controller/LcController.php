<?php

// src/Controller/OrcController.php

namespace App\Controller;

use Conduction\CommonGroundBundle\Service\ApplicationService;
//use App\Service\RequestService;
use Conduction\CommonGroundBundle\Service\CommonGroundService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;

/**
 * The Request test handles any calls that have not been picked up by another test, and wel try to handle the slug based against the wrc.
 *
 * Class RequestController
 *
 * @Route("/lc")
 */
class LcController extends AbstractController
{
    /**
     * @Route("/organization")
     * @Template
     */
    public function organizationAction(Session $session, Request $request, CommonGroundService $commonGroundService, ApplicationService $applicationService, ParameterBagInterface $params, string $slug = 'home')
    {
        $variables = [];
        $variables['places'] = $commonGroundService->getResourceList(['component'=>'lc', 'type'=>'places'])['hydra:member'];
        $variables['organizations'] = $commonGroundService->getResourceList(['component' => 'wrc', 'type' => 'organizations'])['hydra:member'];

        if ($request->isMethod('POST')) {
            $resource = $request->request->all();

            if (isset($resource['publicAccess'])) {
                $resource['publicAccess'] = true;
            } else {
                $resource['publicAccess'] = false;
            }

            if (isset($resource['smokingAllowed'])) {
                $resource['smokingAllowed'] = true;
            } else {
                $resource['smokingAllowed'] = false;
            }

            $commonGroundService->saveResource($resource, (['component'=>'lc', 'type'=>'places']));

            return $this->redirect($this->generateUrl('app_lc_organization'));
        }

        return $variables;
    }

    /**
     * @Route("/places")
     * @Template
     */
    public function placesAction(Session $session, Request $request, CommonGroundService $commonGroundService, ApplicationService $applicationService, ParameterBagInterface $params, string $slug = 'home')
    {
        $variables = [];
        $variables['places'] = $commonGroundService->getResourceList(['component'=>'lc', 'type'=>'places'], ['organization'=>$this->getUser()->getOrganization()])['hydra:member'];

        return $variables;
    }
}
