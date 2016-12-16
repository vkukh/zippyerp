<?php

namespace ZippyERP\ERP\Pages;

use Zippy\Binding\PropertyBinding as Bind;
use Zippy\Html\DataList\DataView;
use Zippy\Html\Form\CheckBox;
use Zippy\Html\Form\DropDownChoice;
use Zippy\Html\Form\Form;
use Zippy\Html\Form\TextArea;
use Zippy\Html\Form\TextInput;
use Zippy\Html\Label;
use Zippy\Html\Link\ClickLink;
use Zippy\Html\Panel;
use ZippyERP\ERP\ACL;
use ZippyERP\System\System;
use ZippyERP\System\Application as App;

class MetaData extends \ZippyERP\System\Pages\Base
{

    private $metadatads;
    private $roleaccessds;

    public function __construct()
    {

        parent::__construct();
        if (System::getUser()->userlogin !== 'admin') {
            App::Redirect('\ZippyERP\System\Pages\Error', 'Вы не админ');
        }
        $this->metadatads = new \ZCL\DB\EntityDataSource("\\ZippyERP\\ERP\\Entity\\MetaData", "", "description");
        $this->roleaccessds = new \Zippy\Html\DataList\ArrayDataSource(null);

        $this->add(new Panel('listpan'));
        $this->listpan->add(new Form('filter'))->onSubmit($this, 'filterOnSubmit');
        $this->listpan->filter->add(new CheckBox('fdoc'))->setChecked(true);
        $this->listpan->filter->add(new CheckBox('fdic'))->setChecked(true);
        $this->listpan->filter->add(new CheckBox('frep'))->setChecked(true);
        $this->listpan->filter->add(new CheckBox('freg'))->setChecked(true);
        $this->listpan->filter->add(new CheckBox('fpage'))->setChecked(true);
        $this->listpan->add(new ClickLink('addnew'))->onClick($this, 'addnewOnClick');
        $this->listpan->add(new DataView('metarow', $this->metadatads, $this, 'metarowOnRow'))->Reload();

        $this->add(new Panel('editpan'))->setVisible(false);
        $this->editpan->add(new Form('editform'))->onSubmit($this, 'editformOnSubmit');
        $this->editpan->editform->add(new TextInput('meta_id'));
        $this->editpan->editform->add(new TextInput('edit_description'));
        $this->editpan->editform->add(new TextInput('edit_meta_name'));
        $this->editpan->editform->add(new TextInput('edit_menugroup'));
        $this->editpan->editform->add(new TextArea('edit_notes'));
        $this->editpan->editform->add(new CheckBox('edit_disabled'));
        $this->editpan->editform->add(new DropDownChoice('edit_meta_type'));
        $this->editpan->add(new ClickLink('cancel'))->onClick($this, 'cancelOnClick');
        $this->editpan->editform->add(new DataView('rolerow', $this->roleaccessds, $this, 'rolerowOnRow'));
        //  $this->editpan->editform->add(new Panel('eipan'));
        //  $this->editpan->editform->eipan->add(new RedirectLink('exportzip', ""));
        //   $this->editpan->editform->eipan->add(new \Zippy\Html\Form\File('importfile'));
    }

    public function filterOnSubmit($sender)
    {

        $where = "1<>1 ";
        if ($this->listpan->filter->fdoc->isChecked()) {
            $where .= " or meta_type = 1";
        }
        if ($this->listpan->filter->fdic->isChecked()) {
            $where .= " or meta_type = 4";
        }
        if ($this->listpan->filter->frep->isChecked()) {
            $where .= " or meta_type = 2";
        }
        if ($this->listpan->filter->freg->isChecked()) {
            $where .= " or meta_type = 3";
        }
        if ($this->listpan->filter->fpage->isChecked()) {
            $where .= " or meta_type = 5";
        }

        $this->metadatads->setWhere($where);

        $this->listpan->metarow->Reload();
    }

    public function addnewOnClick($sender)
    {
        $this->listpan->setVisible(false);
        $this->editpan->setVisible(true);
        //   $this->editpan->editform->eipan->setVisible(false);
        $this->editpan->editform->meta_id->setText(0);

        $this->roleaccessds->setArray(ACL::getRoleAccess(0));
        $this->editpan->editform->rolerow->Reload();
    }

