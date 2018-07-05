<?php
/**
 * Copyright AUXITEC TECHNOLOGIES (groupe Artélia)
 */

namespace vdeApps\Htmltopdf;

use Knp\Snappy\Pdf;

class Wkhtmltopdf {
    
    /** @var Pdf $snappy */
    private $snappy;
    /** @var \GuzzleHttp\Client $client */
    private $client;
    /** @var array $options */
    private $pdfOptions = [
        'method' => 'GET',
    ];
    /** @var array $publicUrlQueries */
    private $publicUrlQueries = [];
    private $publicUrl;
    
    private $apiQueries = [
        'url'          => null,
        'query_params' => null,
        'wk_params'    => null,
    ];
    
    private $nameFile = 'export.pdf';
    
    private $apiUrl;
    private $apiMethod = 'GET';
    private $output;
    
    /**
     * HtmlToPdf constructor.
     *
     * @param       $apiUrl
     * @param array $pdfOptions
     *
     * @throws Exception
     */
    public function __construct($apiUrl, $pdfOptions = []) {
        $this->pdfOptions = array_merge($this->pdfOptions, $pdfOptions);
        $this->snappy = new Pdf('');
        $this->setApiUrl($apiUrl);
    }
    
    public function setUrl($publicUrl) {
        $this->publicUrl = $publicUrl;
        
        $this->apiQueries['url'] = $publicUrl;
        
        return $this;
    }
    
    /**
     * Url queries
     *
     * @param array $queries
     *
     * @return $this
     */
    public function setQueryUrl($queries = []) {
        $this->publicUrlQueries = $queries;
        
        $this->apiQueries['query_params'] = $queries;
        
        return $this;
    }
    
    /**
     * @return mixed|string
     */
    public function getLink() {
        return $this->getApiUrl() . '?' . http_build_query($this->apiQueries);
        
        return $this->getUrl();
    }
    
    /**
     * @return mixed
     */
    public function getApiUrl() {
        return $this->apiUrl;
    }
    
    /**
     * @param mixed $apiUrl
     *
     * @return Wkhtmltopdf
     * @throws Exception
     */
    public function setApiUrl($apiUrl) {
        
        if (empty($apiUrl)) {
            throw new Exception("Export PDF désactivé", 10);
        }
        
        $this->apiUrl = $apiUrl;
        
        return $this;
    }
    
    /**
     * @return mixed
     */
    public function getUrl() {
        return $this->publicUrl;
    }
    
    /**
     * @param ResponseInterface $response
     */
    public function getHtmlHeader(\Psr\Http\Message\ResponseInterface &$response) {
        $response->withHeader('Content-type', 'application/pdf')
                 ->withHeader('Content-Disposition', "inline;filename='" . basename($this->getNameFile()) . "'");
    }
    
    /**
     * @return string
     */
    public function getNameFile() {
        return $this->nameFile;
    }
    
    /**
     * @param string $nameFile
     */
    public function setNameFile($nameFile) {
        $this->nameFile = $nameFile;
    }
    
    /**
     * Retour du contenu
     *
     * @param boolean $inline Affichage du SaveAs ou en Inline
     *
     * @return bool
     * @throws \Exception
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function generate($inline = false) {
        try {
            $this->client = new \GuzzleHttp\Client([
                'base_uri' => $this->getApiUrl(),
            ]);
            
            $httpResponse = $this->client->request($this->getApiMethod(), '', [
                'allow_redirects' => false,
                'query'           => $this->getApiQueries(),
            ]);
            
            $output = $this->getOutput();
            if (is_string($output)) {
                $outputfile = $output . '/' . $this->getNameFile();
                /** @var string $outputt */
                if (false === file_put_contents($outputfile, $httpResponse->getBody())) {
                    throw new \Exception("Erreur d'écriture du fichier: $outputfile", 10);
                }
                
                return $outputfile;
            }
            elseif (is_a($output, \Psr\Http\Message\ResponseInterface::class)) {
                /** @var \Psr\Http\Message\ResponseInterface $output */
                
                $resp = $httpResponse
                    ->withoutHeader('Content-Disposition')
                    ->withHeader('Content-type', 'application/pdf')
                    ->withHeader('Content-Description', 'File Transfer')
                    ->withHeader('Content-Transfer-Encoding', 'binary')
                    ->withHeader('Expires', '0')
                    ->withHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0')
                    ->withHeader('Content-Length', $httpResponse->getBody()->getSize())
                    ->withHeader('Content-Disposition', (($inline) ? 'inline' : 'attachment') . ';filename="' . basename($this->getNameFile()) . '"');
                
                return $resp;
            }
            else {
                throw new \Exception("Unknown output", 10);
            }
            
        }
        catch (\Exception $ex) {
            throw $ex;
        }
        
        return false;
    }
    
    /**
     * @return string
     */
    public function getApiMethod() {
        return $this->apiMethod;
    }
    
    /**
     * @param string $apiMethod
     */
    public function setApiMethod($apiMethod) {
        $this->apiMethod = $apiMethod;
    }
    
    /**
     * @return mixed
     */
    public function getOutput() {
        return $this->output;
    }
    
    /**
     * @param \Psr\Http\Message\ResponseInterface | string $output Response or Directory
     *
     * @return $this
     */
    public function setOutput($output) {
        $this->output = $output;
        
        return $this;
    }
    
    /**
     * @return array
     */
    public function getQueryUrl() {
        return $this->publicUrlQueries;
    }
    
    /**
     * Liste des options wkhtmltopdf
     * @return array
     */
    public function getPdfOptions() {
        return $this->snappy->getOptions();
    }
    
    /**
     * @param array $options
     *
     * @return Wkhtmltopdf
     */
    public function setPdfOptions($options) {
        $this->snappy->setOptions($options);
        
        $pdfParams = $this->snappy->getCommand('', '');
        $pdfParams = str_replace("''", '', $pdfParams);
        
        $this->apiQueries['wk_params'] = $pdfParams;
        
        return $this;
    }
    
    /**
     * @return array
     */
    protected function getApiQueries() {
        return $this->apiQueries;
    }
    
    /**
     * @param array $apiQueries
     *
     * @return Wkhtmltopdf
     */
    protected function setApiQueries($apiQueries) {
        $this->apiQueries = $apiQueries;
        
        return $this;
    }
}
