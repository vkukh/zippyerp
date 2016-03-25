<?php

namespace ZippyERP\ERP\Entity\Doc;

use \ZippyERP\ERP\Helper;

/**
 * Класс-сущность документ
 *
 */
class Document extends \ZCL\DB\Entity
{

    // состояния  документа
    const STATE_NEW = 1;     //Новый
    const STATE_EDITED = 2;  //Отредактирован
    const STATE_CANCELED = 3;      //Отменен
    const STATE_EXECUTED = 5;      // Проведен
    const STATE_APPROVED = 4;      //  Утвержден
    const STATE_DELETED = 6;       //  Удален
    const STATE_WORK = 7; // в  работе
    const STATE_WA = 8; // ждет подтверждения
    const STATE_CLOSED = 9; // Закрыт
    const STATE_WP = 10; // Ждет оплату
    const STATE_INSHIPMENT = 11; // Отгружен
    // типы  экспорта
    const EX_WORD = 1; //  Word
    const EX_EXCEL = 2;    //  Excel
    //const EX_PDF = 3;    //  PDF
    const EX_XML_GNAU = 4;

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
        $this->basedoc = '';
        $this->document_number = '';
        $this->created = time();
        $this->document_date = time();
        $this->user_id = \ZippyERP\System\System::getUser()->user_id;
        $this->headerdata = array();
    }

    protected static function getMetadata()
    {
        return array('table' => 'erp_document', 'view' => 'erp_document_view', 'keyfield' => 'document_id');
    }

    protected function afterLoad()
    {
        $this->created = strtotime($this->created);
        $this->updated = strtotime($this->updated);
        $this->document_date = strtotime($this->document_date);
        $this->unpackData();
    }

    protected function beforeSave()
    {
        $this->document_number = trim($this->document_number);
        $this->packData();

        //todo  отслеживание  изменений
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
                $value = $value === true ? 'true' : $value;
                $value = $value === false ? 'false' : $value;
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
        if ($this->content == null || strlen($this->content) == 0) {
            return;
        }
        $xml = new \SimpleXMLElement($this->content);
        foreach ($xml->header->children() as $child) {
            $this->headerdata[(string) $child->getName()] = (string) $child;
        }
        $this->detaildata = array();
        foreach ($xml->detail->children() as $row) {
            $_row = array();
            foreach ($row->children() as $item) {
                $_row[(string) $item->getName()] = (string) $item;
                if (((string) $item) == 'true') {
                    $_row[(string) $item->getName()] = true;
                }
                if (((string) $item) == 'false') {
                    $_row[(string) $item->getName()] = false;
                }
                if (is_integer((string) $item)) {
                    $_row[(string) $item->getName()] = (int) $item;
                }
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
     * Выполнение документа - обновление склада, бухгалтерские проводки и  т.д.
     *
     */
    public function Execute()
    {

        if (trim(get_class($this), "\\") == 'ZippyERP\ERP\Entity\Doc\Document') {
            //если  екземпляр  базового типа Document приводим  к  дочернему  типу
            return $this->cast()->Execute();
        }
    }

    /**
     * Отмена  документа
     *
     */
    protected function Cancel()
    {
        $conn = \ZCL\DB\DB::getConnect();
        $conn->StartTrans();
        // если  метод не переопределен  в  наследнике удаляем  документ  со  всех  движений
        //   $conn->Execute("delete from erp_stock_activity where document_id =" . $this->document_id);
        $conn->Execute("delete from erp_account_entry where document_id =" . $this->document_id);
        //   $conn->Execute("delete from erp_moneyfunds_activity  where document_id =" . $this->document_id);
        //   $conn->Execute("delete from erp_customer_activity  where document_id =" . $this->document_id);
        //  $conn->Execute("delete from erp_staff_employee_activity   where document_id =" . $this->document_id);
        $conn->Execute("delete from erp_account_subconto   where document_id =" . $this->document_id);
        $conn->Execute("delete from erp_document_update_log   where document_id =" . $this->document_id);
        $conn->CompleteTrans();

        //todo  серийные  номера

        return true;
    }

    /**
     * создает  экземпляр  класса  документа   в   соответсии  с  именем  типа
     *
     * @param mixed $classname
     */
    public static function create($classname)
    {
        $arr = explode("\\", $classname);
        $classname = $arr[count($arr) - 1];
        $conn = \ZCL\DB\DB::getConnect();
        $sql = "select meta_id from  erp_metadata where meta_type=1 and meta_name='{$classname}'";
        $meta = $conn->GetRow($sql);
        $classname = '\ZippyERP\ERP\Entity\Doc\\' . $classname;
        $doc = new $classname();
        $doc->type_id = $meta['meta_id'];
        return $doc;
    }

    /**
     * Приведение  типа  документа
     */
    public function cast()
    {

        $type = Helper::getMetaType($this->type_id);
        $class = "\\ZippyERP\\ERP\\Entity\\Doc\\" . $type['meta_name'];
        $doc = new $class($this->getData());
        $doc->unpackData();
        return $doc;
    }

    protected function beforeDelete()
    {
        $conn = \ZCL\DB\DB::getConnect();
        $conn->Execute("delete from erp_document_update_log  where document_id =" . $this->document_id);
        $conn->Execute("update erp_document set  state=" . self::STATE_DELETED . " where  document_id =" . $this->document_id);

        return true;
    }

    protected function afterSave($update)
    {

        //  if ($update == false) {   //новый  документ
        //    $this->updateStatus(self::STATE_NEW);
        // }
        // else {
        //    if ($this->state == self::STATE_NEW)
        //    $this->updateStatus(self::STATE_EDITED);
        //  }
    }

    /**
     * добавление связанного  документа
     *
     * @param mixed $id
     */
    public function AddConnectedDoc($id)
    {
        if ($id > 0) {
            $conn = \ZCL\DB\DB::getConnect();
            $conn->Execute("delete from erp_docrel  where (doc1={$this->document_id} and doc2={$id} )  or (doc2={$this->document_id} and doc1={$id})");
            $conn->Execute("insert  into erp_docrel (doc1,doc2) values({$id},{$this->document_id})");
        }
    }

    /**
     * удаление  связанного  документа
     *
     * @param mixed $id
     */
    public function RemoveConnectedDoc($id)
    {
        if ($id > 0) {
            $conn = \ZCL\DB\DB::getConnect();
            $conn->Execute("delete from erp_docrel  where (doc1={$this->document_id} and doc2={$id} )  or (doc2={$this->document_id} and doc1={$id})");
        }
    }

    /**
     * список  связанных  документов
     *
     */
    public function ConnectedDocList()
    {

        $where = "document_id in (select doc1 from erp_docrel where doc2={$this->document_id}) or document_id in (select doc2 from erp_docrel where doc1={$this->document_id})";
        return Document::find($where);
    }

    /**
     * список записей   в  логе   состояний
     *
     */
    public function getLogList()
    {


        $conn = \ZCL\DB\DB::getConnect();
        $rs = $conn->Execute("select l.*,u.userlogin from erp_document_update_log l left join system_users u on l.user_id = u.user_id where document_id={$this->document_id}");
        $list = array();
        foreach ($rs as $row) {
            $item = new \ZippyERP\ERP\DataItem();
            $item->hostname = $row['hostname'];
            $item->updatedon = date('Y-m-d H:i', strtotime($row['updatedon']));
            $item->user = $row['userlogin'];

            $item->state = self::getStateName($row['document_state']);
            $list[] = $item;
        }

        return $list;
    }

    /**
     * Обновляет состояние  документа
     *
     * @param mixed $state
     */
    public function updateStatus($state)
    {


        if ($this->state == $state)
            return false;
        if ($this->document_id == 0)
            return false;

        if ($state == self::STATE_CANCELED) {
            $this->Cancel();
        }
        if ($state == self::STATE_EXECUTED) {
            $this->Execute();
        }
        $this->state = $state;

        $conn = \ZCL\DB\DB::getConnect();
        $host = $conn->qstr($_SERVER["REMOTE_ADDR"]);
        $user = \ZippyERP\System\System::getUser()->getUserID();
        $sql = "insert into erp_document_update_log (document_id,user_id,document_state,updatedon,hostname) values ({$this->document_id},{$user},{$this->state},now(),{$host})";
        $conn->Execute($sql);
        $sql = "update erp_document set  state={$this->state},updated=now() where document_id = {$this->document_id}";
        $conn->Execute($sql);
        return true;
    }

    /**
     * Возвращает название  статуса  документа
     *
     * @param mixed $state
     * @return mixed
     */
    public static function getStateName($state)
    {

        switch ($state) {
            case Document::STATE_NEW: return "Новый";
            case Document::STATE_EDITED: return "Отредактирован";
            case Document::STATE_CANCELED: return "Отменен";
            case Document::STATE_EXECUTED: return "Проведен";
            case Document::STATE_CLOSED: return "Закрыт";
            case Document::STATE_APPROVED: return "Утвержден";
            case Document::STATE_DELETED: return "Удален";
            case Document::STATE_WP: return "Ожидает оплату";
            case Document::STATE_WA: return "Ждет утверждения";
            default: return "Неизвестный статус";
        }
    }

    /**
     * Возвращает  следующий  номер  при  автонумерации
     *
     */
    public function nextNumber()
    {


        $class = explode("\\", get_called_class());
        $metaname = $class[count($class) - 1];
        $doc = Document::getFirst("meta_name='" . $metaname . "'", "document_id desc");
        if ($doc == null)
            return '';
        $prevnumber = $doc->document_number;
        if (strlen($prevnumber) == 0)
            return '';
        $number = preg_replace('/[^0-9]/', '', $prevnumber);
        if (strlen($number) == 0)
            $number = 0;
        $letter = preg_replace('/[0-9]/', '', $prevnumber);

        return $letter . sprintf("%05d", ++$number);
    }

    /**
     *  Возвращает  списки  документов которые  могут быть  созданы  на  основании
     *
     */
    public function getRelationBased()
    {
        $list = array();

        return $list;
    }

    /**
     * дефолтный список состояний  для   выпадающих списков
     * может  переружатся  для  уточнения  в  зависимости  от типа  документа
     */
    public static function getStatesList()
    {
        $list = array();
        $list[Document::STATE_NEW] = 'Новый';
        $list[Document::STATE_EDITED] = 'Отредактирован';
        $list[Document::STATE_EXECUTED] = 'Проведен';

        return $list;
    }

    /**
     * Проверяет  может  ли  документ  быть  удален
     *
     */
    public function checkDeleted()
    {
        $conn = \ZCL\DB\DB::getConnect();

        $cnt = $conn->GetOne("select  count(*) from erp_docrel where  doc1 = {$this->document_id}  or  doc2 = {$this->document_id}");
        if ($cnt > 0)
            return false;

        return true;
    }

    /**
     * Возвращает  список  типов экспорта
     * Перегружается  дочерними  для  добавление  специфических  типов
     *
     */
    public function supportedExport()
    {
        return array(self::EX_EXCEL);
    }

    /**
     * Поиск  документа
     *
     * @param mixed $type   имя или id типа
     * @param mixed $from   начало  периода  или  null
     * @param mixed $to     конец  периода  или  null
     * @param mixed $header значения заголовка
     */
    public static function search($type, $from, $to, $header = array())
    {
        $conn = $conn = \ZCL\DB\DB::getConnect();
        ;
        $where = "state= " . Document::STATE_EXECUTED;

        if (strlen($type) > 0) {
            if ($type > 0) {
                $where = $where . " and  type_id ={$type}";
            } else {
                $where = $where . " and  meta_name='{$type}'";
            }
        }

        if ($from > 0)
            $where = $where . " and  document_date >= " . $conn->DBDate($from);
        if ($to > 0)
            $where = $where . " and  document_date <= " . $conn->DBDate($to);
        foreach ($header as $key => $value) {
            $where = $where . " and  content like '%<{$key}>{$value}</{$key}>%'";
        }

        return Document::find($where);
    }

}
