<?php
namespace barrelstrength\sproutforms;

use Craft;
use craft\events\RegisterUrlRulesEvent;
use craft\web\UrlManager;
use yii\base\Event;

use barrelstrength\sproutcore\SproutCoreHelper;
use barrelstrength\sproutforms\models\Settings;
use barrelstrength\sproutforms\services\Groups;
use barrelstrength\sproutforms\variables\SproutFormsVariable;
use barrelstrength\sproutforms\events\RegisterFieldsEvent;
use barrelstrength\sproutforms\integrations\sproutforms\fields\PlainText;
use barrelstrength\sproutforms\integrations\sproutforms\fields\Number;
use barrelstrength\sproutforms\integrations\sproutforms\fields\Dropdown;
use barrelstrength\sproutforms\integrations\sproutforms\fields\RadioButtons;
use barrelstrength\sproutforms\integrations\sproutforms\fields\Checkboxes;
use barrelstrength\sproutforms\integrations\sproutforms\fields\MultiSelect;
use barrelstrength\sproutforms\integrations\sproutforms\fields\Assets;
use barrelstrength\sproutforms\integrations\sproutforms\fields\Categories;
use barrelstrength\sproutforms\integrations\sproutforms\fields\Entries;
use barrelstrength\sproutforms\integrations\sproutforms\fields\Tags;
use barrelstrength\sproutforms\integrations\sproutforms\fields\Email;
use barrelstrength\sproutforms\integrations\sproutforms\fields\EmailSelect;
use barrelstrength\sproutforms\integrations\sproutforms\fields\Hidden;
use barrelstrength\sproutforms\integrations\sproutforms\fields\Invisible;
use barrelstrength\sproutforms\services\Fields;

class SproutForms extends \craft\base\Plugin
{
	/**
	 * Enable use of SproutForms::$app-> in place of Craft::$app->
	 *
	 * @var [type]
	 */
	public static $app;

	public $hasCpSection = true;
	public $hasCpSettings = true;

	public function init()
	{
		parent::init();

		self::$app = $this->get('app');
		SproutCoreHelper::registerModule();

		Event::on(UrlManager::class, UrlManager::EVENT_REGISTER_CP_URL_RULES, function(RegisterUrlRulesEvent $event) {
				$event->rules = array_merge($event->rules, $this->getCpUrlRules());
			}
		);

		Event::on(Fields::class, Fields::EVENT_REGISTER_FIELDS, function(RegisterFieldsEvent $event) {
				$event->fields[] = new PlainText();
				$event->fields[] = new Number();
				$event->fields[] = new Dropdown();
				$event->fields[] = new Checkboxes();
				$event->fields[] = new RadioButtons();
				$event->fields[] = new MultiSelect();
				$event->fields[] = new Assets();
				$event->fields[] = new Categories();
				$event->fields[] = new Entries();
				$event->fields[] = new Tags();
				$event->fields[] = new Email();
				$event->fields[] = new EmailSelect();
				$event->fields[] = new Hidden();
				$event->fields[] = new Invisible();
			}
		);
	}

	public function getCpNavItem()
	{
		$parent = parent::getCpNavItem();
		$parent['url'] = 'sproutforms';
		return array_merge($parent,[
			'subnav' => [
				'entries' => [
					"label" => SproutForms::t("Entries"),
					"url"   => 'sproutforms/entries'
				],
				'forms' =>[
					"label" => SproutForms::t("Forms"),
					"url" => 'sproutforms/forms'
				],
				'settings' =>[
					"label" => SproutForms::t("Settings"),
					"url" => 'sproutforms/settings'
				]
			]
		]);
	}

	protected function createSettingsModel()
	{
		return new Settings();
	}

	/**
	 * @param string $message
	 * @param array  $params
	 *
	 * @return string
	 */
	public static function t($message, array $params = [])
	{
		return Craft::t('sproutforms', $message, $params);
	}

	public static function error($message)
	{
		Craft::error($message, __METHOD__);
	}

	public static function info($message)
	{
		Craft::info($message, __METHOD__);
	}

	public static function warning($message)
	{
		Craft::warning($message, __METHOD__);
	}

	/**
	 * @return array
	 */
	private function getCpUrlRules()
	{
		return [
			'sproutforms/forms/new'                                  =>
			'sprout-forms/forms/edit-form-template',

			'sproutforms/forms/edit/<formId:\d+>'                    =>
			'sprout-forms/forms/edit-form-template',

			'sproutforms/entries/edit/<entryId:\d+>'                 =>
			'sprout-forms/entries/edit-entry',

			'sproutforms/settings/(general|advanced)'                =>
			'sprout-forms/settings/settings-index-template',

			'sproutforms/settings/entrystatuses'                     =>
			'sprout-forms/entry-statuses/index',

			'sproutforms/settings/entrystatuses/new'                 =>
			'sprout-forms/entry-statuses/edit',

			'sproutforms/settings/entrystatuses/<entryStatusId:\d+>' =>
			'sprout-forms/entry-statuses/edit',

			'sproutforms/forms/<groupId:\d+>'                        =>
			'sprout-forms/forms',
		];
	}

	/**
	 * @throws \Exception
	 */
	public function beforeUninstall(): bool
	{
		$forms = SproutForms::$app->forms->getAllForms();

		foreach ($forms as $form)
		{
			SproutForms::$app->forms->deleteForm($form);
		}

		return true;
	}

	/**
	 * @return string
	 */
	public function defineTemplateComponent()
	{
		return SproutFormsVariable::class;
	}
}

