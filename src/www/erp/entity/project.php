<?php

namespace ZippyERP\ERP\Entity;

/**
 * Клас-сущность  проект
 * 
 * @table=erp_task_project
 * @view=erp_task_project_view
 * @keyfield=project_id
 */
class Project extends \ZCL\DB\Entity
{

    protected function afterLoad()
    {
        $this->start_date = strtotime($this->start_date);
        $this->end_date = strtotime($this->end_date);
    }

    protected function beforeDelete()
    {
        $conn = \ZCL\DB\DB::getConnect();
        // $conn->Execute("delete from erp_document_update_log  where document_id =" . $this->document_id);

        return true;
    }

}
