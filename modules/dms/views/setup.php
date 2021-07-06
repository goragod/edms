<?php
/**
 * @filesource modules/dms/views/setup.php
 *
 * @copyright 2016 Goragod.com
 * @license http://www.kotchasan.com/license/
 *
 * @see http://www.kotchasan.com/
 */

namespace Dms\Setup;

use Kotchasan\DataTable;
use Kotchasan\Date;
use Kotchasan\Http\Request;
use Kotchasan\Language;

/**
 * module=dms-setup
 *
 * @author Goragod Wiriya <admin@goragod.com>
 *
 * @since 1.0
 */
class View extends \Gcms\View
{
    /**
     * @var object
     */
    private $category;

    /**
     * แสดงรายการเอกสารส่ง
     *
     * @param Request $request
     * @param array $login
     *
     * @return string
     */
    public function render(Request $request, $login)
    {
        // ค่าที่ส่งมา
        $params = array();
        foreach (Language::get('DMS_CATEGORIES') as $k => $label) {
            $params[$k] = $request->request($k)->toInt();
        }
        // department
        if ($login['status'] != 1 && !empty(self::$cfg->dms_upload_options)) {
            $params['member_id'] = $login['id'];
        }
        // หมวดหมู่
        $this->category = \Dms\Category\Model::init();
        // URL สำหรับส่งให้ตาราง
        $uri = $request->createUriWithGlobals(WEB_URL.'index.php');
        // ตาราง
        $table = new DataTable(array(
            /* Uri */
            'uri' => $uri,
            /* Model */
            'model' => \Dms\Setup\Model::toDataTable($params),
            /* รายการต่อหน้า */
            'perPage' => $request->cookie('dmsSetup_perPage', 30)->toInt(),
            /* เรียงลำดับ */
            'sort' => $request->cookie('dmsSetup_sort', 'create_date DESC')->toString(),
            /* ฟังก์ชั่นจัดรูปแบบการแสดงผลแถวของตาราง */
            'onRow' => array($this, 'onRow'),
            /* คอลัมน์ที่ไม่ต้องแสดงผล */
            'hideColumns' => array('id', 'url'),
            /* ตั้งค่าการกระทำของของตัวเลือกต่างๆ ด้านล่างตาราง ซึ่งจะใช้ร่วมกับการขีดถูกเลือกแถว */
            'action' => 'index.php/dms/model/setup/action',
            'actionCallback' => 'dataTableActionCallback',
            'actions' => array(
                array(
                    'id' => 'action',
                    'class' => 'ok',
                    'text' => '{LNG_With selected}',
                    'options' => array(
                        'delete' => '{LNG_Delete}',
                    ),
                ),
            ),
            /* คอลัมน์ที่สามารถค้นหาได้ */
            'searchColumns' => array('topic', 'document_no'),
            /* ส่วนหัวของตาราง และการเรียงลำดับ (thead) */
            'headers' => array(
                'create_date' => array(
                    'text' => '{LNG_Date}',
                    'sort' => 'create_date',
                ),
                'document_no' => array(
                    'text' => '{LNG_Document No.}',
                    'sort' => 'document_no',
                ),
                'topic' => array(
                    'text' => '{LNG_Document title}',
                    'sort' => 'topic',
                ),
            ),
            /* ฟังก์ชั่นตรวจสอบการแสดงผลปุ่มในแถว */
            'onCreateButton' => array($this, 'onCreateButton'),
            /* ปุ่มแสดงในแต่ละแถว */
            'buttons' => array(
                'files' => array(
                    'class' => 'icon-documents button brown',
                    'href' => $uri->createBackUri(array('module' => 'dms-files', 'id' => ':id')),
                    'text' => '{LNG_File}',
                ),
                'edit' => array(
                    'class' => 'icon-edit button green',
                    'href' => $uri->createBackUri(array('module' => 'dms-write', 'id' => ':id')),
                    'text' => '{LNG_Edit}',
                ),
            ),
            /* ปุ่มเพิ่ม */
            'addNew' => array(
                'class' => 'float_button icon-new',
                'href' => $uri->createBackUri(array('module' => 'dms-write')),
                'title' => '{LNG_Upload} {LNG_Document}',
            ),
        ));
        foreach (Language::get('DMS_CATEGORIES') as $k => $label) {
            $table->filters[] = array(
                'name' => $k,
                'text' => $label,
                'options' => array(0 => '{LNG_all items}') + $this->category->toSelect($k),
                'value' => $params[$k],
            );
            $table->headers[$k] = array(
                'text' => $label,
                'class' => 'center',
            );
            $table->cols[$k] = array(
                'class' => 'center',
            );
        }
        // save cookie
        setcookie('dmsSetup_perPage', $table->perPage, time() + 2592000, '/', HOST, HTTPS, true);
        setcookie('dmsSetup_sort', $table->sort, time() + 2592000, '/', HOST, HTTPS, true);
        // คืนค่า HTML
        return $table->render();
    }

    /**
     * จัดรูปแบบการแสดงผลในแต่ละแถว
     *
     * @param array $item
     *
     * @return array
     */
    public function onRow($item, $o, $prop)
    {
        $item['create_date'] = Date::format($item['create_date'], 'd M Y');
        return $item;
    }

    /**
     * ฟังกชั่นตรวจสอบว่าสามารถสร้างปุ่มได้หรือไม่
     *
     * @param array $item
     *
     * @return array
     */
    public function onCreateButton($btn, $attributes, $item)
    {
        if ($btn == 'files') {
            if ($item['url'] != '') {
                $attributes['href'] = $item['url'];
                $attributes['class'] = 'button blue icon-world';
                $attributes['text'] = '{LNG_URL}';
                $attributes['target'] = '_blank';
            }
        }
        return $attributes;
    }
}
