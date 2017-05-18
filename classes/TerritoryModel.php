<?php
require_once 'CustomObjectModel.php';

class TerritoryModel extends TerritoryCustomObjectModel
{
    public static $definition = [
        'table'     => 'territory',
        'primary'   => 'id_territory',
        'multilang' => false,
        'fields'    => [
            'id_territory' => ['type' => self::TYPE_INT, 'validate' => 'isInt'],
            'name'         => ['type' => self::TYPE_STRING, 'db_type' => 'varchar(255)'],
            'description'  => ['type' => self::TYPE_STRING, 'db_type' => 'varchar(255)'],
            'active'       => ['type' => self::TYPE_BOOL, 'validate' => 'isBool', 'db_type' => 'int'],
            'date_add'      => [
                'type'     => self::TYPE_DATE,
                'validate' => 'isDate',
                'db_type'  => 'datetime',
            ],
            'date_upd'      => [
                'type'     => self::TYPE_DATE,
                'validate' => 'isDate',
                'db_type'  => 'datetime',
            ],
        ],
        'relation_tables'  => ['employee', 'customer']
    ];

    public $id_territory;
    public $name;
    public $description;
    public $active;
    public $date_add;
    public $date_upd;

    public static function getAll() {
            return Db::getInstance()->executeS('
			SELECT `id_territory`, `name`, `description`
			FROM `' ._DB_PREFIX_. 'territory` t
			WHERE `active` = 1
			ORDER BY `name` ASC
		');
    }

    public static function getTerritoryWithId($id_territory) {
        return Db::getInstance()->executeS('
			SELECT `id_territory`, `name`, `description`
			FROM `' ._DB_PREFIX_. 'territory` t
			WHERE `active` = 1 AND t.`id_territory` = '.$id_territory.'
			ORDER BY `name` ASC
		');
    }
}