    public function cancelOnClick($sender)
    {
        $this->listpan->setVisible(true);
        $this->editpan->setVisible(false);
    }

    public function metarowOnRow($row)
    {
        $item = $row->getDataItem();
        $row->setAttribute('style', $item->disabled == 1 ? 'color: #aaa' : null);
        switch ($item->meta_type) {
            case 1:
                $title = "Документ";
                break;
            case 2:
                $title = "Отчет";
                break;
            case 3:
                $title = "Журнал";
                break;
            case 4:
                $title = "Справочник";
                break;
            case 5:
                $title = "Страница";
                break;
        }

        $row->add(new Label('description', $item->description));
        $row->add(new Label('meta_name', $item->meta_name));
        $row->add(new Label('menugroup', $item->menugroup));
        $row->add(new Label('type', $title));
        $row->add(new ClickLink('rowedit'))->onClick($this, 'roweditOnClick');
        $row->add(new ClickLink('rowdelete'))->onClick($this, 'rowdeleteOnClick');
    }

    public function roweditOnClick($sender)
    {

        $item = $sender->getOwner()->getDataItem();
        $form = $this->editpan->editform;
        $form->meta_id->setText($item->meta_id);
        $form->edit_description->setText($item->description);
        $form->edit_notes->setText($item->notes);
        $form->edit_meta_name->setText($item->meta_name);
        $form->edit_menugroup->setText($item->menugroup);
        $form->edit_meta_type->setValue($item->meta_type);
        $form->edit_disabled->setChecked($item->disabled == 1);

        $this->listpan->setVisible(false);
        $this->editpan->setVisible(true);
        //   $form->eipan->setVisible(true);
        $this->roleaccessds->setArray(ACL::getRoleAccess($item->meta_id));
        $this->editpan->editform->rolerow->Reload();

        //  $form->eipan->exportzip->pagename = $reportpage;
        //   $form->eipan->exportzip->params = array('metaie', $item->meta_id);
    }

    public function rowdeleteOnClick($sender)
    {
        $item = $sender->getOwner()->getDataItem();
        \ZippyERP\ERP\Entity\MetaData::delete($item->meta_id);

        $this->listpan->metarow->Reload();
    }

    public function editformOnSubmit($sender)
    {

        $meta_id = $this->editpan->editform->meta_id->getText();
        if ($meta_id > 0) {
            $item = \ZippyERP\ERP\Entity\MetaData::load($meta_id);
        } else {
            $item = new \ZippyERP\ERP\Entity\MetaData();
        }

        $item->description = $this->editpan->editform->edit_description->getText();
        $item->menugroup = trim($this->editpan->editform->edit_menugroup->getText());
        $item->meta_name = trim(ucfirst($this->editpan->editform->edit_meta_name->getText()));
        $item->meta_type = $this->editpan->editform->edit_meta_type->getValue();
        $item->notes = $this->editpan->editform->edit_notes->getText();
        $item->disabled = $this->editpan->editform->edit_disabled->isChecked() ? 1 : 0;

        $item->save();
        ACL::updateRoleAccess($item->meta_id, $this->getComponent('rolerow')->getDataRows());
        $this->listpan->setVisible(true);
        $this->editpan->setVisible(false);

        $this->listpan->metarow->Reload();
        //обнуляем  поля   формы
        $this->editpan->editform->edit_description->setText('');
        $this->editpan->editform->edit_meta_name->setText('');
        $this->editpan->editform->edit_menugroup->setText('');
        $this->editpan->editform->edit_notes->setText('');
    }

    public function rolerowOnRow($row)
    {
        $item = $row->getDataItem();

        $row->add(new Label('rolename', $item->description));
        $row->add(new CheckBox('viewacc', new Bind($item, 'viewacc')));
        $row->add(new CheckBox('editacc', new Bind($item, 'editacc')));
        $row->add(new CheckBox('deleteacc', new Bind($item, 'deleteacc')));
        $row->add(new CheckBox('execacc', new Bind($item, 'execacc')));
    }

    /*
      События  жизненного  цикла  страницы, раскоментировать нужное
      public function beforeRequest(){
      parent::beforeRequest();

      }
      public function afterRequest(){
      parent::afterRequest();

      }
      public function beforeRender(){
      parent::beforeRender();

      }
      public function afterRender(){
      parent::afterRender();

      }
     */
}
