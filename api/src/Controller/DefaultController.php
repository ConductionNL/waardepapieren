<?php

// src/Controller/DefaultController.php

namespace App\Controller;

use Conduction\CommonGroundBundle\Service\CommonGroundService;
use Ramsey\Uuid\Uuid;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\UrlHelper;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\OgoneService;

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
            'name' => 'Akte van geboorte',
            'type' => 'akte_van_geboorte',
            'price' => '14',
        ];
//        $variables['types'][] = [
//            'name'=> 'Akte van overlijden',
//            'type'=> 'akte_van_overlijden',
//        ];
        $variables['types'][] = [
            'name' => 'Verklaring van in leven zijn',
            'type' => 'verklaring_van_in_leven_zijn',
            'price' => '14',
        ];
//        $variables['types'][] = [
//            'name'=> 'Verklaring van Nederlanderschap',
//            'type'=> 'verklaring_van_nederlanderschap',
//        ];
        $variables['types'][] = [
            'name' => 'Uittreksel basis registratie personen',
            'type' => 'uittreksel_basis_registratie_personen',
            'price' => '14',
        ];
//        $variables['types'][] = [
//            'name'=> 'Historisch uittreksel basis registratie personen',
//            'type'=> 'historisch_uittreksel_basis_registratie_personen',
//        ];

        if ($this->getUser() && $this->getUser()->getPerson()) {
            $variables['certificates'] = $commonGroundService->getResourceList(['component' => 'wari', 'type' => 'certificates'], ['person' => $this->getUser()->getPerson()])['hydra:member'];
//            $variables['certificates'][] = array('type' => 'geboorte akte', 'created' => '17-09-2020', 'id' => '1');
        }

        return $variables;
    }

    /**
     * @Route("/pay-certificate")
     * @Template
     */
    public function payCertificateAction(
        CommonGroundService $commonGroundService,
        Request $request,
        ParameterBagInterface $params,
        UrlHelper $urlHelper,
        Session $session,
        OgoneService $ogoneService
    )
    {
        $variables = [];

        $shaSignature = $params->get('app_shasign');

//        if (isset($shaSignature) && $request->query->get('orderID') && $request->query->get('PAYID') && $request->query->get('SHASIGN') && $this->getUser()) {
        if ($ogoneService->canWeHash([$shaSignature,  $request->query->get('orderID'), $request->query->get('PAYID'), $request->query->get('SHASIGN'), $this->getUser()]) == true) {


            $variables['paramsArray'] = $request->query->all();
            $variables['hashResult'] = $ogoneService->verifyReturnedHash($variables['paramsArray'], $shaSignature);


            $receivedOrderId = $request->query->get('orderID');
            $orderId = $session->get('orderId');
            $type = $session->get('type');

            $paymentStatus = $ogoneService->checkPaymentStatus($variables['paramsArray'], $orderId, $receivedOrderId, [$type]);

            if ($paymentStatus == 'valid') {
                // Create and get certificates
                $variables['certificate']['type'] = $session->get('type');
                $variables['certificate']['organization'] = '001516814';
                $variables['certificate']['person'] = $this->getUser()->getPerson();
                $variables['certificate'] = $commonGroundService->createResource($variables['certificate'], ['component' => 'waar', 'type' => 'certificates']);
                $variables['certificate']['claim'] = base64_encode(json_encode($variables['certificate']['claim']));

                $variables['certificates'] = $commonGroundService->getResourceList(['component' => 'wari', 'type' => 'certificates'], ['person' => $this->getUser()->getPerson()])['hydra:member'];
            } else {
                // Unset order session vars
                $session->set('type', null);
                $session->set('orderId', null);
            }

//            if (isset($variables['paramsArray']['STATUS']) && ($variables['paramsArray']['STATUS'] == '5' ||
//                    $variables['paramsArray']['STATUS'] == '9' || $variables['paramsArray']['STATUS'] == '51' ||
//                    $variables['paramsArray']['STATUS'] == '91') && isset($orderId) && isset($receivedOrderId) && $orderId == $receivedOrderId) {
//
////                Create certificate if type is in session
//                if ($session->get('type')) {
//                    $variables['certificate']['type'] = $session->get('type');
//                    $variables['certificate']['organization'] = '001516814';
//                    $variables['certificate']['person'] = $this->getUser()->getPerson();
//                    $variables['certificate'] = $commonGroundService->createResource($variables['certificate'], ['component' => 'waar', 'type' => 'certificates']);
//                    $variables['certificate']['claim'] = base64_encode(json_encode($variables['certificate']['claim']));
//
//                    $variables['certificates'] = $commonGroundService->getResourceList(['component' => 'wari', 'type' => 'certificates'], ['person' => $this->getUser()->getPerson()])['hydra:member'];
//                }
//            } elseif (isset($variables['paramsArray']['STATUS'])) {
//                $session->set('type', null);
//                $session->set('orderId', null);
//            }

        } elseif (isset($shaSignature) && $request->isMethod('POST') && $this->getUser()) {
            $variables['values'] = $request->request->all();
            $typeinfo = json_decode($variables['values']['typeinfo']);

            $orderId = (string)Uuid::uuid4();

            $variables['paymentArray'] = [
                'PSPID' => 'gemhoorn',
                'orderid' => $orderId,
                'amount' => $typeinfo->price * 100,
                'currency' => 'EUR',
                'language' => 'nl_NL',
                'CN' => $this->getUser()->getName(),
                'TITLE' => 'Certificate',
                'BGCOLOR' => 'white',
                'TXTCOLOR' => 'black',
                'TBLBGCOLOR' => 'white',
                'TBLTXTCOLOR' => 'black',
                'BUTTONBGCOLOR' => 'white',
                'BUTTONTXTCOLOR' => 'black',
                'FONTTYPE' => 'Verdana',
                'ACCEPTURL' => $urlHelper->getAbsoluteUrl('/pay-certificate'),
                'EXCEPTIONURL' => $urlHelper->getAbsoluteUrl('/pay-certificate'),
                'DECLINEURL' => $urlHelper->getAbsoluteUrl('/pay-certificate'),
                'CANCELURL' => $urlHelper->getAbsoluteUrl('/pay-certificate'),
            ];


            $variables['paymentArray']['SHASign'] = $ogoneService->createShaSignature($variables['paymentArray'], $shaSignature);
            $variables['status'] = 'test';

            if (isset($typeinfo)) {
                $session->set('type', $typeinfo->type);
                $session->set('orderId', $orderId);
            }
        } else {
            return $this->redirectToRoute('app_default_index');
        }

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
