<?php
/**
 * Rest JSON Client
 *
 * @package HCMS
 * @subpackage Rest
 * @copyright Horisen
 * @author milan
 */
class HCMS_Rest_Client_Json {
    protected $_username = null;
    protected $_password = null;
    protected $_url;

    /**
     *
     * @var array
     */
    protected $curlOptions = array(
        CURLOPT_RETURNTRANSFER  => true, // return web page
        CURLOPT_HEADER          => false, // don't return headers
        //CURLINFO_HEADER_OUT     => true, //show request headers
        CURLOPT_FOLLOWLOCATION  => true, // follow redirects
        CURLOPT_ENCODING        => "", // handle all encodings
        CURLOPT_USERAGENT       => "HCMS_Rest_Client_Json", // who am i
        CURLOPT_AUTOREFERER     => true, // set referer on redirect
        CURLOPT_CONNECTTIMEOUT  => 120, // timeout on connect
        CURLOPT_TIMEOUT         => 120, // timeout on response
        CURLOPT_MAXREDIRS       => 10, // stop after 10 redirects
        CURLOPT_SSL_VERIFYPEER  => false,
        CURLOPT_NOBODY          => false
    );

    /**
     * Last transport (curl) error code
     * 
     * @var int
     */
    public $lastTransportErrorNumber = 0;

    /**
     * Last transport (curl) error message
     * 
     * @var string
     */
    public $lastTransportErrorMessage = '';

    /**
     * last error array
     * 
     * @var array
     */
    protected $_lastErrorArr = array();

    /**
     * Get Last http response code
     * 
     * @var int
     */
    protected $_lastHttpResponseCode = 0;

    /**
     * singleton instance
     *
     * @var HCMS_Rest_Client_Json
     */
    protected static $_instance = null;

    /**
     * set config
     * 
     * @param array $config
     */
    private function setConfig(array $config){
        if(isset ($config['username'])){
            $this->_username = $config['username'];
        }
        if(isset ($config['password'])){
            $this->_password = $config['password'];
        }
        if(isset ($config['url'])){
            $this->_url = $config['url'];
        }        
    }
    /**
     * private constructor
     * 
     * @param array $config
     */
    private function  __construct(array $config)
    {
        $this->setConfig($config);
    }

    /**
     * get instance
     * 
     * @param array $config
     * @return HCMS_Rest_Client_Json
     */
    public static function getInstance(array $config = array())
    {
        if(self::$_instance === null)
        {
            self::$_instance = new self($config);
        }
        if(count($config)){
            self::$_instance->setConfig($config);
        }
        return self::$_instance;
    }

    /**
     * Get remote content
     * 
     * @param string $method
     * @param string $url
     * @param string $data
     * @param array $headers
     * @return string|boolean
     */
    protected function grabContent($method,$url,$data = "", array $headers = array()){
        //init curl
        //echo "\nURL: $url\n";
        $curlCon = curl_init($url);

        curl_setopt($curlCon, CURLOPT_CUSTOMREQUEST,$method);

        $headers  =  array_merge(array("Content-type: application/json"),$headers);

        curl_setopt($curlCon,CURLOPT_HTTPHEADER,$headers);
        if(isset ($this->_username)){
            curl_setopt($curlCon, CURLOPT_USERPWD, "$this->_username:$this->_password");
        }

        //set options
        reset($this->curlOptions);
        while(list($option,$value) = each($this->curlOptions)){
            curl_setopt($curlCon,$option,$value);
        }
        if(isset ($data) && $data != ''){
            curl_setopt($curlCon, CURLOPT_POSTFIELDS, $data);
        }

        $pageContent = curl_exec($curlCon);
        $errNo = curl_errno($curlCon);
        $errMsg = curl_error($curlCon);        
        
        if($errNo==0){
            $this->_lastHttpResponseCode = curl_getinfo($curlCon,CURLINFO_HTTP_CODE);
            curl_close( $curlCon );
            return $pageContent; //content ready for parsing //
        }
        else{
            $this->lastTransportErrorMessage = $errMsg;
            $this->lastTransportErrorNumber = $errNo;
            curl_close( $curlCon );
            return false;
        }
    }

    /**
     * Last result error array
     * 
     * @return array
     */
    public function getLastErrorArray(){
        return $this->_lastErrorArr;
    }

    /**
     * Last result error array
     *
     * @return array
     */
    public function getLastHttpResponseCode(){
        return $this->_lastHttpResponseCode;
    }


    /**
     * Process response from api server
     * 
     * @param mixed $result
     * @return array|false
     */
    protected function processResult($result){
        if($result !== FALSE){
            $resultObj = json_decode($result, true);
            if(isset ($resultObj['error'])){
                $this->_lastErrorArr = $resultObj['error'];
                return false;
            }
            else{
                if(isset ($resultObj['data'])){
                    return $resultObj['data'];
                }
                else{
                    return $resultObj;
                }
            }
        }
        else{
            $this->_lastErrorArr = array("code"=>$this->lastTransportErrorNumber,"message"=>$this->lastTransportErrorMessage);
            return false;
        }
    }

    /**
     * Build REST url
     * 
     * @param string $entity
     * @param int $id
     * @param array $params
     * @return string
     */
    protected function buildUrl($entity,$id = 0,array $params = array()){
        $url = $this->_url . "/" . urlencode($entity) . "/";
        if($id > 0){
            $url .= urlencode($id) . "/";
        }
        if(count($params)){
            $url .= "?" . http_build_query($params);
        }
        return $url;
    }

    /**
     * List all entities
     * 
     * @param string $entity
     * @param array $params
     * @return array|false
     */
    public function index($entity,array $params = array()){
        $url = $this->buildUrl($entity, 0, $params);
        $result = $this->grabContent("get", $url);

        return $this->processResult($result);
    }

    /**
     * Get an entity
     *
     * @param string $entity
     * @param int $id
     * @return array|false
     */
    public function get($entity,$id){
        $url = $this->buildUrl($entity, $id);
        $result = $this->grabContent("get", $url);

        return $this->processResult($result);
    }

    /**
     * Delete an entity
     *
     * @param string $entity
     * @param int $id
     * @return array|false
     */
    public function delete($entity,$id){
        $url = $this->buildUrl($entity, $id);
        $result = $this->grabContent("delete", $url);

        return $this->processResult($result);
    }

    /**
     * Add an entity
     *
     * @param string $entity
     * @param array $data
     * @return array|false
     */
    public function post($entity,array $data = null){
        $url = $this->buildUrl($entity);
        $result = $this->grabContent("post", $url,json_encode($data));

        return $this->processResult($result);
    }

    /**
     * Update an entity
     *
     * @param string $entity
     * @param int $id
     * @param array $data
     * @return array|false
     */
    public function put($entity,$id, array $data = null){
        $url = $this->buildUrl($entity, $id);
        $result = $this->grabContent("put", $url,json_encode($data));

        return $this->processResult($result);
    }
}
