<?php
/**
 * This behavior provides "Cake Magic" type of access to htmlpurifier
 * Copyright 2011-2014, Jason D Snider (https://jasonsnider.com)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright 2012, Jason D Snider (https://jasonsnider.com)
 * @link https://jasonsnider.com
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
App::uses('Scrub', 'Jsc.Lib');
App::uses('Sanitize', 'Utility');

/**
 * This behavior provides "Cake Magic" type of access to htmlpurifier
 * @package Jsc
 * @author Jason D Snider <root@jasonsnider.com>
 */
class ScrubableBehavior extends ModelBehavior {

/**
 * Execute after __constructor
 * @param object $model
 * @param array $settings
 * @access public
 */
    public function setup(Model $model, $settings = array()) {
        if (!is_array($settings)) {
            $settings = array();
        }

		$override = Configure::read("Jsc.Scrubable.{$model->name}");
		if(!empty($override)){
			$settings['Filters'] = array_merge(
				$settings['Filters'], 
				Configure::read("Jsc.Scrubable.{$model->name}")
			);
		}
		
        $this->settings[$model->name] = $settings;
    }

/**
 * Pre-save data manipulation
 * @param object $model
 * @param array $options
 * @return boolean
 */
    public function beforeSave(Model $model, $options = array()) {
				
        //Determine which filters are requested and against what fields
        //Then build a filter map
        foreach ($this->settings[$model->name]['Filters'] as $key => $value) {
            $currentFilter = $key;
            if ($value == '*') {
                $filterMap[$currentFilter] = array_keys($model->data[$model->name]);
            } else {
                $filterMap[$currentFilter] = $value;
            }
        }


        foreach ($filterMap as $key => $value) {

            //Parse the filter map and apply the filters accordingly
            for ($i = 0; $i < count($value); $i++) {

                //echo 'Applying ' . $key . ' to ' . $value[$i] .'<br>';

                if (
                        isset($model->data[$model->name][$value[$i]]) &&
                        !is_array($model->data[$model->name][$value[$i]])
                ) {
                    //The data
                    $model->data[$model->name][$value[$i]]
                            //The filter
                            = $this->{$key}($model->data[$model->name][$value[$i]]);
                }
            }
        }

        return true;
    }

/**
 * A wrapper method for trim()
 * @param string $value
 * @return string
 */
    public function trim($value) {
        return trim($value);
    }

/**
 * A wrapper for Scrub.html
 * @param string $value
 * @return string
 */
    public function html($value) {
        return Scrub::html($value);
    }

/**
 * A wrapper for Scrub.htmlMedia
 * @param string $value
 * @return string
 */
    public function htmlMedia($value) {
        return Scrub::htmlMedia($value);
    }

/**
 * A wrapper for Scrub.htmlStrict
 * @param string $value
 * @return string
 */
    public function htmlStrict($value) {
        return Scrub::htmlStrict($value);
    }

/**
 * A wrapper for Scrub.safe
 * @param string $value
 * @return string
 */
    public function safe($value) {
        return Scrub::safe($value);
    }

/**
 * A wrapper for Scrub.noHTML
 * @param string $value
 * @return string
 */
    public function noHTML($value) {
        return Scrub::noHTML($value);
    }

/**
 * A wrapper for Scrub.phoneNumber
 * @param string $value
 * @return string
 */
    public function phoneNumber($value) {
        return Scrub::phoneNumber($value);
    }

/**
 * A wrapper for Scrub.lower()
 * @param string $value
 * @return string
 */
    public function lower($value) {
        return Scrub::lower($value);
    }

}