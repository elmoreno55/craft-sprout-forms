<?php

namespace barrelstrength\sproutforms\conditionallogictypes\conditions;

use barrelstrength\sproutforms\base\BaseCondition;

class DoesNotStartWithCondition extends BaseCondition
{
    public $fieldRule;

    public static function getLabel(): string
    {
        return 'does not starts with';
    }

    public static function getValue(): string
    {
        return 'doesNotStartsWith';
    }

    public function getValueInputHtml($name)
    {
        if ($this->fieldRule instanceof BaseCondition){

        }else{

        }

        return "";
    }
}