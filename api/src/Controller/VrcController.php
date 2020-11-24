<?php

// src/Controller/ZZController.php

namespace App\Controller;

use Conduction\CommonGroundBundle\Service\ApplicationService;
//use App\Service\RequestService;
use Conduction\CommonGroundBundle\Service\CommonGroundService;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\Settings;
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
 * @Route("/request")
 */
class VrcController extends AbstractController
{
    /**
     * @Route("/user")
     * @Template
     */
    public function userAction(Session $session, Request $request, CommonGroundService $commonGroundService, ApplicationService $applicationService, ParameterBagInterface $params, string $slug = 'home')
    {
        $variables = [];
        $variables['resources'] = $commonGroundService->getResourceList(['component'=>'vrc', 'type'=>'requests'], ['submitters.brp'=>$this->getUser()->getPerson()])['hydra:member'];

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
     * @Route("/")
     * @Template
     */
    public function indexAction(Session $session, Request $request, CommonGroundService $commonGroundService, ApplicationService $applicationService, ParameterBagInterface $params, string $slug = 'home')
    {
        $variables = $applicationService->getVariables();
        $variables['requests'] = $commonGroundService->getResourceList(['component'=>'vrc', 'type'=>'requests'], ['submitters.brp'=>$variables['user']['@id']])['hydra:member'];
//        var_dump($variables['requests']);

        // Lets provide this data to the template
        return $variables;
    }

    /**
     * @Route("/download/{id}/{requestId}")
     */
    public function DownloadAction(Request $request, CommonGroundService $commonGroundService, $id, $requestId)
    {
        $document = $commonGroundService->getResource(['component' => 'vtc', 'type' => 'templates', 'id' => $id]);
        $currentRequest = $commonGroundService->getResource(['component' => 'vrc', 'type' => 'requests', 'id' => $requestId]);
        $query = ['request' => $currentRequest['@id']];
        $render = $commonGroundService->createResource($query, $document['uri'].'/render');
        switch ($document['type']) {
            case 'word':
                $phpWord = new PhpWord();
                $section = $phpWord->addSection();
                \PhpOffice\PhpWord\Shared\Html::addHtml($section, $render['content']);
                $objWriter = IOFactory::createWriter($phpWord, 'Word2007');
                $filename = dirname(__FILE__, 3)."/var/{$document['name']}.docx";
                $objWriter->save($filename);
                header('Content-Type: application/vnd.ms-word');
                header('Content-Disposition: attachment; filename='.$document['name'].'.docx');
                header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
                flush();
                readfile($filename);
                unlink($filename); // deletes the temporary file
                exit;
            case 'pdf':
                $phpWord = new PhpWord();
                $section = $phpWord->addSection();
                \PhpOffice\PhpWord\Shared\Html::addHtml($section, $render['content']);
                $objWriter = IOFactory::createWriter($phpWord, 'Word2007');
                $filenameDocx = dirname(__FILE__, 3)."/var/{$document['name']}.docx";
                $objWriter->save($filenameDocx);
                $phpWord = \PhpOffice\PhpWord\IOFactory::load($filenameDocx);
                $rendererName = Settings::PDF_RENDERER_DOMPDF;
                $rendererLibraryPath = realpath('../vendor/dompdf/dompdf');
                Settings::setPdfRenderer($rendererName, $rendererLibraryPath);
                $xmlWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'PDF');
                $filename = dirname(__FILE__, 3)."/var/{$document['name']}.pdf";
                $xmlWriter->save($filename);
                header('Content-Disposition: attachment; filename='.$document['name'].'.pdf');
                header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
                flush();
                readfile($filename);
                unlink($filename); // deletes the temporary file
                unlink($filenameDocx); // deletes the temporary file
                exit;
        }
    }
}
