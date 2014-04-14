<?php

/*
 * BitSHOK Starter Module
 * 
 * @author BitSHOK <office@bitshok.net>
 * @copyright 2014 BitSHOK
 * @version 0.1
 * @license http://creativecommons.org/licenses/by/3.0/ CC BY 3.0
 */

if (!defined('_PS_VERSION_'))
    exit;

class StarterPsModule extends Module {

    public function __construct() {
        $this->name = 'starterpsmodule'; // internal identifier, unique and lowercase
        $this->tab = ''; // backend module coresponding category - optional since v1.6
        $this->version = '0.1'; // version number for the module
        $this->author = 'BitSHOK'; // module author
        $this->need_instance = 0; // load the module when displaying the "Modules" page in backend

        $this->bootstrap = true;
        parent::__construct();

        $this->displayName = $this->l('Starter PrestaShop Module'); // public name
        $this->description = $this->l('Starter Module for PrestaShop 1.6.x'); // public description

        $this->confirmUninstall = $this->l('Are you sure you want to uninstall?'); // confirmation message at uninstall
    }

    /**
     * Install this module
     * @return boolean
     */
    public function install() {
        return  parent::install() &&
                $this->initConfig() &&
                $this->registerHook('displayHeader') &&
                $this->registerHook('displayHome');
    }

    /**
     * Uninstall this module
     * @return boolean
     */
    public function uninstall() {
        return  Configuration::deleteByName($this->name) &&
                parent::uninstall();
    }
    
    /**
     * Set the default configuration
     * @return boolean
     */
    protected function initConfig() {
        $languages = Language::getLanguages(false);
        $config = array();

        foreach ($languages as $lang) {
            $config['quote'][$lang['id_lang']] = 'The secret of getting ahead is getting started. The secret of getting started is breaking your complex overwhelming tasks into small manageable tasks, and then starting on the first one.';
            $config['author'][$lang['id_lang']] = 'Mark Twain';
        }
        
        return Configuration::updateValue($this->name, json_encode($config));
    }

    /**
     * Header of pages hook (Technical name: displayHeader)
     */
    public function hookHeader() {
        $this->context->controller->addCSS($this->_path . 'style.css');
        $this->context->controller->addJS($this->_path . 'script.js');
    }

    /**
     * Homepage content hook (Technical name: displayHome)
     */
    public function hookDisplayHome() {
        $config = json_decode(Configuration::get($this->name), true);
        $this->smarty->assign(array(
            'quote' => $config['quote'][$this->context->language->id],
            'author' => $config['author'][$this->context->language->id]
        ));

        return $this->display(__FILE__, $this->name . '.tpl');
    }

    /**
     * Configuration page
     */
    public function getContent() {
        return $this->postProcess() . $this->renderForm();
    }
    
    /*
     * Configuration page form builder
     */
    public function renderForm() {
        $fields_form = array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('Starter PrestaShop Module'),
                    'icon' => 'icon-cogs'
                ),
                'input' => array(
                    array(
                        'label' => $this->l('Quote'),
                        'type'  => 'text',
                        'lang'  => true,
                        'name'  => 'quote'
                    ),
                    array(
                        'label' => $this->l('Author'),
                        'type'  => 'text',
                        'lang'  => true,
                        'name'  => 'author'
                    ),
                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                    'class' => 'button pull-right'
                )
            )
        );
        
        
        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $lang = new Language((int) Configuration::get('PS_LANG_DEFAULT'));
        $helper->default_form_language = $lang->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
        $this->fields_form = array();
        $helper->module = $this;
        $helper->identifier = $this->identifier;
        $helper->submit_action = 'saveBtn';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
                . '&configure=' . $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->tpl_vars = array(
            'fields_value' => $this->getConfigFieldsValues(),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id
        );

        return $helper->generateForm(array($fields_form));
    }
    
    /*
     * Process data from Configuration page after form submition.
     */
    public function postProcess() {
        if (Tools::isSubmit('saveBtn')) {
            $languages = Language::getLanguages();
            $config = array();

            foreach ($languages as $lang) {
                $config['quote'][$lang['id_lang']] = Tools::getValue('quote_'.$lang['id_lang']);
                $config['author'][$lang['id_lang']] = Tools::getValue('author_'.$lang['id_lang']);
            }
            Configuration::updateValue($this->name, json_encode($config));
            
            return $this->displayConfirmation($this->l('Settings updated'));
        }
    }
    
    /**
     *  Display input values into the form after process
     */
    public function getConfigFieldsValues() {
        return json_decode(Configuration::get($this->name), true);
    }

}
