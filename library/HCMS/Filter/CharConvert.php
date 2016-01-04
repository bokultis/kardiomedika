<?php
/**
 * Zend Framework
 *
 * LICENSE
 * 
 * PHP version 5
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category  Zend
 * @package   Zend_Filter
 * @copyright Copyright (c) 2005-2011 Zend Technologies USA Inc.
 * @license   http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @see Zend_Filter_Interface
 */
//require_once 'Zend/Filter/Interface.php';

/**
 * @category Zend
 * @package  HCMS_Filter
 * @author   Bruno Thiago Leite Agutoli <brunotla1@gmail.com>
 * @version  Release: 0.1
 * @license  http://framework.zend.com/license/new-bsd     New BSD License
 */
class HCMS_Filter_CharConvert implements Zend_Filter_Interface
{
    /**
     * Corresponds to the third
     *
     * @var string
     */
    protected $_encoding;

    /**
     * Corresponds to the third 
     *
     * @var string
     */
    protected $_locale;

    /**
     * Replace white space for some chacacter
     * 
     * @var string
     */
    protected $_replaceWhiteSpace;

    /**
     * Allow only Alpha characters and numbers
     * 
     * @var boolean
     */
    protected $_onlyAlnum;

    /**
     * Sets the characters that are relevant and keeps the text 
     *
     * @var string|array
     */
    protected $_relevantChars;

   /**
     * Sets the characters that are relevant and should be 
     * replaced by the value set in "replaceWhiteSpace"
     *
     * @var string|array
     */
    protected $_irrelevantChars;

    /**
     *
     * @var array
     */
    protected $_transcriptMap = array();

    /**
     * Constant that holds an empty space
     *
     * string WHITE_SPACE
     */
    const WHITE_SPACE = ' ';

    /**
     * Create static filter for SEO
     *
     * @param array $data
     * @param array $options
     * @return Zend_Filter
     */
    public static function createSEOFilter(array $data = null, array $options = null){
        if(is_null($options)){
            $options = array();
        }
        $seoFilter = new Zend_Filter();
        $seoFilter->addFilter(new HCMS_Filter_CharConvert(array(
                    'replaceWhiteSpace' => '-',
                    'onlyAlnum' => true,
                    //'irrelevantChars'   => '',
                    //'relevantChars'   => '\/\+',
                    'locale' => isset($data['lang']) ? $data['lang'] : 'en_US',
                    'transcriptMap' => array(
                        "А" => "A", "Б" => "B", "В" => "V", "Г" => "G",
                        "Д" => "D", "Ђ" => "Dj",
                        "Е" => "E",
                        "Ж" => "Z",
                        "З" => "Z",
                        "И" => "I",
                        "Ј" => "J",
                        "К" => "K",
                        "Л" => "L",
                        "Љ" => "Lj",
                        "М" => "M",
                        "Н" => "N",
                        "Њ" => "Nj",
                        "О" => "O",
                        "П" => "P",
                        "Р" => "R",
                        "С" => "S",
                        "Т" => "T",
                        "Ћ" => "C",
                        "У" => "U",
                        "Ф" => "F",
                        "Х" => "H",
                        "Ц" => "C",
                        "Ч" => "C",
                        "Џ" => "Dz",
                        "Ш" => "S",
                        "а" => "a",
                        "б" => "b",
                        "в" => "v",
                        "г" => "g",
                        "д" => "d",
                        "ђ" => "dj",
                        "е" => "e",
                        "ж" => "z",
                        "з" => "z",
                        "и" => "i",
                        "ј" => "j",
                        "к" => "k",
                        "л" => "l",
                        "љ" => "lj",
                        "м" => "m",
                        "н" => "n",
                        "њ" => "nj",
                        "о" => "o",
                        "п" => "p",
                        "р" => "r",
                        "с" => "s",
                        "т" => "t",
                        "ћ" => "c",
                        "у" => "u",
                        "ф" => "f",
                        "х" => "h",
                        "ц" => "c",
                        "ч" => "c",
                        "џ" => "dz",
                        "ш" => "s",
                        "Ä"    => 'Ae',
                        "Ö"    => 'Oe',
                        "Ü"    => 'Ue',
                        "ä"    => 'ae',
                        "ö"    => 'oe',
                        "ü"    => 'ue'
                    )
                ) + $options));
        $seoFilter->addFilter(new Zend_Filter_StringToLower());

        return $seoFilter;
    }


    /**
     * Sets filter options
     *
     * @param  string|array $charset
     * @param  string|array $locale
     * @param  string|array $replaceWhiteSpace
     * @param  boolean|array $onlyAlnum
     * @param  string|array $relevantChars
     * @param  string|array $irrelevantChars
     *
     * @return void
     */
    public function __construct($options = array())
    {
        if ($options instanceof Zend_Config) {
            $options = $options->toArray();
        } else if (!is_array($options)) {
            $options = func_get_args();
            $temp = array();
            if (isset($options[0])) {
                $temp['charset'] = $options[0];
            }
            
            if (isset($options[1])) {
                $temp['locale'] = $options[1];
            }

            if (isset($options[2])) {
                $temp['replaceWhiteSpace'] = $options[2];
            }
            
            if (isset($options[3])) {
                $temp['onlyAlnum'] = $options[3];
            }

            if (isset($options[4])) {
                $temp['relevantChars'] = $options[4];
            }

            if (isset($options[5])) {
                $temp['irrelevantChars'] = $options[5];
            }
            $options = $temp;
        }
        
        if (!isset($options['encoding'])) {
            $options['encoding'] = 'UTF-8';
        }
 
        if (isset($options['charset'])) {
            $options['encoding'] = $options['charset'];
        }

        if (!isset($options['locale'])) {
            $options['locale'] = 'en_US';
        }

        if (!isset($options['replaceWhiteSpace'])) {
            $options['replaceWhiteSpace'] = ' ';
        }

        if (!isset($options['onlyAlnum'])) {
            $options['onlyAlnum'] = false;
        }

        if (!isset($options['relevantChars'])) {
            $options['relevantChars'] = '\+';
        }

        if (!isset($options['irrelevantChars'])) {
            $options['irrelevantChars'] = '\/'; 
        }
        if (!isset($options['transcriptMap'])) {
            $options['transcriptMap'] = array();
        }
        $this->setLocale($options['locale']);
        $this->setEncoding($options['encoding']);
        $this->setReplaceWhiteSpace($options['replaceWhiteSpace']);
        $this->setOnlyAlnum($options['onlyAlnum']);
        $this->setRelevantChars($options['relevantChars']);
        $this->setIrrelevantChars($options['irrelevantChars']);
        $this->setTranscriptMap($options['transcriptMap']);
    }

