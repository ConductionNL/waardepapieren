<?php

// src/Controller/ProcessController.php

namespace App\Controller;

use Conduction\CommonGroundBundle\Service\ApplicationService;
//use App\Service\RequestService;
use Conduction\CommonGroundBundle\Service\CommonGroundService;
use Conduction\CommonGroundBundle\Service\PtcService;
use Conduction\CommonGroundBundle\Service\VrcService;
use DateTime;
use function GuzzleHttp\Promise\all;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
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
 * @Route("/ptc")
 */
class PtcController extends AbstractController
{
    /**
     * @Route("/user")
     *
     * @Template
     */
    public function userAction(Session $session, Request $request, CommonGroundService $commonGroundService, ApplicationService $applicationService, ParameterBagInterface $params, string $slug = 'home')
    {
        $variables = [];
        $variables['resources'] = $commonGroundService->getResourceList(['component'=>'ptc', 'type'=>'process_types'])['hydra:member'];

        return $variables;
    }

    /**
     * @Route("/organisation")
     * @Security("is_granted('ROLE_group.admin')")
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
        $variables['processes'] = $commonGroundService->getResourceList(['component' => 'ptc', 'type' => 'process_types'], ['order[name]' => 'asc', 'limit' => 100])['hydra:member'];

        return $variables;
    }

    /**
     * This function will kick of or run a procces without a request.
     *
     * @Route("/process/{id}")
     * @Route("/process/{id}/{stage}", name="app_ptc_process_stage")
     * @Template
     */
    public function processAction(
        Session $session,
        $id,
        $stage = false,
        Request $request,
        CommonGroundService $commonGroundService,
        VrcService $vrcService,
        PtcService $ptcService,
        ParameterBagInterface $params
    ) {
        $variables = [];
        $variables['slug'] = $stage;
        $variables['submit'] = $request->query->get('submit', 'false');

        // Lets load a request
        if ($loadrequest = $request->query->get('request')) {
            $requestUUID = $commonGroundService->getUuidFromUrl($loadrequest);
            $variables['request'] = $commonGroundService->getResource(['component'=>'vrc', 'type'=>'requests', 'id'=>$requestUUID]);
            $variables['submit'] = 'true';
            $session->set('request', $variables['request']);
        }

        $variables['process'] = $commonGroundService->getResource(['component' => 'ptc', 'type' => 'process_types', 'id' => $id]);

        if ($this->getUser()) {
            $variables['requests'] = $commonGroundService->getResourceList(['component' => 'vrc', 'type' => 'requests'], ['process_type' => $variables['process']['@id'], 'submitters.brp' => $this->getUser()->getPerson(), 'order[dateCreated]'=>'desc'])['hydra:member'];
        }

        if ($stage == 'start') {
            $session->remove('request');
        }

        $variables['request'] = $session->get('request', ['requestType'=>$variables['process']['requestType'], 'properties'=>[]]);

        // What if the request in session is defrend then the procces type that we are currently running? Or if we dont have a process_type at all? Then we create a base request
        if (
            (array_key_exists('processType', $variables['request']) && $commonGroundService->getUuidFromUrl($variables['request']['processType']) != $variables['process']['id'])
            ||
            !array_key_exists('processType', $variables['request'])
        ) {
            // Lets whipe the request
            $variables['request']['process_type'] = $variables['process']['@id'];
            $variables['request']['status'] = 'incomplete';
            $variables['request']['properties'] = [];
            $variables['request']['requestType'] = $variables['process']['requestType'];
            $session->set('request', $variables['request']);
        }

        // lets handle a current stage
        if ($stage && $stage != 'start') {
            $variables['request']['currentStage'] = $stage;
        }
        // Aditionally some one might have tried to pre-fill the form, wich we will then use overwrite the data
        $variables['request'] = array_merge($variables['request'], $request->query->all());

        if ($request->isMethod('POST')) {
            // the second argument is the value returned when the attribute doesn't exist
            $resource = $request->request->all();
            $files = $request->files->all();

            // Lets transfer the known properties
            $request = $resource['request'];
            if (array_key_exists('properties', $resource['request'])) {
                $properties = array_merge($variables['request']['properties'], $resource['request']['properties']);
                $request['properties'] = $properties;
            } elseif (array_key_exists('properties', $variables['request'])) {
                $request['properties'] = $variables['request']['properties'];
            }

            if (count($files) > 0) {
                //We are going to need a JWT token for the DRC and ZTC here

                $token = $commonGroundService->getJwtToken('ztc');
                $commonGroundService->setHeader('Authorization', 'Bearer '.$token);
                $infoObjectTypes = $commonGroundService->getResourceList(['component'=>'ztc', 'type'=>'informatieobjecttypen'])['results'];

                $informationObjectType = null;
                foreach ($infoObjectTypes as $infoObjectType) {
                    if ($infoObjectType['omschrijving'] == 'Document') {
                        $informationObjectType = $infoObjectType['url'];
                    }
                }
                if ($informationObjectType) {
                    foreach ($files['request']['properties'] as $key=>$file) {
                        $drc['informatieobjecttype'] = $informationObjectType;
                        $drc['bronorganisatie'] = '999990482';
                        $drc['titel'] = urlencode($key);
                        if ($this->getUser()) {
                            $drc['auteur'] = $this->getUser()->getPerson();
                        } else {
                            $drc['auteur'] = 'Zelf regelen applicatie';
                        }
                        $drc['creatiedatum'] = (new DateTime('now'))->format('Y-m-d');
                        $drc['bestandsnaam'] = $file->getClientOriginalName();
                        $drc['bestandstype'] = $file->getClientOriginalExtension();
                        $drc['formaat'] = $file->getClientMimeType();
                        $drc['taal'] = 'nld';
                        $drc['inhoud'] = base64_encode(file_get_contents($file->getPathname()));

                        $token = $commonGroundService->getJwtToken('drc');
                        $commonGroundService->setHeader('Authorization', 'Bearer '.$token);
                        $result = $commonGroundService->createResource($drc, ['component'=>'drc', 'type'=>'enkelvoudiginformatieobjecten']);
                        $request['properties'][$key] = $result['url'];
                        $commonGroundService->setHeader('Authorization', $this->getParameter('app_commonground_key'));
                    }
                }
            }

            // We only support the posting and saving of
            if ($this->getUser() || in_array($request['status'], ['submitted'])) {
                $request = $commonGroundService->saveResource($request, ['component' => 'vrc', 'type' => 'requests']);
            }

            // stores an attribute in the session for later reuse
            $variables['request'] = $request;

            $session->set('request', $request);
        }

        // Let load the request on the procces and validate it
        $variables['process'] = $ptcService->extendProcess($variables['process'], $variables['request']);

        // Lets make sure that we always have a stage
        if (!array_key_exists('stage', $variables) && $stage) {
            /* @todo dit is lelijk */
            foreach ($variables['process']['stages'] as $tempStage) {
                if ($tempStage['slug'] == $stage) {
                    $variables['stage'] = $tempStage;
                }
            }
        }

        if (!array_key_exists('stage', $variables)) {
            $variables['stage'] = ['next' => $variables['process']['stages'][0]];
        }

        if (key_exists('show', $variables['stage']) && !$variables['stage']['show']) {
            return $this->redirect($this->generateUrl('app_ptc_process_stage', ['id' => $id, 'stage'=>$variables['stage']['next']['slug']]));
        }

        /* lagacy */
        $variables['resource'] = $variables['request'];

        return $variables;
    }
}
