<?php

namespace App\Controller;

use Conduction\CommonGroundBundle\Service\CommonGroundService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route('/claims')
 */
class ClaimController extends AbstractController
{
    /**
     * @Route("/public_keys/{rsin}")
     */
    public function publicKeyAction(CommonGroundService $commonGroundService, string $rsin): Response
    {
        $key = $commonGroundService->getResourceList(['component' => 'waar', 'type' => 'metadata/public_keys', 'id' => $rsin])['key'];

        return new Response(
            $key,
            Response::HTTP_OK,
            ['Content-Type' => 'application/x-pem-file']
        );
    }
}
