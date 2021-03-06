<?php

//avoid direct access to file
if (!defined('_PS_VERSION_')) {
    exit;
}

require_once 'classes/TerritoryModel.php';

class Territory extends Module
{
    /*
     * The models should extends CrudCustomObjectModel.
     * When installing, the module will create the relative to each model
     * in the database. If the table already exists, any missing coluns
     * in it will be added.
     */
    public $models = ['TerritoryModel'];

    //tabs to be created in the backoffice menu
    protected $tabs = [
        [
            'name'      => 'Territory',
            'className' => 'AdminTerritories',
            'active'    => 1,
            //'childs'    => [],
        ],
    ];

    public function __construct()
    {
        $this->name    = 'territory';
        $this->version = '1.1.0';

        //module category
        $this->tab = 'administration';
        
        $this->author     = 'Nascenia';
        $this->author_uri = 'http://www.nascenia.com';
        
        $this->ps_versions_compliancy = ['min' => '1.6', 'max' => '1.6.99'];

        $this->displayName = 'Territory';
        $this->description = 'Add company territories.';

        parent::__construct();
    }

    //add a tab in the backoffice menu
    public function addTab($tabs, $id_parent = 0) {
        foreach ($tabs as $tab) {
            $tabModel             = new Tab();
            $tabModel->module     = $this->name;
            $tabModel->active     = $tab['active'];
            $tabModel->class_name = $tab['className'];
            $tabModel->id_parent  = $id_parent;

            //tab text in each language
            foreach (Language::getLanguages(true) as $lang) {
                $tabModel->name[$lang['id_lang']] = $tab['name'];
            }

            $tabModel->add();

            //submenus of the tab
            if (isset($tab['childs']) && is_array($tab['childs'])) {
                $this->addTab($tab['childs'], Tab::getIdFromClassName($tab['className']));
            }
        }
        return true;
    }

    //remove a tab and its childrens from the backoffice menu
    public function removeTab($tabs) {
        foreach ($tabs as $tab) {
            $id_tab = (int) Tab::getIdFromClassName($tab["className"]);
            if ($id_tab) {
                $tabModel = new Tab($id_tab);
                $tabModel->delete();
            }

            if (isset($tab["childs"]) && is_array($tab["childs"])) {
                $this->removeTab($tab["childs"]);
            }
        }

        return true;
    }

    public function setConfiguration($is_territory = true) {
        Configuration::updateValue('IS_TERRITORY', $is_territory);
    }

    public function install() {
        foreach ($this->models as $model) {
        	require_once 'classes/' . $model . '.php';
            //instantiate the module
            $modelInstance = new $model();

            //create the table relative to this model in the database
            //if the table does not exists yet
            $modelInstance->createDatabase();

            //if the table already exists, add to it any column that may be missing.
            //this is useful in the case of new updates that require new columns
            //to exist in the table.
            $modelInstance->createMissingColumns();
        }

        //module installation
        $success = parent::install();

        //if the installation fails, return error
        if (!$success) {
            return false;
        }

        //create the tabs in the backoffice menu
        $this->addTab($this->tabs, (int)Tab::getIdFromClassName('AdminAdmin'));

        $this->setConfiguration(true);

        return true;
    }

    public function uninstall() {
        $this->removeTab($this->tabs);

        foreach ($this->models as $model) {
            require_once 'classes/' . $model . '.php';
            //instantiate the module
            $modelInstance = new $model();

            //remove the table relative to this model in the database
            $modelInstance->dropDatabase();
        }

        $this->setConfiguration(true);

        return parent::uninstall();
    }

    public static function getTerritories() {
        return TerritoryModel::getAll();
    }
}
