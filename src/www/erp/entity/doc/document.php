<?php

namespace ZippyERP\ERP\Entity\Doc;

/**
 * Класс-сущность документ
 *
 */
class Document extends \ZCL\DB\Entity
{

        const STATE_NEW = 1;     //Новый
        const STATE_EDITED = 2;  //Отредактирован
        const STATE_CANCELED = 3;      //Отменен
        const STATE_EXECUTED = 4;      //Выполнен
        const STATE_APROOVED = 5;      //  Утвержден
        const STATE_DELETED = 6;       //  Удален

        /**
         * Ассоциативный массив   с атрибутами заголовка  документа
         * 
         * @var mixed
         */

        public $headerdata = array();

        /**
         * Массив  ассоциативных массивов (строк) содержащих  строки  детальной части (таблицы) документа
         * 
         * @var mixed
         */
        public $detaildata = array();

        protected function init()
        {
                $this->document_id = 0;
                $this->state = 0;
                $this->created = time();
                $this->user_id = \ZippyERP\System\System::getUser()->user_id;
        }

        protected static function getMetadata()
        {
                return array('table' => 'erp_document', 'view' => 'erp_document_view', 'keyfield' => 'document_id');
        }

        protected function afterLoad()
        {
                $this->created = strtotime($this->created);
                $this->document_date = strtotime($this->document_date);
                $this->unpackData();
        }

        protected function beforeSave()
        {

                $this->packData();
        }

        /**
         * Упаковка  данных  в  XML
         * 
         */
        private function packData()
        {

                $this->content = "<doc><header>";

                foreach ($this->headerdata as $key => $value) {
                        if (strlen($value) > 10) {
                                $value = "<![CDATA[" . $value . "]]>";
                        }
                        $this->content .= "<{$key}>{$value}</{$key}>";
                }
                $this->content .= "</header><detail>";
                foreach ($this->detaildata as $row) {
                        $this->content .= "<row>";
                        foreach ($row as $key => $value) {
                                if (strlen($value) > 10) {
                                        $value = "<![CDATA[" . $value . "]]>";
                                }
                                $this->content .= "<{$key}>{$value}</{$key}>";
                        }

                        $this->content .= "</row>";
                }
                $this->content .= "</detail></doc>";
        }

        /**
         * распаковка из  XML 
         * 
         */
        private function unpackData()
        {
                $this->headerdata = array();
                $xml = new \SimpleXMLElement($this->content);
                foreach ($xml->header->children() as $child) {
                        $this->headerdata[(string) $child->getName()] = (string) $child;
                }
                $this->detaildata = array();
                foreach ($xml->detail->children() as $row) {
                        $_row = array();
                        foreach ($row->children() as $item) {
                                $_row[(string) $item->getName()] = (string) $item;
                        }
                        $this->detaildata[] = $_row;
                }
        }

        /**
         * Генерация HTML  для  печатной формы
         * 
         */
        public function generateReport()
        {
                return "";
        }

        /**
         * Обработка  данных  документа (обновление  склада, бухгалтерские проводки и  т.д.)
         * 
         */
        public function Execute()
        {
                if ($this->state == self::STATE_EXECUTED)
                        return false;
                $this->updateLog(self::STATE_EXECUTED);
                return $this->ExecuteImpl();
        }

        /**
         * Отмена докумета
         * 
         */
        public function Cancel()
        {
                if ($this->state != self::STATE_EXECUTED)
                        return false;
                $this->updateLog(self::STATE_CANCELED);
                return $this->CancelImpl();
        }

        /**
         * Имплементация  выполнения  наследниками
         * 
         */
        protected function ExecuteImpl()
        {
                return true;
        }

        /**
         * Имплементация  отмены  наследниками
         *       
         */
        protected function CancelImpl()
        {
                $conn = \ZCL\DB\DB::getConnect();

                $sql = "delete from erp_stock_activity where document_id =" . $this->document_id;
                $conn->Execute($sql);
                $sql = "delete from erp_account_entry where document_id =" . $this->document_id;
                $conn->Execute($sql);


                //todo  серийные  номера

                return true;
        }

        public static function create($classname)
        {
                $arr = explode("\\", $classname);
                $classname = $arr[count($arr) - 1];
                $conn = \ZCL\DB\DB::getConnect();
                $sql = "select meta_id from  erp_metadata where meta_type=1 and meta_name='{$classname}'";
                $meta = $conn->GetRow($sql);
                $doc = new Document();
                $doc->type_id = $meta['meta_id'];
                return $doc;
        }

        public function updateLog($state)
        {
                $user = \ZippyERP\System\System::getUser()->getUserID();
                $conn = \ZCL\DB\DB::getConnect();
                $sql = "insert into erp_document_update_log (document_id,user_id,document_state,updatedon) values ({$this->document_id},{$user},{$state},now())";
                $conn->Execute($sql);
        }

        protected function beforeDelete()
        {
                $conn = \ZCL\DB\DB::getConnect();
                $sql = "delete from erp_document_update_log  where document_id =" . $this->document_id;
                $conn->Execute($sql);

                return true;
        }

        protected function afterSave($update)
        {
                $this->updateLog($update == true ? self::STATE_EDITED : self::STATE_NEW );
        }

}

