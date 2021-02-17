<?php

// src/Controller/DefaultController.php

namespace App\Controller;

use Conduction\CommonGroundBundle\Service\ApplicationService;
use Conduction\CommonGroundBundle\Service\CommonGroundService;
use Endroid\QrCode\Factory\QrCodeFactoryInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * The Procces test handles any calls that have not been picked up by another test, and wel try to handle the slug based against the wrc.
 *
 * Class WaardepapierenController
 *
 * @Route("/waardepapieren")
 */
class WaardepapierenController extends AbstractController
{
    /**
     * @Route("/certificate")
     * @Template
     */
    public function certificateAction(CommonGroundService $commonGroundService, Request $request, ParameterBagInterface $params)
    {
        $variables = [];

        // Handle post
        if ($request->isMethod('POST')) {
            $certificate = $request->request->all();

            $variables['certificate'] = $request->request->all();
            $variables['certificate'] = $commonGroundService->createResource($variables['certificate'], 'https://waardepapieren-gemeentehoorn.commonground.nu/api/v1/waar/certificates');
        }

        return $variables;
    }

    /**
     * This function will prompt a downloaden for the qr code.
     *
     * It provides the following optional query parameters
     * size: the size of the image renderd, default  300
     * margin: the maring on the image in pixels, default 10
     * file: the file type renderd, default png
     * encoding: the encoding used for the file, default: UTF-8
     *
     * @Route("/download/{id}")
     */
    public function downloadAction(Session $session, $id, $type = 'png', Request $request, FlashBagInterface $flash, CommonGroundService $commonGroundService, ApplicationService $applicationService, ParameterBagInterface $params, QrCodeFactoryInterface $qrCodeFactory)
    {
        $splits = explode('.', $id);
        $id = $splits[0];
        $extention = $splits[1];
        $certificate = $commonGroundService->getResource(['component' => 'waar', 'type' => 'certificates', 'id'=>$id]);

//        $url = $this->generateUrl('app_chin_checkin', ['code'=>$node['reference']], UrlGeneratorInterface::ABSOLUTE_URL);
//
//        $configuration = $node['qrConfig'];
//        if ($request->query->get('size')) {
//            $configuration['size'] = $request->query->get('size', 300);
//        }
//        if ($request->query->get('margin')) {
//            $configuration['margin'] = $request->query->get('margin', 10);
//        }
//
//        $qrCode = $qrCodeFactory->create($url, $configuration);
//
//        // Set advanced options
//        $qrCode->setWriterByName($request->query->get('file', $extention));
//        $qrCode->setEncoding($request->query->get('encoding', 'UTF-8'));
//        //$qrCode->setErrorCorrectionLevel(ErrorCorrectionLevel::HIGH());
//
//        $filename = 'qr-code.'.$extention;
//
//        $response = new Response($qrCode->writeString());
//        // Create the disposition of the file
//        $disposition = $response->headers->makeDisposition(
//            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
//            $filename
//        );
//
//        // Set the content disposition
//        $response->headers->set('Content-Disposition', $disposition);
//
//        return $response;
    }
}
