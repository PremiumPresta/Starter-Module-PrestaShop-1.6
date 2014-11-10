<?php
/**
 * Starter Module
 * 
 *  @author    PremiumPresta <office@premiumpresta.com>
 *  @copyright 2014 PremiumPresta
 *  @license   http://creativecommons.org/licenses/by-nd/4.0/ CC BY-ND 4.0
 */

!defined('_PS_VERSION_') && exit;

class StarterPsModule extends Module
{

	public function __construct()
	{
		$this->name = 'starterpsmodule'; // internal identifier, unique and lowercase
		$this->tab = 'front_office_features'; // backend module coresponding category
		$this->version = '1.0.0'; // version number for the module
		$this->author = 'PremiumPresta'; // module author
		$this->need_instance = 0; // load the module when displaying the "Modules" page in backend
		$this->bootstrap = true;

		parent::__construct();

		$this->displayName = $this->l('Starter PrestaShop Module'); // public name
		$this->description = $this->l('Starter Module for PrestaShop 1.6.x'); // public description

		$this->confirmUninstall = $this->l('Are you sure you want to uninstall?'); // confirmation message at uninstall

		$this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
	}

	/**
	 * Install this module
	 * @return boolean
	 */
	public function install()
	{
		return parent::install() &&
			$this->initConfig() &&
			$this->registerHook('displayHeader') &&
			$this->registerHook('displayHome');
	}

	/**
	 * Uninstall this module
	 * @return boolean
	 */
	public function uninstall()
	{
		return Configuration::deleteByName($this->name) &&
			parent::uninstall();
	}

	/**
	 * Set the default configuration
	 * @return boolean
	 */
	protected function initConfig()
	{
		$languages = Language::getLanguages(false);
		$config = array();

		foreach ($languages as $lang)
		{
			$config['quote'][$lang['id_lang']] = 'The secret of getting ahead is getting started. The secret of getting started is breaking your complex overwhelming tasks into small manageable tasks, and then starting on the first one.';
			$config['author'][$lang['id_lang']] = 'Mark Twain';
		}
		$config['show_author'] = true;

		return Configuration::updateValue($this->name, Tools::jsonEncode($config));
	}

	/**
	 * Add css and javascript to back office head
	 * @param array $params 
	 */
	public function headBackOfficeScripts()
	{
		$this->context->controller->addJS($this->_path.'js/back.js');
		$this->context->controller->addCSS($this->_path.'css/back.css');
	}

	/**
	 * Configuration page
	 */
	public function getContent()
	{
		$this->headBackOfficeScripts();
		return $this->_postProcess().$this->renderForm();
	}

	/**
	 * Create the structure of your form.
	 */
	protected function getConfigForm()
	{
		return array(
			'form' => array(
				'legend' => array(
					'title' => $this->l('Starter PrestaShop Module'),
					'icon' => 'icon-cogs'
				),
				'input' => array(
					array(
						'label' => $this->l('Quote'),
						'name' => 'quote',
						'type' => 'text',
						'lang' => true,
						'required' => true
					),
					array(
						'label' => $this->l('Author'),
						'name' => 'author',
						'type' => 'text',
						'lang' => true,
						'class' => 'fixed-width-lg',
					),
					array(
						'label' => $this->l('Show Author'),
						'name' => 'show_author',
						'type' => 'switch',
						'is_bool' => true,
						'values' => array(
							array(
								'id' => 'active_on',
								'value' => 1,
								'label' => $this->l('Yes')
							),
							array(
								'id' => 'active_off',
								'value' => 0,
								'label' => $this->l('No')
							)
						),
					),
				),
				'submit' => array(
					'name' => 'saveBtn',
					'title' => $this->l('Save'),
					'class' => 'btn btn-success pull-right'
				)
			)
		);
	}

	/**
	 * Save form data.
	 */
	protected function _postProcess()
	{
		if (Tools::isSubmit('saveBtn'))
		{
			$languages = Language::getLanguages();
			$config = array();

			foreach ($languages as $lang)
			{
				$config['quote'][$lang['id_lang']] = Tools::getValue('quote_'.$lang['id_lang']);
				$config['author'][$lang['id_lang']] = Tools::getValue('author_'.$lang['id_lang']);
			}
			$config['show_author'] = Tools::getValue('show_author');
			Configuration::updateValue($this->name, Tools::jsonEncode($config));

			return $this->displayConfirmation($this->l('Settings updated'));
		}
	}

	/**
	 * Create the form that will be displayed in the configuration of your module.
	 */
	protected function renderForm()
	{
		$helper = new HelperForm();

		$helper->show_toolbar = false;
		$helper->table = $this->table;
		$helper->module = $this;
		$helper->default_form_language = $this->context->language->id;
		$helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);

		$helper->identifier = $this->identifier;
		$helper->submit_action = 'submitQuickcontactModule';
		$helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
			.'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
		$helper->token = Tools::getAdminTokenLite('AdminModules');

		$helper->tpl_vars = array(
			'fields_value' => $this->getConfigFormValues(), /* Add values for your inputs */
			'languages' => $this->context->controller->getLanguages(),
			'id_language' => $this->context->language->id,
		);

		return $helper->generateForm(array($this->getConfigForm()));
	}

	/**
	 * Set values for the inputs.
	 */
	public function getConfigFormValues()
	{
		return Tools::jsonDecode(Configuration::get($this->name), true);
	}

	/**
	 * Add css and javascript to head
	 * @param array $params 
	 */
	public function headScrips()
	{
		$this->context->controller->addCSS($this->_path.'front.css');
		$this->context->controller->addJS($this->_path.'front.js');
	}

	/**
	 * Homepage content hook (Technical name: displayHome)
	 */
	public function hookDisplayHome($params)
	{
		$this->headScrips();

		!isset($params['tpl']) && $params['tpl'] = 'hookHome';

		$config = Tools::jsonDecode(Configuration::get($this->name), true);
		$this->smarty->assign($config);

		return $this->display(__FILE__, $params['tpl'].'.tpl');
	}

}
