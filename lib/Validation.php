<?php

/**
 * User: yulu04
 * Date: 16/6/30
 * Time: 下午5:51
 * Desc: 表单校验, 后期如有需要可创建专用的Exception类和配置文件
 */
class Validation
{
    // 校验规则名称和对应函数
    private $aRules = array(
        'required' => '_required',
        'reg'      => '_reg',
        'numeric'  => '_numeric',
        'int'      => '_int',
        'between'  => '_between',
        'max'      => '_max',
        'min'      => '_min',
        'email'    => '_email',
        'len'      => '_len',
        'json'     => '_json',
        'equal'    => '_equal',
        'in'       => '_in',
        'time'     => '_time',
        'string'   => '_string',
        'array'    => '_array',
        'uniq'     => '_uniq',
    );

    // 校验规则的错误信息
    private $arrMessages = array(
        'en' => array(
            'required' => '%0 is required!',
            'reg'      => '%0 not match required format!',
            'numeric'  => '%0 must be numeric!',
            'int'      => '%0 mst be int type!',
            'between'  => '%0 must between %1 and %2!',
            'max'      => '%0 must lower than %1!',
            'min'      => '%0 must higher than %1!',
            'email'    => '%0 must be validate email address!',
            'len'      => '%0 length must between %1 to %2!',
            'json'     => '%0 must be json string!',
            'equal'    => '%0 not equal to specific value!',
            'in'       => '%0 not in specific values!',
            'time'     => '%0 must be valid time!',
            'string'   => '%0 must be string!',
            'array'    => '%0 must be array!',
            'uniq'     => '%1 has been taken!',
        ),

        'default' => array(
            'required' => '%0必须填写!',
            'reg'      => '%0不符合格式要求!',
            'numeric'  => '%0必须是纯数字!',
            'int'      => '%0必须是整数!',
            'between'  => '%0必须位于%1,%2之间!',
            'max'      => '%0最大值是%1!',
            'min'      => '%0最小值是%1!',
            'email'    => '%0必须是电子邮箱!',
            'len'      => '%0长度必须位于%1,%2之间!',
            'json'     => '%0必须是json字符串!',
            'equal'    => '%0与期望值不符!',
            'in'       => '%0不在指定值中间!',
            'time'     => '%0必须是合法的时间!',
            'string'   => '%0必须是字符串!',
            'array'    => '%0必须是数组!',
            'uniq'     => '%0已被占用!',
        ),
    );

    /**
     * desc   字段唯一性检验, 第一个参数为表名, 后面的为要比较字段(可选)
     * example 'name' => 'uniq|user', user表的name字段要唯一
     * example 'name' => 'uniq|user@user_anem', user表的user_name字段唯一
     * example 'name' => 'uniq|category_id,name', user表的category_id,name字段唯一
     * example 'name' => 'uniq|category_id=1,name', user表的name字段唯在category_id字段等于1的条件下唯一
     * date
     * @param $data
     * @param $field
     * @param $arrParams
     * @return bool
     * @throws Exception
     */
    private function _uniq($data, $field, $arrParams)
    {
        // 没有参数
        if (count($arrParams) == 0) {
            throw new Exception("uniq validation should have 1 params!");
        }

        // 表名
        $sTable = $arrParams[0];

        // 只有一个参数(表名)时, 使用传入的字段名做为数据库字段名取进行重复校验
        if (count($arrParams) == 1) {
            $oConds = array($field => $data[ $field ]);
        } else {
            $aFields = array_slice($arrParams, 1);
            $oConds  = array();
            foreach ($aFields as $sField) {
                // 1 提取字段比较符号
                $sPat = '/=|>=|<=|!=|>|</';
                preg_match($sPat, $sField, $aSymbolMatch);
                if (count($aSymbolMatch) > 1) {
                    throw new Exception("uniq validation field at most have 1 equal symbol!");
                }
                $sCompareSymbol = count($aSymbolMatch) ? $aSymbolMatch[0] : '=';

                // 2 提取字段名,  左边为字段名,右边为比较值(可选)
                $aCompareParts = preg_split('/=|>=|<=|!=/', $sField);
                $aFieldName    = explode('@', $aCompareParts[0]);
                if (count($aFieldName) > 2) {
                    throw new Exception("uniq validation field at most have 1 alias name!");
                }
                $sFieldName = count($aFieldName) == 2 ? $aFieldName[1] : $aFieldName[0];

                // 3 提取字段比较值
                if (count($aCompareParts) > 2) {
                    throw new Exception("uniq validation field at most have 1 equal symbol!");
                }
                $sCompareValue = count($aCompareParts) == 1 || empty($aCompareParts[1]) ? $data[ $sFieldName ] : $aCompareParts[1];

                $oConds["${sFieldName}[$sCompareSymbol]"] = $sCompareValue;
            }
        }

        $db   = Database::getInstance();
        $rows = $db->select($sTable, array($field), $oConds);
        if (!is_array($rows)) {
            throw new Exception("uniq validation get data exception");
        }
        return empty($rows);
    }

