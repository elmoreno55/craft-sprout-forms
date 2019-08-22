<?php

namespace barrelstrength\sproutforms\conditionallogictypes\conditions;

use barrelstrength\sproutforms\base\BaseCondition;

class EndsWithCondition extends BaseCondition
{
    public $fieldRule;

    public static function getLabel(): string
    {
        return 'ends with';
    }

    public static function getValue(): string
    {
        return 'endsWith';
    }

    public function getValueInputHtml($name)
    {
        if ($this->fieldRule instanceof BaseCondition){

        }else{

        }

        return "";
    }
}