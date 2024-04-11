<?php

if (!class_exists('IssuuServiceAPI'))
{
    require(dirname(__FILE__) . '/class.issuuserviceapi.php');
}

/**
*   Classe IssuuDocument
*
*   @author Pedro Marcelo de Sá Alves
*   @link https://github.com/pedromarcelojava/
*   @version 1.2
*/
class IssuuDocument extends IssuuServiceAPI
{
    /**
    *   Método de exclusão da seção Document
    *
    *   @access protected
    *   @var string
    */
    protected $delete = 'issuu.document.delete';

    /**
    *   Slug da seção
    *
    *   @access protected
    *   @var string
    */
    protected $slug_section = 'results';

    /**
    *   IssuuDocument::upload()
    *
    *   Relacionado ao método issuu.document.upload da API.
    *   Carrega um arquivo para a conta.
    *
    *   @access public
    *   @param array $params Correspondente aos parâmetros da requisição
    *   @return array Retorna um array com a resposta da requisição
    */
    public function upload($params = array())
    {
        if (!isset($_FILES['file']) || empty($_FILES['file']))
        {
            header($_SERVER['SERVER_PROTOCOL'] . ' 500 Internal Server Error');
            header('Content-Type: text/plain');
            die('This form is not multipart/form-data');
        }

        $params['action'] = 'issuu.document.upload';

        foreach ($params as $key => $value) {
            if (isset($value) && ($value == '' || is_null($value)))
            {
                unset($params[$key]);
            }
        }

        $this->setParams($params);
        $this->setFile($_FILES['file']);
        $response = $this->curlRequest(
            $this->getUploadUrl($params['slug']),
            $this->params,
            array(),
            false
        );

        $slug = $this->slug_section;

        if (isset($params['format']) && $params['format'] == 'json')
        {
            $response = json_decode($response);
            $response = $response->rsp;

            if($response->stat == 'ok')
            {
                $result['stat'] = 'ok';
                $result[$slug] = $this->clearObjectJson($response->_content->$slug);

                return $result;
            }
            else
            {
                return $this->returnErrorJson($response);
            }
        }
        else
        {
            $response = new SimpleXMLElement($response);

            if ($response['stat'] == 'ok')
            {
                $result['stat'] = 'ok';
                $result[$slug] = $this->clearObjectXML($response->$slug);

                return $result;
            }
            else
            {
                return $this->returnErrorXML($response);
            }
        }
    }

    /**
    *   IssuuDocument::urlUpload()
    *
    *   Relacionado ao método issuu.document.url_upload da API.
    *   Carrega um arquivo para a conta através de uma URL informada.
    *
    *   @access public
    *   @param array $params Correspondente aos parâmetros da requisição
    *   @return array Retorna um array com a resposta da requisição
    */
    public function urlUpload($params = array())
    {
        $params['action'] = 'issuu.document.url_upload';

        return $this->returnSingleResult($params);
    }

    /**
    *   IssuuDocument::update()
    *
    *   Relacionado ao método issuu.document.update da API.
    *   Atualiza os dados de um determinado documento.
    *
    *   @access public
    *   @param array $params Correspondente aos parâmetros da requisição
    *   @return array Retorna um array com a resposta da requisição
    */
    public function update($params = array())
    {
        return array('stat' => 'error', 'message' => 'Method not implemented');
    }

    /**
     *  IssuuDocument::getUpdateData()
     *  
     */
    public function getUpdateData($params = array())
    {
        return $this->returnSingleResult($params);
    }

    /**
    *   IssuuDocument::clearObjectXML()
    *
    *   Valida e formata os atributos do objeto do documento.
    *
    *   @access protected
    *   @param object $document Correspondente ao objeto do documento
    *   @return object Retorna um novo objeto do documento devidamente validado
    */
    protected function clearObjectXML($document)
    {
        $doc = new stdClass();
        echo json_encode($document);

        $doc->username = $this->validFieldXML($document, 'username');
        $doc->name = $this->validFieldXML($document, 'name');
        $doc->documentId = $this->validFieldXML($document, 'documentId');
        $doc->title = $this->validFieldXML($document, 'title');
        $doc->access = $this->validFieldXML($document, 'access');
        $doc->state = $this->validFieldXML($document, 'state');
        $doc->errorCode = $this->validFieldXML($document, 'errorCode');
        $doc->preview = $this->validFieldXML($document, 'preview', 2);
        $doc->category = $this->validFieldXML($document, 'category');
        $doc->type = $this->validFieldXML($document, 'type');

        $doc->orgDocType = $this->validFieldXML($document, 'orgDocType');
        $doc->orgDocName = $this->validFieldXML($document, 'orgDocName');
        $doc->downloadable = $this->validFieldXML($document, 'downloadable', 2);
        $doc->origin = $this->validFieldXML($document, 'origin');
        $doc->language = $this->validFieldXML($document, 'language');
        $doc->rating = $this->validFieldXML($document, 'rating');
        $doc->ratingsAllowed = $this->validFieldXML($document, 'ratingsAllowed', 2);
        $doc->ratingDist = $this->validFieldXML($document, 'ratingDist');
        $doc->commentsAllowed = $this->validFieldXML($document, 'commentsAllowed', 2);
        $doc->showDetectedLinks = $this->validFieldXML($document, 'showDetectedLinks', 2);

        $doc->pageCount = $this->validFieldXML($document, 'pageCount');
        $doc->dcla = $this->validFieldXML($document, 'dcla');
        $doc->ep = $this->validFieldXML($document, 'ep');
        $doc->publicationCreationTime = $this->validFieldXML($document, 'publicationCreationTime');
        $doc->publishDate = $this->validFieldXML($document, 'publishDate');
        $doc->publicOnIssuuTime = $this->validFieldXML($document, 'publicOnIssuuTime');
        $doc->description = $this->validFieldXML($document, 'description');
        $doc->coverWidth = $this->validFieldXML($document, 'coverWidth', 1);
        $doc->coverHeight = $this->validFieldXML($document, 'coverHeight', 1);

        if (isset($document->tags))
        {
            $doc->tags = array();

            foreach ($document->tags->tag as $tag) {
                $doc->tags[] = utf8_decode($tag['value']);
            }
        }

        if (isset($document->folders))
        {
            $doc->folders = array();

            foreach ($document->folders->folder as $folder) {
                $doc->folders[] = (string) $folder['id'];
            }
        }

        return $doc;
    }

    /**
    *   IssuuDocument::clearObjectJson()
    *
    *   Valida e formata os atributos do objeto do documento.
    *
    *   @access protected
    *   @param object $document Correspondente ao objeto do documento
    *   @return object Retorna um novo objeto do documento devidamente validado
    */
    protected function clearObjectJson($document)
    {
        $doc = (object) $document;

        if(isset($doc->cover['small'])) {
            $doc->coverImage = $doc->cover['small']['url'];
        }
        if(isset($doc->cover['medium'])) {
            $doc->coverImage = $doc->cover['medium']['url'];
        }
        if(isset($doc->cover['large'])) {
            $doc->coverImage = $doc->cover['large']['url'];
        }

        return $doc;
    }

    private function setFile($file)
    {
        if (version_compare(PHP_VERSION, '5.5', '>='))
        {
            $this->params['file'] = new CURLFile(
                $file['tmp_name'],
                $file['type'],
                $file['name']
            );
        }
        else
        {
            $this->params['file'] = '@' . $file['tmp_name'];
        }
    }
 
}