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
    public function certificateAction(CommonGroundService $commonGroundService, Request $request, ParameterBagInterface $params) {
        $variables = [];

        // Handle post
        if ($request->isMethod('POST')) {
            $certificate = $request->request->all();

            // Set the certificate person
            $certificate['person'] = '';//$this->getUser()->getPerson();

            $certificate = $commonGroundService->createResource($certificate, ['component' => 'waar', 'type' => 'certificates']);

            // return to a page with download buttons
            $variables['certificate'] = $certificate;
        }

        return $variables;
    }
}
