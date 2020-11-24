<?php

// src/Controller/ZZController.php

namespace App\Controller;

use Conduction\CommonGroundBundle\Service\ApplicationService;
//use App\Service\RequestService;
use Conduction\CommonGroundBundle\Service\CommonGroundService;
use function GuzzleHttp\Promise\all;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;

/**
 * The ZZ test handles any calls that have not been picked up by another test, and wel try to handle the slug based against the wrc.
 *
 * Class ZZController
 *
 * @Route("/")
 */
class ZZController extends AbstractController
{
    /**
     * @Route("/", name="app_default_index")
     * @Route("/{slug}", requirements={"slug"=".+"}, name="slug")
     * @Template
     */
    public function indexAction(Session $session, Request $request, CommonGroundService $commonGroundService, ApplicationService $applicationService, ParameterBagInterface $params, string $slug = 'home')
    {
        $content = false;
        $variables = $applicationService->getVariables();

        // Lets provide this data to the template
        $variables['query'] = $request->query->all();
        $variables['post'] = $request->request->all();
        $variables['session'] = $session->all();

        // Lets also provide any or all id
        $slug_parts = explode('/', $slug);
        $variables['id'] = end($slug_parts);

        $variables['slug'] = $slug;

        // Lets find an appoptiate slug
        $template = $commonGroundService->getResource(['component'=>'wrc', 'type'=>'applications', 'id'=> $params->get('app_id').'/'.$slug]);

        if ($template && array_key_exists('content', $template)) {
            $content = $template['content'];
        }

        // Lets see if there is a post to procces
        if ($request->isMethod('POST')) {
            $resource = $request->request->all();
            if (array_key_exists('@component', $resource)) {
                // Passing the variables to the resource
                $resource = $commonGroundService->saveResource($resource, ['component' => $resource['@component'], 'type' => $resource['@type']]);
            }
        }

        // Create the template
        if ($content) {
            $template = $this->get('twig')->createTemplate($content);
            $template = $template->render($variables);
        } else {
            $template = $this->render('404.html.twig', $variables);

            return $template;
        }

        return $response = new Response(
            $template,
            Response::HTTP_OK,
            ['content-type' => 'text/html']
        );
    }
}
