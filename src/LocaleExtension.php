<?php

namespace Derralf\FluentTweaks;

use SilverStripe\Forms\CheckboxField;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\TextField;
use SilverStripe\ORM\DataExtension;


class LocaleExtension extends DataExtension
{
    private static $db = [
        'Hidden' => 'Boolean',
        'Sort'   => 'Int'
    ];

//    private static $summary_fields = array(
//        'Hidden',
//        'Sort'
//    );

    public function updateCMSFields(FieldList $fields) {

        $Hidden = CheckboxField::create('Hidden', 'Hidden');
        $Hidden->setTitle(_t(__CLASS__.'.HiddenLabel', 'Hidden'));
        $Hidden->setDescription(_t(__CLASS__.'.HiddenDescription', 'Hide from Language Menu (published Pages/Contents will still be public/accessible)'));
        $fields->addFieldsToTab('Root.Main', $Hidden);

        $Sort = TextField::create('Sort', 'Sort');
        $Sort->setTitle(_t(__CLASS__.'.SortLabel', 'Sort Order'));
        $Sort->setDescription(_t(__CLASS__.'.SortDescription', 'Sort Order in Language Menu'));
        $fields->addFieldsToTab('Root.Main', $Sort);


		return $fields;
	}

	public function updateFieldLabels(&$labels) {
        $labels['Hidden'] = _t(__CLASS__.'.HiddenLabel', 'Hidden');
        $labels['Sort'] = _t(__CLASS__.'.SortLabel', 'Sort Order');
	}
}