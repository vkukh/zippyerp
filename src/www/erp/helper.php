<?

namespace ZippyERP\ERP;

use \ZCL\DB\DB;

/**
 * Класс   со  вспомагательными   функциями
 *   для  работы с  БД 
 */
class Helper
{

        /**
         * Возвращает  роли  с  парвами  доступа   к  
         */
        public static function getRoleAccess($meta_id)
        {
                $conn = \ZCL\DB\DB::getConnect();
                $roles = \ZippyERP\System\Role::find();

                foreach (array_keys($roles) as $role_id) {

                        $row = $conn->GetRow("select * from erp_metadata_access where metadata_id ={$meta_id} and role_id={$role_id}");
                        if (is_array($row)) {
                                $roles[$role_id]->viewacc = $row['viewacc'];
                                $roles[$role_id]->editacc = $row['editacc'];
                                $roles[$role_id]->deleteacc = $row['deleteacc'];
                                $roles[$role_id]->execacc = $row['execacc'];
                        }
                }

                return $roles;
        }

        public static function updateRoleAccess($meta_id, $rows)
        {
                $conn = \ZCL\DB\DB::getConnect();
                $conn->Execute("delete from erp_metadata_access where metadata_id ={$meta_id} ");

                foreach ($rows as $row) {
                        $item = $row->getDataItem();
                        $conn->Execute("insert  into erp_metadata_access (metadata_id,role_id,viewacc,editacc,deleteacc,execacc) values ({$meta_id},{$item->role_id}," . ($item->viewacc ? 1 : 0) . "," . ($item->editacc ? 1 : 0) . "," . ($item->deleteacc ? 1 : 0) . "," . ($item->execacc ? 1 : 0) . ") ");
                }
        }

        /**
         * Генерация  иеню  для  типа  метаданных
         * 
         * @param mixed $meta_type
         */
        public static function generateMenu($meta_type)
        {
                $conn = \ZCL\DB\DB::getConnect();
                $rows = $conn->Execute("select *  from erp_metadata where meta_type= {$meta_type} order  by  description ");
                $menu = array();
                $groups = array();

                foreach ($rows as $meta_id => $meta_object) {
                        if (strlen($meta_object['menugroup']) == 0) {
                                $menu[$meta_id] = $meta_object;
                        } else {
                                if (!isset($groups[$meta_object['menugroup']])) {
                                        $groups[$meta_object['menugroup']] = array();
                                }
                                $groups[$meta_object['menugroup']][$meta_id] = $meta_object;
                        }
                }
                switch ($meta_type) {
                        case 1 : $dir = "Doc";
                                break;
                        case 2 : $dir = "Report";
                                break;
                        case 3 : $dir = "Register";
                                break;
                        case 4 : $dir = "Reference";
                                break;
                }
                $textmenu = "";
                foreach ($menu as $item) {
                        $textmenu .= "<li><a href=\"/?p=ZippyERP/ERP/Pages/{$dir}/{$item['meta_name']}\">{$item['description']}</a></li>";
                }
                foreach ($groups as $gname => $group) {
                        $textmenu .= "<li class=\"dropdown-submenu\"><a tabindex=\"-1\" href=\"#\">$gname</a><ul class=\"dropdown-menu\">";

                        foreach ($group as $item) {
                                $textmenu .= "<li><a href=\"/?p=ZippyERP/ERP/Pages/{$dir}/{$item['meta_name']}\">{$item['description']}</a></li>";
                        }
                        $textmenu .= "</ul></li>";
                }

                return $textmenu;
        }

        public static function getDocGroups()
        {
                $conn = \ZCL\DB\DB::getConnect();
                $groups = array();

                $rs = $conn->Execute('SELECT distinct menugroup FROM  erp_metadata');
                foreach ($rs as $row) {
                        if (strlen($row['menugroup']) > 0) {
                                $groups[$row['menugroup']] = $row['menugroup'];
                        }
                }
                return $groups;
        }

        public static function getMeasureList()
        {
                $list = array();
                $conn = DB::getConnect();
                $sql = "select measure_id,measure_name from  erp_item_measures ";
                $rs = $conn->Execute($sql);
                foreach ($rs as $row) {
                        $list[$row["measure_id"]] = $row["measure_name"];
                }

                return $list;
        }
        
        public static function getTypeList()
        {
                $list = array();
                $list[Consts::ITEM_TYPE_GOODS] = 'Товар';
                $list[Consts::ITEM_TYPE_MBP] = 'МБП';
                $list[Consts::ITEM_TYPE_SERVICE] = 'Услуги';
                
                return $list;
        }

        public static function getMetaType($id)
        {

                $conn = DB::getConnect();
                $sql = "select * from  erp_metadata where meta_id = " . $id;
                return $conn->GetRow($sql);
        }

}
