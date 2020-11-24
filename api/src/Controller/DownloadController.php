<?php

// src/Controller/OrcController.php

namespace App\Controller;

use Conduction\CommonGroundBundle\Service\ApplicationService;
//use App\Service\RequestService;
use Conduction\CommonGroundBundle\Service\CommonGroundService;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\Settings;
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
 * @Route("/download")
 */
class DownloadController extends AbstractController
{
    /**
     * @Route("/order/{id}")
     */
    public function orderAction($id, Session $session, Request $request, CommonGroundService $commonGroundService, ApplicationService $applicationService, ParameterBagInterface $params, string $slug = 'home')
    {
        $application = $commonGroundService->getResource(['component' => 'wrc', 'type' => 'applications', 'id' => getenv('APP_ID')]);
        $order = $commonGroundService->getResource(['component' => 'orc', 'type' => 'orders', 'id' => $id]);
        $orderTemplate = $commonGroundService->getResource($application['defaultConfiguration']['configuration']['orderTemplate']);
        $query = ['resource' => $order['@id']];
        $render = $commonGroundService->createResource($query, $orderTemplate['@id'].'/render');
        $phpWord = new PhpWord();
        $section = $phpWord->addSection();
        \PhpOffice\PhpWord\Shared\Html::addHtml($section, $render['content']);
        $objWriter = IOFactory::createWriter($phpWord, 'Word2007');
        $filenameDocx = dirname(__FILE__, 3)."/var/{$order['reference']}.docx";
        $objWriter->save($filenameDocx);
        $phpWord = \PhpOffice\PhpWord\IOFactory::load($filenameDocx);
        $rendererName = Settings::PDF_RENDERER_DOMPDF;
        $rendererLibraryPath = realpath('../vendor/dompdf/dompdf');
        Settings::setPdfRenderer($rendererName, $rendererLibraryPath);
        $xmlWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'PDF');
        $filename = dirname(__FILE__, 3)."/var/{$order['reference']}.pdf";
        $xmlWriter->save($filename);
        header('Content-Disposition: attachment; filename='.$order['reference'].'.pdf');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        flush();
        readfile($filename);
        unlink($filename); // deletes the temporary file
        unlink($filenameDocx);
        exit;
    }

    /**
     * @Route("/invoice/{id}")
     */
    public function invoiceAction($id, Session $session, Request $request, CommonGroundService $commonGroundService, ApplicationService $applicationService, ParameterBagInterface $params, string $slug = 'home')
    {
        $application = $commonGroundService->getResource(['component' => 'wrc', 'type' => 'applications', 'id' => getenv('APP_ID')]);
        $order = $commonGroundService->getResource(['component' => 'bc', 'type' => 'invoices', 'id' => $id]);
        $orderTemplate = $commonGroundService->getResource($application['defaultConfiguration']['configuration']['invoiceTemplate']);
        $query = ['resource' => $order['@id']];
        $render = $commonGroundService->createResource($query, $orderTemplate['@id'].'/render');
        $phpWord = new PhpWord();
        $section = $phpWord->addSection();
        \PhpOffice\PhpWord\Shared\Html::addHtml($section, $render['content']);
        $objWriter = IOFactory::createWriter($phpWord, 'Word2007');
        $filenameDocx = dirname(__FILE__, 3)."/var/{$order['name']}.docx";
        $objWriter->save($filenameDocx);
        $phpWord = \PhpOffice\PhpWord\IOFactory::load($filenameDocx);
        $rendererName = Settings::PDF_RENDERER_DOMPDF;
        $rendererLibraryPath = realpath('../vendor/dompdf/dompdf');
        Settings::setPdfRenderer($rendererName, $rendererLibraryPath);
        $xmlWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'PDF');
        $filename = dirname(__FILE__, 3)."/var/{$order['reference']}.pdf";
        $xmlWriter->save($filename);
        header('Content-Disposition: attachment; filename='.$order['name'].'.pdf');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        flush();
        readfile($filename);
        unlink($filename); // deletes the temporary file
        unlink($filenameDocx); // deletes the temporary file
        exit;
    }
}
