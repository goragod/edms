<?php
/**
 * @filesource modules/dms/views/settings.php
 *
 * @copyright 2016 Goragod.com
 * @license http://www.kotchasan.com/license/
 *
 * @see http://www.kotchasan.com/
 */

namespace Dms\Settings;

use Kotchasan\Html;
use Kotchasan\Language;
use Kotchasan\Text;

/**
 * module=dms-settings
 *
 * @author Goragod Wiriya <admin@goragod.com>
 *
 * @since 1.0
 */
class View extends \Gcms\View
{
    /**
     * ฟอร์มตั้งค่า
     *
     * @return string
     */
    public function render()
    {
        // form
        $form = Html::create('form', array(
            'id' => 'setup_frm',
            'class' => 'setup_frm',
            'autocomplete' => 'off',
            'action' => 'index.php/dms/model/settings/submit',
            'onsubmit' => 'doFormSubmit',
            'ajax' => true,
            'token' => true,
        ));
        $fieldset = $form->add('fieldset', array(
            'title' => '{LNG_Module settings}',
        ));
        // dms_format_no
        $fieldset->add('text', array(
            'id' => 'dms_format_no',
            'labelClass' => 'g-input icon-number',
            'itemClass' => 'item',
            'label' => '{LNG_Document number}',
            'comment' => '{LNG_Numeric code format, such as %YY%M%D-%04d (for example 25641231-0001)}',
            'placeholder' => 'DOC%Y%M%D-%04d',
            'value' => isset(self::$cfg->dms_format_no) ? self::$cfg->dms_format_no : 'DOC%Y%M%D-%04d',
        ));
        // dms_user_permission
        $fieldset->add('checkboxgroups', array(
            'id' => 'dms_user_permission',
            'labelClass' => 'g-input icon-list',
            'itemClass' => 'item',
            'label' => '{LNG_Permission}',
            'comment' => '{LNG_Default license When member registration}',
            'options' => \Dms\Init\Controller::updatePermissions(array()),
            'value' => isset(self::$cfg->dms_user_permission) ? self::$cfg->dms_user_permission : array(),
        ));
        $fieldset = $form->add('fieldset', array(
            'title' => '{LNG_Upload}',
        ));
        // dms_upload_options
        $fieldset->add('select', array(
            'id' => 'dms_upload_options',
            'labelClass' => 'g-input icon-menus',
            'itemClass' => 'item',
            'label' => '{LNG_Upload}',
            'options' => Language::get('DMS_UPLOAD_OPTIONS'),
            'value' => isset(self::$cfg->dms_upload_options) ? self::$cfg->dms_upload_options : 0,
        ));
        // dms_file_typies
        $fieldset->add('text', array(
            'id' => 'dms_file_typies',
            'labelClass' => 'g-input icon-file',
            'itemClass' => 'item',
            'label' => '{LNG_Type of file uploads}',
            'comment' => '{LNG_Specify the file extension that allows uploading. English lowercase letters and numbers 2-4 characters to separate each type with a comma (,) and without spaces. eg zip,rar,doc,docx}',
            'value' => isset(self::$cfg->dms_file_typies) ? implode(',', self::$cfg->dms_file_typies) : 'doc,ppt,pptx,docx,rar,zip,jpg,pdf',
        ));
        // dms_upload_size
        $sizes = array();
        foreach (array(2, 4, 6, 8, 16, 32, 64, 128, 256, 512, 1024, 2048) as $i) {
            $a = $i * 1048576;
            $sizes[$a] = Text::formatFileSize($a);
        }
        $fieldset->add('select', array(
            'id' => 'dms_upload_size',
            'labelClass' => 'g-input icon-upload',
            'itemClass' => 'item',
            'label' => '{LNG_Size of the file upload}',
            'comment' => '{LNG_The size of the files can be uploaded. (Should not exceed the value of the Server :upload_max_filesize.)}',
            'options' => $sizes,
            'value' => isset(self::$cfg->dms_upload_size) ? self::$cfg->dms_upload_size : ':upload_max_filesize',
        ));
        $fieldset = $form->add('fieldset', array(
            'title' => '{LNG_Download}',
        ));
        // dms_download_action
        $fieldset->add('select', array(
            'id' => 'dms_download_action',
            'labelClass' => 'g-input icon-download',
            'itemClass' => 'item',
            'label' => '{LNG_When download}',
            'options' => Language::get('DOWNLOAD_ACTIONS'),
            'value' => isset(self::$cfg->dms_download_action) ? self::$cfg->dms_download_action : 0,
        ));
        $fieldset = $form->add('fieldset', array(
            'class' => 'submit',
        ));
        // submit
        $fieldset->add('submit', array(
            'class' => 'button save large icon-save',
            'value' => '{LNG_Save}',
        ));
        \Gcms\Controller::$view->setContentsAfter(array(
            '/:upload_max_filesize/' => ini_get('upload_max_filesize'),
        ));
        // คืนค่า HTML
        return $form->render();
    }
}
