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
 * @Route("/wrc")
 */
class WrcController extends AbstractController
{
    /**
     * @Route("/organization")
     * @Template
     */
    public function organizationAction(Session $session, Request $request, CommonGroundService $commonGroundService, ApplicationService $applicationService, ParameterBagInterface $params, string $slug = 'home')
    {
        $variables = [];
        $variables['resources'] = $commonGroundService->getResourceList(['component'=>'wrc', 'type'=>'organizations'])['hydra:member'];

        if ($request->isMethod('POST')) {
            $resource = $request->request->all();

            $org['@id'] = $resource['@id'];
            $org['name'] = $resource['name'];
            $org['description'] = $resource['description'];
            $org['contact'] = $resource['contact'];

            if (!empty($org['contact'])) {
                $orgCc = $commonGroundService->getResource($org['contact']);
                $org['contact'] = $orgCc['@id'];
            } else {
                $orgCc = $commonGroundService->createResource(['component'=>'cc', 'type'=>'organizations'])['@id'];
                $org['contact'] = $orgCc['@id'];
            }

            $email['email'] = $resource['email'];
            $emails = $resource['emails'];
            if (!empty($email) && !empty($orgCc['emails'][0])) {
                $email = $commonGroundService->saveResource($email, $orgCc['emails'][0]['@id']);
            } elseif (!empty($email)) {
                $email = $commonGroundService->createResource($email, ['component'=>'cc', 'type'=>'emails']);
                $orgCc['emails'][] = $email;
            } elseif (!empty($emails)) {
                for ($i = 0; $i < count($emails); $i++) {
                    if (!empty($orgCc['emails'][$i])) {
                        $orgCc['emails'][$i] = $commonGroundService->saveResource($emails[$i], $orgCc['emails'][$i]['@id']);
                    } else {
                        $orgCc['emails'][$i] = $commonGroundService->createResource($emails[$i], ['component'=>'cc', 'type'=>'emails']);
                    }
                }
            }

            $telephone['telephone'] = $resource['telephone'];
            $telephones = $resource['telephones'];
            if (!empty($telephone) && !empty($orgCc['telephones'][0])) {
                $telephone = $commonGroundService->saveResource($telephone, $orgCc['telephones'][0]['@id']);
            } elseif (!empty($telephone)) {
                $telephone = $commonGroundService->createResource($telephone, ['component'=>'cc', 'type'=>'telephones']);
                $orgCc['telephones'][] = $telephone;
            } elseif (!empty($telephones)) {
                for ($i = 0; $i < count($telephones); $i++) {
                    if (!empty($orgCc['telephones'][$i])) {
                        $orgCc['telephones'][$i] = $commonGroundService->saveResource($telephones[$i], $orgCc['telephones'][$i]['@id']);
                    } else {
                        $orgCc['telephones'][$i] = $commonGroundService->createResource($telephones[$i], ['component'=>'cc', 'type'=>'telephones']);
                    }
                }
            }

            $social['website'] = $resource['website'];
            $social['facebook'] = $resource['facebook'];
            $social['twitter'] = $resource['twitter'];
            $social['linkedin'] = $resource['linkedin'];
            $social['instagram'] = $resource['instagram'];

            if (!empty($orgCc['socials'][0])) {
                $orgCc['social'] = $commonGroundService->saveResource($social, $orgCc['social']['@id']);
            } else {
                $orgCc['social'][0] = $commonGroundService->createResource($social, ['component'=>'cc', 'type'=>'socials']);
            }

            $commonGroundService->saveResource($org, $org['@id']);

            return $this->redirect($this->generateUrl('app_wrc_organization'));
        }

        return $variables;
    }
}
