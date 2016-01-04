<?php
/**
 * Base entity model.
 *
 * @package HCMS
 * @subpackage Utils
 * @copyright Horisen
 * @author milan
 */
class HCMS_Utils_Time {
	/**
	 * convert timestamp to mysql datetime format
	 *
	 * @param int $timestamp
	 * @return string
	 */
	static public function timeTs2Mysql($timestamp){
		return date('Y-m-d H:i:s',$timestamp);
	}

	/**
	 * parse mysql given date/time and return timestamp
	 * @param string $dateTime
	 * @return int|bool
	 */
	static public function timeMysql2Ts($dateTime) {
		if (preg_match("/^([0-9]{4})-([0-9]{1,2})-([0-9]{1,2}) ([0-9]{1,2}):([0-9]{1,2}):([0-9]{1,2})/",$dateTime,$r)) {
			return mktime($r[4],$r[5],$r[6],$r[2],$r[3],$r[1]);
		}
		else if (preg_match("/^([0-9]{4})-([0-9]{1,2})-([0-9]{1,2})$/",$dateTime,$r)) {
			return mktime(0,0,0,$r[2],$r[3],$r[1]);
		}
		else if (preg_match("/^([0-9]{4})([0-9]{2})([0-9]{2})([0-9]{2})([0-9]{2})([0-9]{2})/",$dateTime,$r)) {
			return mktime($r[4],$r[5],$r[6],$r[2],$r[3],$r[1]);
		}
		return false;
	}

        /**
         * parse mysql given date/time and return local string
         * @param string $dateTime
         * @return string
         */
        static public function timeMysql2Local($dateTime) {
            return date("j.n.Y",self::timeMysql2Ts($dateTime));
        }

        /**
         * get mysql date time
         *
         * @param Zend_Date $date
         * @return string
         */
        static public function getMysqlDateTime(Zend_Date $date){
            return $date->get('yyyy-MM-dd HH:mm:ss');
        }
        
        /**
         * parse mysql given date/time and return local string (but real local not like this shit above)
         *
         * @param type $dateTime
         * @param type $format
         * @return type 
         */
        
        static public function timeMySql2Custom($dateTime, $format) {
            $date = new Zend_Date($dateTime);
            return $date->toString($format);
        }

}
