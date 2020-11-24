<?php

// src/Controller/ProcessController.php

namespace App\Controller;

use Conduction\CommonGroundBundle\Service\ApplicationService;
//use App\Service\RequestService;
use Conduction\CommonGroundBundle\Service\CommonGroundService;
use function GuzzleHttp\Promise\all;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;

/**
 * The Procces test handles any calls that have not been picked up by another test, and wel try to handle the slug based against the wrc.
 *
 * Class ProcessController
 *
 * @Route("/cmc")
 */
class CmcController extends AbstractController
{
    /**
     * @Route("/user")
     * @Template
     */
    public function userAction(Session $session, Request $request, CommonGroundService $commonGroundService, ApplicationService $applicationService, ParameterBagInterface $params, string $slug = 'home')
    {
        $variables = [];
        $variables['reciever'] = $commonGroundService->getResourceList(['component' => 'cmc', 'type' => 'contact_moments'], ['receiver' => $this->getUser()->getPerson()])['hydra:member'];
        $variables['send'] = $commonGroundService->getResourceList(['component' => 'cmc', 'type' => 'contact_moments'], ['sender' => $this->getUser()->getPerson()])['hydra:member'];
        $variables['resources'] = array_merge($variables['reciever'], $variables['send']);

        return $variables;
    }

    /**
     * @Route("/organisation")
     * @Template
     */
    public function organisationAction(Session $session, Request $request, CommonGroundService $commonGroundService, ApplicationService $applicationService, ParameterBagInterface $params, string $slug = 'home')
    {
        $variables = [];
        $variables['resources'] = $commonGroundService->getResourceList(['component'=>'brc', 'type'=>'invoices'], ['submitters.brp'=>$variables['user']['@id']])['hydra:member'];

        return $variables;
    }

    /**
     * This function shows all available processes.
     *
     * @Route("/")
     * @Template
     */
    public function indexAction(Session $session, Request $request, CommonGroundService $commonGroundService, ApplicationService $applicationService, ParameterBagInterface $params)
    {
        $variables = $applicationService->getVariables();
        $variables['reciever'] = $commonGroundService->getResourceList(['component' => 'cmc', 'type' => 'contact_moments'], ['receiver' => $this->getUser()->getPerson()])['hydra:member'];
        $variables['send'] = $commonGroundService->getResourceList(['component' => 'cmc', 'type' => 'contact_moments'], ['sender' => $this->getUser()->getPerson()])['hydra:member'];

        $variables['resources'] = array_merge($variables['reciever'], $variables['send']);

        return $variables;
    }

    /**
     * This function will kick of the suplied proces with given values.
     *
     * @Route("/send")
     * @Template
     */
    public function sendAction(Session $session, Request $request, CommonGroundService $commonGroundService, ApplicationService $applicationService, ParameterBagInterface $params)
    {
        $variables = $applicationService->getVariables();

        // We accept any and all ways of pre-filling the form
        $variables['resource'] = array_merge($request->query->all(), $request->request->all());

        // We need to fall back on resources
        if (!array_key_exists('resources', $variables['resource'])) {
            $variables['resource']['resources'] = [];
        }

        // Is a single resource has been passed we need to supply it to the resources array
        if (array_key_exists('resource', $variables['resource'])) {
            $variables['resource']['resources'][] = $variables['resource']['resource'];
            unset($variables['resource']['resource']);
        }

        // We need an sender on messages
        if (!array_key_exists('sender', $variables['resource']) && $user = $this->getUser()) {
            $variables['resource']['sender'] = $user->getPerson();
        }

        // Lets handle a post
        if ($request->isMethod('POST')) {
            $resource = $request->request->all();

            if (isset($resource['sender_uri']) && !empty($resource['sender_uri'])) {
                $resource['sender'] = $resource['sender_uri'];
            }

            if (isset($resource['receiver_uri']) && !empty($resource['receiver_uri'])) {
                $resource['receiver'] = $resource['receiver_uri'];
            }

            $resource = $commonGroundService->saveResource($resource, ['component' => 'cmc', 'type' => 'contact_moments']);

            // If the contact moment was succesfully created we forward the user
            if (array_key_exists('@id', $resource)) {
                return $this->redirect($this->generateUrl('app_cmc_index'));
            }
        }

        return $variables;
    }

    /**
     * This function will kick of the suplied proces with given values.
     *
     * @Route("/{id}")
     * @Template
     */
    public function viewAction(Session $session, $id, Request $request, CommonGroundService $commonGroundService, ApplicationService $applicationService, ParameterBagInterface $params)
    {
        $variables = $applicationService->getVariables();
        $variables['resource'] = $commonGroundService->getResourceList(['component' => 'cmc', 'type' => 'contact_moments', 'id' => $id]);

        return $variables;
    }
}
