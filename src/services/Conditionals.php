<?php

namespace barrelstrength\sproutforms\services;

use barrelstrength\sproutforms\base\ConditionalInterface;
use barrelstrength\sproutforms\base\ConditionalLogic;
use barrelstrength\sproutforms\conditionallogictypes\MissingIntegration;
use barrelstrength\sproutforms\records\ConditionalLogic as ConditionalLogicRecord;
use craft\base\Component;
use craft\db\Query;
use craft\errors\MissingComponentException;
use craft\events\RegisterComponentTypesEvent;
use Craft;
use craft\helpers\Component as ComponentHelper;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use yii\base\InvalidConfigException;


class Conditionals extends Component
{
    const EVENT_REGISTER_CONDITIONALS = 'registerConditionals';

    /**
     * Returns all registered Conditional Logic Types
     *
     * @return array
     */
    public function getAllConditionalsTypes(): array
    {
        $event = new RegisterComponentTypesEvent([
            'types' => []
        ]);

        $this->trigger(self::EVENT_REGISTER_CONDITIONALS, $event);

        return $event->types;
    }

    /**
     * @return ConditionalLogic[]
     */
    public function getAllConditionals(): array
    {
        $conditionalTypes = $this->getAllConditionalsTypes();

        $conditionals = [];

        foreach ($conditionalTypes as $conditionalType) {
            $conditionals[] = new $conditionalType();
        }

        return $conditionals;
    }

    /**
     * @param $formId
     *
     * @return array
     * @throws InvalidConfigException
     * @throws MissingComponentException
     */
    public function getFormConditionals($formId): array
    {
        $results = (new Query())
            ->select([
                'conditional.id',
                'conditional.formId',
                'conditional.name',
                'conditional.type',
                'conditional.rules',
                'conditional.settings',
                'conditional.enabled'
            ])
            ->from(['{{%sproutforms_conditional_logic}} conditional'])
            ->where(['conditional.formId' => $formId])
            ->all();

        $conditionals = [];

        foreach ($results as $result) {
            $conditional = ComponentHelper::createComponent($result, ConditionalInterface::class);
            $conditionals[] = new $result['type']($conditional);
        }

        return $conditionals;
    }

    /**
     * @param $conditionalId
     *
     * @return ConditionalLogic|null
     * @throws InvalidConfigException
     * @throws MissingComponentException
     */
    public function getConditionalById($conditionalId)
    {
        $result = (new Query())
            ->select([
                'conditional.id',
                'conditional.formId',
                'conditional.name',
                'conditional.type',
                'conditional.rules',
                'conditional.settings',
                'conditional.enabled'
            ])
            ->from(['{{%sproutforms_integrations}} conditional'])
            ->where(['conditional.id' => $conditionalId])
            ->one();

        if (!$result) {
            return null;
        }

        $conditional = ComponentHelper::createComponent($result, ConditionalInterface::class);

        return new $result['type']($conditional);
    }

    /**
     * @param ConditionalLogic $conditionalLogic
     *
     * @return bool
     */
    public function saveConditional(ConditionalLogic $conditionalLogic): bool
    {
        if ($conditionalLogic->id) {
            $conditionalRecord = ConditionalLogicRecord::findOne($conditionalLogic->id);
        } else {
            $conditionalRecord = new ConditionalLogicRecord();
        }

        $conditionalRecord->type = get_class($conditionalLogic);
        $conditionalRecord->formId = $conditionalLogic->formId;
        $conditionalRecord->name = $conditionalLogic->name ?? $conditionalLogic::displayName();
        $conditionalRecord->enabled = $conditionalLogic->enabled;
        $conditionalRecord->rules = $conditionalLogic->rules;

        $conditionalRecord->settings = $conditionalLogic->getSettings();

        if ($conditionalRecord->save()) {
            $conditionalLogic->id = $conditionalRecord->id;
            $conditionalLogic->name = $conditionalRecord->name;
            return true;
        }

        return false;
    }

    /**
     * @param $config
     *
     * @return ConditionalInterface
     * @throws InvalidConfigException
     */
    public function createConditional($config): ConditionalInterface
    {
        if (is_string($config)) {
            $config = ['type' => $config];
        }

        try {
            /** @var ConditionalLogic $integration */
            $conditional = ComponentHelper::createComponent($config, ConditionalInterface::class);
        } catch (MissingComponentException $e) {
            $config['errorMessage'] = $e->getMessage();
            $config['expectedType'] = $config['type'];
            unset($config['type']);

            $conditional = new MissingIntegration($config);
        }

        return $conditional;
    }

    /**
     * Loads the sprout modal conditional via ajax.
     *
     * @param ConditionalLogic $conditional
     *
     * @return array
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function getModalConditionalTemplate(ConditionalLogic $conditional): array
    {
        $view = Craft::$app->getView();

        $html = $view->renderTemplate('sprout-forms/forms/_editConditionalModal', [
            'conditional' => $conditional,
        ]);

        $js = $view->getBodyHtml();
        $css = $view->getHeadHtml();

        return [
            'html' => $html,
            'js' => $js,
            'css' => $css
        ];
    }

}
