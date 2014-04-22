<?php
/**
 * FormModel class file.
 * @author Pavel Bariev <bariew@yandex.ru>
 * @copyright (c) 2013, Moqod
 * @license http://www.opensource.org/licenses/bsd-license.php
 */

/**
 * base class for form models
 * @package application.models
 */
class FormModel extends CFormModel
{
    /**
     * imitates ActiveRecord getSafeAttributes method
     * @return array safe attribute values
     */
    public function getSafeAttributes()
    {
        return $this->attributes;
    }
}
