# php-wkhtmltopdf
Call API to generate PDF from HTML

## Usage
```

/*
 * Webserice provide from Docker: docker pull vdeapps/wkhtmltopdf:0.12.5
 * Map the 8137 port to 80 port of Docker
 */
$url = "http://localhost:8137/wk"; 
$pdf = new Wkhtmltopdf($this->url);

$options = $pdf->getPdfOptions();
       
       // set $options
        
$pdf->setPdfOptions($options);

// Url to make PDF
$pdf->setUrl("http://www.google.com");

// Output name file
$pdf->setNameFile('test.pdf');

// Outup directory 
$pdf->setOutput(__DIR__.'/output');

// Create file
$pdf->generate();
```

