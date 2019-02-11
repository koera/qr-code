<?php

namespace AppBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;
use Endroid\QrCode\QrCode;
use Dompdf\Dompdf;
use Dompdf\Options;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction()
    {
        // Create a basic QR code
        $qrCode = new QrCode('Life is too short to be generating QR codes');
        $qrCode->setSize(300);
        // Set advanced options
        $qrCode->setWriterByName('png');
        $qrCode->setMargin(10);
        $qrCode->setEncoding('UTF-8');
        $qrCodeName = time();
        // Save it to a file
        $qrCode->writeFile($this->get('kernel')->getRootDir().'/../web/QRCODE/'. $qrCodeName .'.png');
        

        // Configure Dompdf according to your needs
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');
        
        // Instantiate Dompdf with our options
        $dompdf = new Dompdf($pdfOptions);

        $dompdf->set_option('isHtml5ParserEnabled', true);
        
        // Retrieve the HTML generated in our twig file
        $html = $this->renderView('default/mypdf.html.twig', [
            "qrCodePath" => "QRCODE/".$qrCodeName.".png"
        ]);
        
        // Load HTML to Dompdf
        $dompdf->loadHtml($html);
        
        // (Optional) Setup the paper size and orientation 'portrait' or 'portrait'
        $dompdf->setPaper('A4', 'portrait');

        // Render the HTML as PDF
        $dompdf->render();

        // Store PDF Binary Data
        $output = $dompdf->output();
        
        // In this case, we want to write the file in the public directory
        $pdfDir = $this->get('kernel')->getRootDir() . '/../web/PDF';
        // e.g /var/www/project/public/mypdf.pdf
        $pdfFilepath =  $pdfDir . '/mypdf.pdf';
        
        // Write file to the desired path
        file_put_contents($pdfFilepath, $output);

        return $this->render('default/index.html.twig', [

        ]);
    }
}