    private function _array($data, $field, $arrParams)
    {
        return is_array($data[ $field ]);
    }

    private function _string($data, $field, $arrParams)
    {
        return is_string($data[ $field ]);
    }

    /**
     * @desc  校验字段是否与指定值相等
     * @param $data
     * @param $field
     * @param $arrParams
     * @return bool
     */
    private function _equal($data, $field, $arrParams)
    {
        return $data[ $field ] == $arrParams[0];
    }

    /**
     * @desc  校验字段是否在指定范围中
     * @param $data
     * @param $field
     * @param $arrParams
     * @return bool
     */
    private function _in($data, $field, $arrParams)
    {
        return in_array($data[ $field ], $arrParams);
    }

    /**
     * @desc 校验字段是否存在
     * @param $data
     * @param $field
     * @param $arrParams
     * @return bool
     */
    private function _required($data, $field, $arrParams)
    {
        return isset($data[ $field ]);
    }

    /**
     * @desc 校验字段是否满足正则匹配条件
     * @param $data
     * @param $field
     * @param $arrParams
     * @return bool|int
     */
    private function _reg($data, $field, $arrParams)
    {
        if (empty($arrParams[0])) {
            return false;
        }

        $pattern = '/' . $arrParams[0] . '/';
        if (isset($data[ $field ])) {
            return preg_match($pattern, $data[ $field ]);
        }
        return false;
    }

    /**
     * @desc 校验字段是否由数字组成
     * @param $data
     * @param $field
     * @param $arrParams
     * @return bool
     */
    private function _numeric($data, $field, $arrParams)
    {
        if (isset($data[ $field ])) {
            return is_numeric($data[ $field ]);
        }
        return false;
    }

    /**
     * @desc 校验字段是否为整数
     * @param $data
     * @param $field
     * @param $arrParams
     * @return bool
     */
    private function _int($data, $field, $arrParams)
    {
        if (isset($data[ $field ])) {
            return filter_var($data[ $field ], FILTER_VALIDATE_INT) !== false;
        }
        return false;
    }

    /**
     * @desc 校验数字所属范围
     * @param $data
     * @param $field
     * @param $arrParams
     * @return bool
     */
    private function _between($data, $field, $arrParams)
    {
        $min = $arrParams[0];
        $max = $arrParams[1];
        if (isset($data[ $field ])) {
            return $data[ $field ] >= $min && $data[ $field ] <= $max;
        }
        return false;
    }

    /**
     * @desc 校验数字是否大于最小值
     * @param $data
     * @param $field
     * @param $arrParams
     * @return bool
     */
    private function _min($data, $field, $arrParams)
    {
        $min = $arrParams[0];
        if (isset($data[ $field ])) {
            return $data[ $field ] >= $min;
        }
        return false;
    }

    /**
     * @desc 校验数字是否小于最大值
     * @param $data
     * @param $field
     * @param $arrParams
     * @return bool
     */
    private function _max($data, $field, $arrParams)
    {
        $max = $arrParams[0];
        if (isset($data[ $field ])) {
            return $data[ $field ] <= $max;
        }
        return true;
    }

    /**
     * @desc 校验字符串是否为email
     * @param $data
     * @param $field
     * @param $arrParams
     * @return bool
     */
    private function _email($data, $field, $arrParams)
    {
        if (isset($data[ $field ])) {
            return filter_var($data[ $field ], FILTER_VALIDATE_EMAIL) !== false;
        }
        return false;
    }

    /**
     * @desc 校验字段是否不空
     * @param $data
     * @param $field
     * @param $arrParams
     * @return bool
     */
    private function _not_empty($data, $field, $arrParams)
    {
        return !empty($data[ $field ]);
    }

