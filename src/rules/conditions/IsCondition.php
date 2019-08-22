<?php

namespace barrelstrength\sproutforms\conditionallogictypes\conditions;

use barrelstrength\sproutforms\base\BaseCondition;

class IsCondition extends BaseCondition
{
    public $fieldRule;

    public static function getLabel(): string
    {
        return 'is';
    }

    public static function getValue(): string
    {
        return 'is';
    }

    public function getValueInputHtml($name)
    {
        if ($this->fieldRule instanceof BaseCondition){

        }else{

        }

        return "";
    }
}