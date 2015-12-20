<?php

/**
 * Manager class of a watson API client.
 */
class Manager_Watson_Client extends Manager_Abstract {

    /**
     * The client to do the real http request.
     *
     * @var Zend_Http_Client
     */
    protected $_httpClient = null;

    /**
     * The only available instance of Manager_Watson_Client.
     *
     * @var Manager_Watson_Client
     */
    protected static $_instance;

    /**
     * Returns an instance.
     *
     * Singleton pattern implementation.
     *
     * @return Manager_Watson_Client
     */
    public static function getInstance() {
        if (null === self::$_instance) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

    /**
     * Initialize some of the basic properties.
     *
     * @return void
     */
    protected function init() {
        $config = Zend_Registry::getInstance()->config->watson;
        $client = new Zend_Http_Client();
        $client->setUri($config->baseUrl);
        $client->setAuth($config->username, $config->password, Zend_Http_Client::AUTH_BASIC);

        // Extra headers needed
        $client->setHeaders(array(
            'X-SyncTimeout' => $config->timeout,
            'Content-Type' => 'application/json',
            'Accept' => 'application/json'
        ));

        $this->_httpClient = $client;
    }

    /**
     * Ask a question.
     *
     * @param string $question
     * @return array | false
     */
    public function qaApi($question, $items = 5, $kv = array()) {

        $temp = array();

        $newarray = array();
        foreach ($kv as &$value) {
            foreach ($value as $k => $v) {
                if (strpos($v, ",") === FALSE) {
                    $temp[] = array("filterType" => "metadataFilter",
                        "fieldName" => "indexedKey." . $k,
                        "values" => array($v));
                } else {
                    $theItems = explode(",", $v);
                    foreach ($theItems as &$ti) {
                        $newarray[] = $ti;
                    }
                    $temp[] = array("filterType" => "metadataFilter",
                        "fieldName" => "indexedKey." . $k,
                        "values" => $newarray);
                }
            }
        }


        $payload = array(
            'question' => array(
                'questionText' => $question,
                'evidenceRequest' => array(
                    'items' => -1,
                    'profile' => 'YES'
                ),
                'items' => $items,
                'formattedAnswer' => true,
                'formattedText' => true
            )
        );
        if (!empty($temp)) {
            $payload['question']['filters'] = $temp;
        }
        $logger = Zend_Registry::get("logger");
        $logger->log(Zend_Json::encode($payload),2);
        $response = $this->_httpClient->setRawData(Zend_Json::encode($payload))->request(Zend_Http_Client::POST);
      //  $logger->log(print_r($response,true),2);
        if ($response->getStatus() != 200) {
            // Log error and return false
            return false;
        }

        // Parse the data
        $responseBody = Zend_Json::decode($response->getBody());
        return $responseBody;
    }

    /*
     * Gets a list of ID's from watson
     *
     * @param String $from
     */

    public function getAllDocuments($from) {
        $config = Zend_Registry::getInstance()->config->watson;
        $this->_httpClient->setUri($config->allDocumentUrl);
        $payload = array();
        $this->_httpClient->setHeaders(array(
            'X-SyncTimeout' => $config->timeout,
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            'Accept-Encoding'=>'',
            'Range' => 'items=' . (int) $from . '-' . ((int) $from + 10)
        ));
        $response = $this->_httpClient->request(Zend_Http_Client::GET);

        $responseBody = Zend_Json::decode($response->getBody());
        return $responseBody;
    }

    public function getAllDocumentsNoRange($from) {
        $config = Zend_Registry::getInstance()->config->watson;
        $this->_httpClient->setUri($config->allDocumentUrl);
        $payload = array();
        $this->_httpClient->setHeaders(array(
            'X-SyncTimeout' => $config->timeout,
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            'Range' => 'items=' . (int) $from . '-' . ((int) $from + 100)
        ));
        $response = $this->_httpClient->request(Zend_Http_Client::GET);
        $responseBody = Zend_Json::decode($response->getBody());
        return $responseBody;
    }

    /*
     * Gets a specific document from watson based on ID
     *
     * @param Integer $docId
     */

    public function getWatsonDocument($id) {
        $config = Zend_Registry::getInstance()->config->watson;
        $this->_httpClient->setUri($config->downloadDocumentUrl . $id);
        $payload = array();
        $this->_httpClient->setHeaders(array(
            'X-SyncTimeout' => $config->timeout,
            'Content-Type' => 'application/json',
            'Accept' => 'application/json'
        ));
        $response = $this->_httpClient->request(Zend_Http_Client::GET);
        return $response->getBody();
    }

    /*
     * Gets meta data for a specific document based on given Id
     *
     * @param Integer $id
     */

    public function getMetaDataForDocument($id) {
        $config = Zend_Registry::getInstance()->config->watson;
        $this->_httpClient->setUri($config->metaDataUrl . $id);
        $payload = array();
        $this->_httpClient->setHeaders(array(
            'X-SyncTimeout' => $config->timeout,
            'Content-Type' => 'application/json',
            'Accept' => 'application/json'
        ));
        $response = $this->_httpClient->request(Zend_Http_Client::GET);
        $responseBody = Zend_Json::decode($response->getBody());
        return $responseBody;
    }

    public function saveMetaDataForDocument($id, $data) {
        $config = Zend_Registry::getInstance()->config->watson;
        $this->_httpClient->setUri($config->saveMetaDataUrl . $id);
        $this->_httpClient->setHeaders(array(
            'X-SyncTimeout' => $config->timeout,
            'Content-Type' => 'application/json',
            'Accept' => 'application/json'
        ));
        $response = $this->_httpClient->setRawData($data)->request(Zend_Http_Client::PUT);
        $responseBody = Zend_Json::decode($response->getBody());
        return $responseBody;
    }

}
