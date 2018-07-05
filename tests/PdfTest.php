<?php

namespace Tests\vdeApps\Htmltopdf;

use PHPUnit\Framework\TestCase;
use vdeApps\Htmltopdf\Wkhtmltopdf;

class PdfTest extends TestCase {

    public $url = "localhost:8137/wk";
    
    public function testPdf(){
        
        $pdf = new Wkhtmltopdf($this->url);
        
        $options = $pdf->getPdfOptions();
        
        $pdf->setPdfOptions($options);
        
        $pdf->setUrl("http://www.google.com");
    
        $pdf->setNameFile('test.pdf');
        $pdf->setOutput(__DIR__.'/output');
        
        $pdf->generate();
    }
    
    
}
