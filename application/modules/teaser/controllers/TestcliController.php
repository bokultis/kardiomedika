<?php

class Teaser_TestcliController extends HCLI_Controller_Action {

    protected $_themeName = 'horisen_2015';
    protected $_langs = array('en','de');
    protected $_createVersions = array('1366.', '960_2x.', '768_2x.', '480_2x.', '320_2x.');
    protected $_copyFrom = '960';
    
    /**
     * Logger
     * @var Zend_Log
     */
    protected $_logger = null;
    protected static $_boxes = null;    

    public function __construct(Zend_Controller_Request_Abstract $request, Zend_Controller_Response_Abstract $response, array $invokeArgs = array()) {
        parent::__construct($request, $response, $invokeArgs);
        $this->_logger = Zend_Registry::get('Zend_Log');
    }

    public function echoAction() {
        $console = $this->getConsoleOptions(
                array('name|n=s' => 'Tell me your name')
        );
        $message = 'Hello ' . $console->getOption("name");
        echo $message, "\n";
        $this->_logger->log($message, Zend_Log::INFO);
        exit(0);
    }

    public function maxAction() {
        echo Teaser_Model_TeaserMapper::getInstance()->getMaxItemOrderNum(1);
    }

    /**
     * Get box configuration
     * @param string $boxCode
     * @return array|null
     */
    public function getBox($theme, $boxCode = null) {
        if (self::$_boxes === null) {
            $filePath = APPLICATION_PATH . '/../themes/' . $theme . '/configs/teaser/boxes.php';
            $boxes = require $filePath;
            foreach ($boxes as $code => $box) {
                if (isset($boxes[$code]['params']['images_dims'])) {
                    $section = isset($boxes[$code]['params']['images_section']) ? $boxes[$code]['params']['images_section'] : 'default';
                    $boxes[$code]['params']['images'] = $this->getImagesParams($theme, $boxes[$code]['params']['images_dims'], $section);
                }
            }
            self::$_boxes = $boxes;
        }
        if (isset($boxCode)) {
            if (isset(self::$_boxes[$boxCode])) {
                return self::$_boxes[$boxCode];
            } else {
                return null;
            }
        }
        return self::$_boxes;
    }

    /**
     * Get image params
     * 
     * @param array $dims
     * @param boolean $independent960
     * @return array
     */
    public function getImagesParams($theme, $dims, $section = 'default') {
        $filePath = APPLICATION_PATH . '/../themes/' . $theme . '/configs/picture.php';
        $imagesConf = require $filePath;
        $variations = $imagesConf[$section];

        $result = array();
        $i = 0;
        foreach ($variations as $query => $suffix) {
            $width = $dims[$i * 2];
            $height = $dims[$i * 2 + 1];
            $elements = explode('_', $suffix);
            $vp = $elements[0];
            $density = (count($elements) >= 2) ? $elements[1] : 1;
            $result['img_' . $suffix] = array(
                "name" => "Image for viewport width $vp and density $density",
                "media_query" => $query,
                "options" => array(
                    "minwidth" => $width,
                    "maxwidth" => $width,
                    "minheight" => $height,
                    "maxheight" => $height
                )
            );
            $i++;
        }

        return $result;
    }

    public function getBoxCodes() {
        $codes = array();
        $teasers = Teaser_Model_TeaserMapper::getInstance()->fetchAll();
        /* @var $teaser Teaser_Model_Teaser */
        foreach ($teasers as $teaser) {
            $codes[$teaser->get_box_code()] = true;
        }
        return $codes;
    }

    public function updateItemContent($lang) {
        $itemMapper = Teaser_Model_ItemMapper::getInstance();

        $boxCodes = $this->getBoxCodes();

        foreach ($boxCodes as $code => $val) {
            $items = $itemMapper->fetchAll(array(
                'lang' => $lang,
                'box_code' => $code
                    ));
            /* @var  Teaser_Model_Item $item */
            foreach ($items as $item) {
                $box = $this->getBox($this->_themeName, $item->get_box_code());
                if (!isset($box['params']['images'])) {
                    continue;
                }
                foreach ($box['params']['images'] as $imageKey => $imageValues) {
                    if ($item->get_content($imageKey) && trim($item->get_content($imageKey)) != '') {
                        continue;
                    }
                    $valCopy = $item->get_content('img_' . $this->_copyFrom);
                    if (preg_match('/(.+)' . $this->_copyFrom . '(.+)/i', $valCopy, $matches)) {
                        $newKey = substr($imageKey, 4);
                        $newVal = $matches[1] . $newKey . $matches[2];
                        //echo "$newVal\n";
                        $content = $item->get_content();
                        $content[$imageKey] = $newVal;
                        $item->set_content($content);
                        //print_r($item);
                    }
                }
                //print_r($item);
                $itemMapper->populateTeaserIds($item);
                $itemMapper->save($item, $lang);
            }
        }
    }

    protected function addItemImageContent(Teaser_Model_Item $item, $imageKey, $prefix, $suffix, $ext) {
        $content = $item->get_content();
        if (isset($content[$imageKey])) {
            return;
        }
        $newFile = $prefix . $suffix . $ext;
        echo "$newVal\n";
        $content = $item->get_content();
        $content[$imageKey] = $newVal;
        $item->set_content($content);
    }

    public function updatecontentAction() {
        foreach ($this->_langs as $lang) {
            $this->updateItemContent($lang);
        }
    }

    public function copyAsVersion($sourceFile, $prefix, $suffix, $ext) {
        $newFile = $prefix . $suffix . $ext;
        if (file_exists($newFile)) {
            return;
        }
        $green = "\e[0;32m";
        $reset = "\e[0m";
        echo "\n" . $green . "new file: $newFile $reset";
        copy($sourceFile, $newFile);
        //echo "\ncopy($sourceFile, $newFile);\n";
    }

    public function imagecopyAction() {
        $dirPath = APPLICATION_PATH . '/../public/images';
        $directory = new RecursiveDirectoryIterator($dirPath);
        $iterator = new RecursiveIteratorIterator($directory);
        $regex = new RegexIterator($iterator, '/(.+)' . $this->_copyFrom . '\.(jpg|png)$/i', RecursiveRegexIterator::GET_MATCH);
        foreach ($regex as $sourceFile => $matches) {
            foreach ($this->_createVersions as $version) {
                $this->copyAsVersion($sourceFile, $matches[1], $version, $matches[2]);
            }
        }
        //chown at the end
        echo "\nDon't forget to chown -R milan:milan \"$dirPath\"\n";
    }

}