    /**
     * Set replaceWhiteSpace
     * @param  string $value
     * 
     * @return HCMS_Filter_CharConvert
     */
    public function setReplaceWhiteSpace($value)
    {
        $this->_replaceWhiteSpace = $value;
        return $this;
    }

    /**
     * Get replaceWhiteSpace
     *
     * @return string
     */
    public function getReplaceWhiteSpace()
    {
        return $this->_replaceWhiteSpace;
    }

    /**
     * Set onlyAlnum
     * @param  string $value
     *
     * @return HCMS_Filter_CharConvert
     */
    public function setOnlyAlnum($value)
    {
        $this->_onlyAlnum = $value;
        return $this;
    }

    /**
     * Get onlyAlnum
     *
     * @return string
     */
    public function getOnlyAlnum()
    {
        return $this->_onlyAlnum;
    }

    /**
     * Get locale
     *
     * @return string
     */
    public function getLocale()
    {
        return $this->_locale;
    }

    /**
     * Set locale
     *
     * @param  string $value
     * 
     * @return HCMS_Filter_CharConvert
     */
    public function setLocale($value)
    {
        $this->_locale = (string) $value;
        return $this;
    }

    /**
     * Get encoding
     *
     * @return string
     */
    public function getEncoding()
    {
        return $this->_encoding;
    }

    /**
     * Set encoding
     *
     * @param  string $value
     * 
     * @return HCMS_Filter_CharConvert
     */
    public function setEncoding($value)
    {
        $this->_encoding = (string) $value;
        return $this;
    }

    /**
     * Get relevant characters
     *
     * @return string|array
     */
    public function getRelevantChars()
    {
        return $this->_relevantChars;
    }

    /**
     * Set characters
     *
     * @param  string|array $chars
     * 
     * @return HCMS_Filter_CharConvert
     */
    public function setRelevantChars($chars)
    {
        $this->_relevantChars = $chars;
        return $this;
    }

    /**
     * Get irrelevant characters
     *
     * @return string|array
     */
    public function getIrrelevantChars()
    {
        return $this->_irrelevantChars;
    }

    /**
     * Set irrelevantes characters arrow irrelevant that the characters 
     * need to be replaced by the parameter chosen "replaceWhiteSpace"
     *
     * @param  string|array $chars
     * 
     * @return HCMS_Filter_CharConvert
     */
    public function setIrrelevantChars($chars)
    {
        $this->_irrelevantChars = $chars;
        return $this;
    }

    public function getTranscriptMap() {
        return $this->_transcriptMap;
    }

    public function setTranscriptMap($_transcriptMap) {
        $this->_transcriptMap = $_transcriptMap;        
        return $this;
    }


    /**
     * Returns the result of filtering $value
     *
     * @param  mixed $value
     * 
     * @throws HCMS_Filter_Exception If filtering $value is impossible
     * @return mixed
     */
    public function filter($value)
    {
        if (!function_exists('iconv')) {
            //require_once 'HCMS/Filter/Exception.php';
            throw new HCMS_Filter_Exception('Function iconv is required (PHP 4 >= 4.0.5, PHP 5)!');
        }
        //Get options
        $loc = $this->getLocale();
        $enc = $this->getEncoding();
        $rws = $this->getReplaceWhiteSpace();
        $oan = $this->getOnlyAlNum();
        $rlc = $this->getRelevantChars();
        $ilc = $this->getIrrelevantChars();

        //strtr

        if(is_array($this->_transcriptMap) && count($this->_transcriptMap)){
            $value = strtr($value, $this->_transcriptMap);
        }

        //Set locale
        setlocale(LC_ALL, $loc .".". $enc);
        //suppress errors @iconv
        $filtered = @iconv($enc, 'ASCII//TRANSLIT', $value);

        if (true === $oan) {
            $relevantChars   = (is_array($rlc))? implode('', $rlc) : $rlc;
            $irrelevantChars = (is_array($ilc))? implode('', $ilc) : $ilc;
            $filtered  = preg_replace("/[^a-zA-Z0-9{$irrelevantChars}{$relevantChars}\\{$rws} ]*/", '', trim($filtered));
            $filtered  = preg_replace("/[{$irrelevantChars}{$rws}]+/", self::WHITE_SPACE, $filtered);
        }

        if (self::WHITE_SPACE !== $rws) {
            $filtered = preg_replace('/\s\s+/', self::WHITE_SPACE, $filtered);
            $filtered = str_replace(self::WHITE_SPACE, $rws, $filtered);
        }
        return $filtered;
    }
}
?>