    /**
     * @desc 校验字段长度
     * @param $data
     * @param $field
     * @param $arrParams
     * @return bool
     */
    private function _len($data, $field, $arrParams)
    {
        $len    = mb_strlen($data[ $field ]);
        $minLen = $arrParams[0];
        $maxLen = $arrParams[1];
        return $minLen <= $len && $len <= $maxLen;
    }

    /**
     * @desc 校验字段是否json字符串
     * @param $data
     * @param $field
     * @param $arrParams
     * @return bool
     */
    private function _json($data, $field, $arrParams)
    {
        return is_array(json_decode($data[ $field ], true));
    }

    /**
     * desc   校验字段是否时间字符串
     * @param $data
     * @param $field
     * @param $arrParams
     * @return bool
     */
    private function _time($data, $field, $arrParams)
    {
        return strtotime($data[ $field ]) !== false;
    }

    /**
     * @desc 数组验证
     * @param $data
     * @param $rules
     * @param $oErrors
     * @param string $lang
     * @return boolean
     * @throws Exception
     */
    public function validate($data, $rules, &$oErrors, $lang = 'default')
    {
        $arrMessages = array();
        foreach ($rules as $sField => $sFieldRules) {
            list($sFieldName, $sFieldAlias) = $this->_getFieldName($sField);
            $aFieldRules = explode('|', $sFieldRules);
            foreach ($aFieldRules as $sSingleRule) {
                $aRuleElements = explode(':', $sSingleRule); // rule由名称和参数组成 比如 between:1,100
                if (!$this->_checkRule($data, $sFieldName, $aRuleElements)) {
                    $arrMessages[ $sFieldName ] = $this->_getErrorMessage($aRuleElements, $sFieldAlias, $lang);
                    break;
                }
            }
        }

        $oErrors = $arrMessages;
        return empty($oErrors);
    }

    /**
     * @desc   调用校验函数校验字段
     * @param  array $data
     * @param  string $sField
     * @param  array $aRuleElements
     * @return boolean
     * @throws Exception
     */
    private function _checkRule($data, $sField, $aRuleElements)
    {
        if (!isset($aRuleElements[0])) {
            return false;
        }
        $sRuleName   = $aRuleElements[0]; // 规则名称
        $aRuleParams = isset($aRuleElements[1]) ? explode(',', $aRuleElements[1]) : array(); // 规则参数

        // 判断rule和rule对应的函数是否存在
        if (!isset($this->aRules[ $sRuleName ])) {
            throw new Exception("rule [$sRuleName] not in rules array!"); // 规则不存在
        }
        $sFuncName = $this->aRules[ $sRuleName ];
        if (!is_callable(array($this, $sFuncName))) {
            throw new Exception("rule [$sRuleName] function not exists!"); // 规则校验函数不存在
        }

        if ($sRuleName === 'required') {
            return $this->_required($data, $sField, $aRuleParams);
        }
        return isset($data[ $sField ]) ? $this->$sFuncName($data, $sField, $aRuleParams) : true;
    }

    /**
     * desc 获取字段校验错误信息
     * date 2017-07-22
     * @param array $aRuleElements
     * @param string $sFieldName
     * @param string $lang
     * @return string
     * @throws Exception
     */
    private function _getErrorMessage($aRuleElements, $sFieldName, $lang)
    {
        if (!isset($aRuleElements[0])) {
            throw new Exception("empty rule [$sFieldName]!");
        }
        $sRuleName   = $aRuleElements[0];
        $aRuleParams = isset($aRuleElements[1]) ? explode(',', $aRuleElements[1]) : array();
        if (!isset($this->arrMessages[ $lang ] [ $sRuleName ])) {
            throw new Exception("no [$lang][$sRuleName] error message config");
        }
        $message = $this->arrMessages[ $lang ] [ $sRuleName ];
        $message = str_replace('%0', $sFieldName, $message);
        $count   = count($aRuleParams);
        for ($i = 0; $i < $count; $i++) {
            $index   = $i + 1;
            $message = str_replace("%$index", $aRuleParams[ $i ], $message);
        }
        return $message;
    }

    /**
     * desc 提取字段的名称和别名, 比如age:年龄 返回 array('age', '年龄')
     * date 2017-07-22
     * @param  string $sField
     * @return array mixed
     */
    private function _getFieldName($sField)
    {
        $aElements = explode(':', $sField);
        return count($aElements) == 2 ? $aElements : array($sField, $sField);
    }

}



