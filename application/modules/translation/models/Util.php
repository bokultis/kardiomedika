<?php
/**
 * Grid utility methods
 *
 * @package Translation
 * @subpackage Models
 * @copyright Horisen
 * @author milan
 */
class Translation_Model_Util {
    /**
     * Prepare params for client processing
     * @param array $parameters
     *
     * @return $array
     */
    public static function prepareDataParams(&$parameters){
        if($parameters['_search'] == 'false'){
            return $parameters;
        }
        //single column search
        if (!empty($parameters['searchField']) ) {
            return self::prepareDataParamsSingleSearch($parameters);
        }

        //multi columns search
        if (!empty($parameters['filters'])) {
            return self::prepareDataParamsMultiSearch($parameters);
        }
    }

    /**
     * Function prepare data for grid single search.
     * It returns array of condiotions.
     *
     * @param array $parameters array of parameters
     * @return array Array of conditions.
     */
    public static function prepareDataParamsSingleSearch(array &$parameters){
        $operators = self::getAvailableOperators();
        $cond = self::prepareField($parameters['searchField']) . ' ' . $operators[$parameters['searchOper']];
        if ( self::isDateField($parameters['searchField']) ) {
            $parameters['searchString'] = HZ_Util::dateLocalToIso($parameters['searchString']);
            $cond = (strpos($parameters['searchString'], "00:00:00") === false) ?
                    self::prepareField($parameters['searchField']) . ' ' . $operators[$parameters['searchOper']]
                :   'DATE(' . self::prepareField($parameters['searchField']) . ') ' . $operators[$parameters['searchOper']];
        }
        $bind = self::bindValue($parameters['searchOper'], $parameters['searchString']);

        $parameters['where'] = array();
        $parameters['where'][] = array(
            'cond'      => $cond,
            'value'     => $bind
        );
        $parameters['singleSearch'] = true;
        return $parameters;
    }

    /**
     * Function prepare data for grid multi search.
     * It returns array of condiotions.
     *
     * @param array $parameters array of parameters
     * @return array Array of conditions.
     */
    public static function prepareDataParamsMultiSearch(array &$parameters){
        $filters = json_decode($parameters['filters']);

        if (empty ($filters->rules) && empty($filters->groups)) {
            return $parameters;
        }

        $operators = self::getAvailableOperators();
        $whereCond = ($filters->groupOp == 'AND')? 'where' : 'whereOr';
        $parameters[$whereCond] = array();
        foreach ($filters->rules as $rule) {
            $cond = self::prepareField($rule->field) . ' ' . $operators[$rule->op];
            if ( self::isDateField($rule->field) ) {
                $rule->data = HZ_Util::dateLocalToIso($rule->data);
                $cond = (strpos($rule->data, "00:00:00") === false) ?
                        self::prepareField($rule->field) . ' ' . $operators[$rule->op]
                    :   'DATE(' . self::prepareField($rule->field) . ') ' . $operators[$rule->op];
            }
            $bind = self::bindValue($rule->op, $rule->data);
            $parameters[$whereCond][] = array(
                'cond'      => $cond,
                'value'     => $bind
            );
        }
        $parameters['multiSearch'] = true;
    }

    /**
     * Function returns true if field is considered as date field.
     * By our convenction date fields names are ends with "_dt" or "_date" sufix.
     *
     * @param string $searchField
     * @return boolean true if field is considered as date field.
     */
    public static function isDateField($searchField)
    {
        return preg_match("/(_dt\b|_date\b)/i", $searchField);
    }

    /**
     * Function returns array where:
     *  key     - is grid's available operator
     *  value   - MySql representation of that operator
     *
     * @return array
     */
    public static function getAvailableOperators(){
        return array(
            'eq'    => '= ?',
            'ne'    => '<> ?',
            'lt'    => '< ?',
            'le'    => '<= ?',
            'gt'    => '> ?',
            'ge'    => '>= ?',
            'bw'    => 'LIKE ?',
            'bn'    => 'NOT LIKE ?',
            'in'    => 'IN (?)',
            'ni'    => 'NOT IN (?)',
            'ew'    => 'LIKE ?',
            'en'    => 'NOT LIKE ?',
            'cn'    => 'LIKE ?',
            'nc'    => 'NOT LIKE ?',
            'nu'    => 'IS NULL',
            'nn'    => 'IS NOT NULL'
        );
    }

    /**
     * Function bind search string value to it's approprite operator.
     *
     * @param string $searchOperator Grid's search operator.
     * @param string $searchString search string.
     * @return string
     */
    public static function bindValue($searchOperator, $searchString){
        switch($searchOperator){
            case 'bw':
            case 'bn':
                $searchString = $searchString . '%';
                break;
            case 'in':
            case 'no':
                $searchString = explode(',',$searchString);
                break;
            case 'ew':
            case 'en':
                $searchString = '%' . $searchString;
                break;
            case 'cn':
            case 'nc':
                $searchString = '%' . $searchString . '%';
        }
        return $searchString;
    }

    /**
     * Prepare Field
     * @param type $field
     * @return type 
     * 
     */
    public static function prepareField($field){
        $fieldArray = explode(".",$field);
        $preparedField = array();
        if(count($fieldArray) > 0){
            foreach ($fieldArray as $fieldString){
                $preparedField[] = "`".$fieldString."`";
            }
        }
        
        return join(".",$preparedField);
    }
}
