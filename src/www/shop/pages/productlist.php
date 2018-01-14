<?php

namespace ZippyERP\Shop\Pages;

use \Zippy\Html\Panel;
use \Zippy\Html\Label;
use \Zippy\Html\Image;
use \Zippy\Html\Form\Button;
use \Zippy\Html\Form\Form;
use \Zippy\Html\Form\File;
use \Zippy\Html\Form\TextInput;
use \Zippy\Html\Form\TextArea;
use \Zippy\Html\Form\CheckBox;
use \Zippy\Html\Form\DropDownChoice;
use \Zippy\Html\Form\SubmitButton;
use \Zippy\Html\Link\SubmitLink;
use \Zippy\Html\Link\ClickLink;
use \Zippy\Html\Link\BookmarkableLink;
use \ZCL\BT\Tree;
use \ZippyERP\Shop\Entity\ProductGroup;
use \ZippyERP\Shop\Entity\Product;
use \ZippyERP\Shop\Entity\ProductAttribute;
use \ZippyERP\Shop\Entity\ProductAttributeValue;
use \ZippyERP\Shop\Helper;
use \Zippy\Html\DataList\DataView;
use \Zippy\Html\DataList\ArrayDataSource;
use \ZCL\DB\EntityDataSource;
use \Zippy\Binding\PropertyBinding as PB;
use \ZippyERP\System\System;

class ProductList extends Base
{

    private $rootgroup, $product;
    private $shop;
    public $group = null, $attrlist = array();

    public function __construct() {
        parent::__construct();

        $op = System::getOptions("shop");
        $this->shop = $op["store"];
        if ($this->shop == "") {
            $setError("Не заданий склад");
        }
        $tree = $this->add(new Tree("tree"));
        $tree->onSelectNode($this, "onTree");

        $this->ReloadTree();


        $this->add(new Panel('listpanel'));
        $this->listpanel->add(new Form('searchform'))->onSubmit($this, 'searchformOnSubmit');
        $this->listpanel->searchform->add(new TextInput('skeyword'));
        $this->listpanel->searchform->add(new CheckBox('sstatus'));
        $this->listpanel->searchform->add(new DropDownChoice('smanuf', \ZippyERP\Shop\Entity\Manufacturer::findArray('manufacturername', '', 'manufacturername')));
        $this->listpanel->searchform->add(new ClickLink('sclear'))->onClick($this, 'onSClear');
        $this->listpanel->add(new Form('sortform'));
        $this->listpanel->sortform->add(new DropDownChoice('sorting'))->onChange($this, 'sortingOnChange');
        $this->listpanel->add(new ClickLink('addnew'))->onClick($this, 'addnewOnClick');
        $this->listpanel->add(new DataView('plist', new ProductDataSource($this), $this, 'plistOnRow'));
        $this->listpanel->add(new \Zippy\Html\DataList\Paginator('pag', $this->listpanel->plist));
        $this->listpanel->plist->setPageSize(25);


        $this->add(new Panel('editpanel'))->setVisible(false);
        ;

        $editform = $this->editpanel->add(new Form('editform2'));
        $editform->add(new DropDownChoice('eitem', \ZippyERP\ERP\Entity\Item::findArrayEx("item_id in (select item_id from erp_store_stock where closed <> 1 and store_id={$this->shop})", 'itemname')));
        $editform->add(new ClickLink('bcancel2'))->onClick($this, 'bcancelOnClick');
        $editform->onSubmit($this, 'onSubmitForm2');

        $editform = $this->editpanel->add(new Form('editform'));
        $editform->add(new TextInput('ename'));
        $editform->add(new TextInput('ecode'));
        $editform->add(new TextArea('edescshort'));
        $editform->add(new TextArea('edescdet'));
        $editform->add(new DropDownChoice('emanuf', \ZippyERP\Shop\Entity\Manufacturer::findArray('manufacturername', '', 'manufacturername')));
        $editform->add(new DropDownChoice('egroup', \ZippyERP\Shop\Entity\ProductGroup::findArray('groupname', 'group_id not in (select parent_id from shop_productgroups)', 'groupname')));
        $editform->add(new \Zippy\Html\Image('prephoto'));
        $editform->add(new File('photo'));
        $editform->add(new DropDownChoice('estock'));
        $editform->add(new DataView('attrlist', new ArrayDataSource(new PB($this, attrlist)), $this, 'attrlistOnRow'));
        $editform->add(new CheckBox('edisabled'));
        $editform->add(new ClickLink('bcancel'))->onClick($this, 'bcancelOnClick');
        $editform->add(new ClickLink('bdelete'))->onClick($this, 'bdeleteOnClick');
        $editform->add(new ClickLink('bback'))->onClick($this, 'bbackOnClick');
        $editform->onSubmit($this, 'onSubmitForm');

        $this->listpanel->addnew->setVisible(false);
    }

    //загрузить дерево
    public function ReloadTree() {

        $this->tree->removeNodes();

        $this->rootgroup = new ProductGroup();
        $this->rootgroup->group_id = PHP_INT_MAX;
        $this->rootgroup->groupname = "//";

        $root = new \ZCL\BT\TreeNode("//", PHP_INT_MAX);
        $this->tree->addNode($root);

        $itemlist = ProductGroup::find("", "mpath,groupname");
        $nodelist = array();

        foreach ($itemlist as $item) {
            $node = new \ZCL\BT\TreeNode($item->groupname, $item->group_id);
            $parentnode = @$nodelist[$item->parent_id];
            if ($item->parent_id == 0)
                $parentnode = $root;

            $this->tree->addNode($node, $parentnode);

            $nodelist[$item->group_id] = $node;
        }
    }

    //клик по  узлу
    public function onTree($sender, $id) {
        $this->listpanel->addnew->setVisible(false);
        $this->editpanel->setVisible(false);

        $nodeid = $this->tree->selectedNodeId();
        if ($nodeid == -1) {
            $this->group = null;
            return;
        }
        if ($nodeid == -2) {
            $this->group = $this->rootgroup;
            return;
        }
        $this->group = ProductGroup::load($nodeid);
        if ($this->group instanceof ProductGroup) {
            $ch = $this->group->getChildren();
            $this->listpanel->addnew->setVisible(count($ch) == 0); // Добавляем  товар если  нет  дочерних груп у текущей]   
            $this->listpanel->plist->Reload();
            $this->attrlist = array();

            $this->listpanel->setVisible(true);
        }
    }

    public function searchformOnSubmit($sender) {

        $this->listpanel->plist->Reload();
    }

    public function sortingOnChange($sender) {
        $this->listpanel->plist->Reload();
    }

    public function onSClear($sender) {
        $this->listpanel->searchform->clean();
        $this->listpanel->plist->Reload();
    }

//новый
    public function addnewOnClick($sender) {
        $this->editpanel->setVisible(true);
        $this->editpanel->editform2->setVisible(true);
        $this->editpanel->editform2->eitem->setValue(0);
        $this->editpanel->editform->setVisible(false);
        $this->listpanel->setVisible(false);
        $this->editpanel->editform2->clean();
        $this->editpanel->editform->clean();
        $this->editpanel->editform->bdelete->setVisible(false);
    }

//выбран товар со склада
    public function onSubmitForm2($sender) {
        $this->product = new Product();
        $this->product->erp_item_id = $sender->eitem->getValue();
        $this->product->group_id = $this->group->group_id;
        $this->product->createdon = time();
        $item = \ZippyERP\ERP\Entity\Item::load($this->product->erp_item_id);
        $this->product->productname = $item->itemname;
        $this->product->item_code = $item->code;

        if ($this->product->erp_item_id == 0) {
            $this->setError('Не вибраний  товар');
            return;
        }
        $this->editpanel->editform->edisabled->setVisible(false);
        $this->editpanel->editform->bdelete->setVisible(false);
        $this->editpanel->editform->bback->setVisible(true);

        $this->editpanel->editform->ename->setText($this->product->productname);
        $this->editpanel->editform->ecode->setText($this->product->item_code);
        $this->editpanel->editform->estock->setOptionList(array());
        $stocks = \ZippyERP\ERP\Entity\Stock::find("store_id=" . $this->shop . " and item_id=" . $this->product->erp_item_id, "price");
        foreach ($stocks as $key => $value) {
            $this->editpanel->editform->estock->addOption($key, \ZippyERP\ERP\Helper::fm($value->price));
        }

        $this->attrlist = $this->product->getAttrList();
        $this->editpanel->editform->attrlist->Reload();
        $this->editpanel->editform2->setVisible(false);
        $this->editpanel->editform->setVisible(true);
        $this->editpanel->editform->egroup->setValue($this->group->group_id);
    }

//строка товара
    public function plistOnRow($row) {
        $item = $row->getDataItem();

        $row->add(new ClickLink("lname", $this, "lnameOnClick"))->setValue($item->productname);
        $row->add(new Label("lmanuf", $item->manufacturername));
        $row->add(new Label("ldescshort", $item->description));
        $row->add(new Label("lcode", $item->item_code));
        $row->add(new Label("lprice", \ZippyERP\Shop\Helper::fm($item->price)));
        $row->add(new Label("lcnt", number_format($item->cntonstore / 1000, 0, '.', '')));
        $row->add(new \Zippy\Html\Image("lphoto"))->setUrl("/simage/" . $item->image_id);
    }

//редактирование
    public function lnameOnClick($sender) {
        $this->editpanel->setVisible(true);
        $this->listpanel->setVisible(false);
        $this->editpanel->editform2->setVisible(false);
        $this->editpanel->editform->setVisible(true);
        $this->product = $sender->getOwner()->getDataItem();
        $this->editpanel->editform->prephoto->setUrl('/simage/' . $this->product->image_id);
        $this->editpanel->editform->ename->setText($this->product->productname);
        $this->editpanel->editform->ecode->setText($this->product->item_code);
        $this->editpanel->editform->edescshort->setText($this->product->description);
        $this->editpanel->editform->edescdet->setText($this->product->fulldescription);
        $this->editpanel->editform->emanuf->setValue($this->product->manufacturer_id);
        $this->editpanel->editform->estock->setValue($this->product->erp_stock_id);
        $this->editpanel->editform->bdelete->setVisible(true);
        $this->editpanel->editform->bback->setVisible(false);


        $this->editpanel->editform->estock->setOptionList(array());
        $stocks = \ZippyERP\ERP\Entity\Stock::find("store_id=" . $this->shop . " and item_id=" . $this->product->erp_item_id, "price");
        foreach ($stocks as $key => $value) {
            $this->editpanel->editform->estock->addOption($key, \ZippyERP\ERP\Helper::fm($value->price));
        }
        $this->attrlist = $this->product->getAttrList();
        $this->editpanel->editform->attrlist->Reload();
        $this->editpanel->editform->egroup->setValue($this->group->group_id);
    }

//строка  атрибута
    public function attrlistOnRow($row) {
        $attr = $row->getDataItem();

        $row->add(new CheckBox("nodata", new \Zippy\Binding\PropertyBinding($attr, "nodata")));
        $row->add(new AttributeComponent('attrdata', $attr));
    }

    public function bcancelOnClick($sender) {
        $this->editpanel->setVisible(false);
        $this->listpanel->setVisible(true);
    }

    public function onSubmitForm($sender) {
        $this->product->manufacturer_id = $sender->emanuf->getValue();

        if ($this->product->manufacturer_id == 0) {
            $this->setError('Не вибраний виробник');
            return;
        }

        $this->product->productname = $sender->ename->getText();
        $this->product->item_code = $sender->ecode->getText();
        $this->product->group_id = $sender->egroup->getValue();
        $this->product->description = $sender->edescshort->getText();
        $this->product->fulldescription = $sender->edescdet->getText();
        $this->product->erp_stock_id = $sender->estock->getValue();
        $this->product->price = $sender->estock->getValueName() * 100;
        $this->product->partion = \ZippyERP\ERP\Entity\Stock::load($this->product->erp_stock_id)->partion;

        $file = $sender->photo->getFile();
        if (strlen($file["tmp_name"]) > 0) {
            $imagedata = getimagesize($file["tmp_name"]);

            if (preg_match('/(gif|png|jpeg)$/', $imagedata['mime']) == 0) {
                $this->setError('Невірний формат');
                return;
            }

            if ($imagedata[0] * $imagedata[1] > 1000000) {
                $this->setError('Надто великий розмір зображення');
                return;
            }
            $r = ((double) $imagedata[0]) / $imagedata[1];
            if ($r > 1.05 || $r < 0.95) {
                $this->setError('Зображення має бути квадратним');
                return;
            }

            $image = new \ZippyERP\Shop\Entity\Image();
            $image->content = file_get_contents($file['tmp_name']);
            $image->mime = $imagedata['mime'];
            $th = new \JBZoo\Image\Image($file['tmp_name']);
            $th = $th->resize(256, 256);
            $th->save();
            $image->thumb = $th->getBinary();

            $image->save();
            $this->product->image_id = $image->image_id;

            //$sender->prephoto->setUrl('/simage/' .$this->product->image_id);
            $sender->clean();
        }


        $this->product->attributevalues = array();


        $rows = $sender->attrlist->getChildComponents();
        foreach ($rows as $r) {
            $a = $r->getDataItem();
            $this->product->attributevalues[$a->attribute_id] = "" . $a->attributevalue;
            if ($a->nodata)
                $this->product->attributevalues[$a->attribute_id] = '';
        }

        $this->product->save();
        $this->listpanel->plist->Reload();
        $this->editpanel->setVisible(false);
        $this->listpanel->setVisible(true);
    }

    public function bdeleteOnClick($sender) {
        Product::delete($this->product->product_id);
        $this->listpanel->plist->Reload();
        $this->editpanel->setVisible(false);
        $this->listpanel->setVisible(true);
    }

    public function bbackOnClick($sender) {
        $this->editpanel->editform2->setVisible(true);
        $this->editpanel->editform->setVisible(false);
    }

}

class ProductDataSource implements \Zippy\Interfaces\DataSource
{

    private $page;

    public function __construct($page) {
        $this->page = $page;
    }

    private function getWhere() {

        $conn = \ZDB\DB::getConnect();

        $where = " group_id = " . ($this->page->group == null ? 0 : $this->page->group->group_id );
        $st = $this->page->listpanel->searchform->skeyword->getText();
        $sm = $this->page->listpanel->searchform->smanuf->getValue();
        if ($sm > 0) {
            $where .= " and manufacturer_id  =  " . $sm;
        }
        if (strlen($st) > 0) {
            $where .= " and (productname like   " . $conn->qstr("%{$st}%") . " or item_code = " . $conn->qstr($st) . ") ";
        }
        if ($this->page->listpanel->searchform->sstatus->isChecked()) {
            $where .= " and deleted = 1  ";
        } else {
            $where .= " and deleted = 0  ";
        }


        return $where;
    }

    public function getItemCount() {
        return Product::findCnt($this->getWhere());
    }

    public function getItems($start, $count, $sortfield = null, $asc = null) {

        $order = "productname";
        $o = $this->page->listpanel->sortform->sorting->getValue();
        if ($o == 1) {
            $order = "price asc";
        }
        if ($o == 2) {
            $order = "price desc";
        }
        if ($o == 3) {
            $order = "cntonstore asc";
        }
        if ($o == 4) {
            $order = "cntonstore desc";
        }



        return Product::find($this->getWhere(), $order, $count, $start);
    }

    public function getItem($id) {
        
    }

}

//компонент атрибута  товара
//выводит  элементы  формы  ввода   в  зависимости  от  типа  атрибута
class AttributeComponent extends \Zippy\Html\CustomComponent implements \Zippy\Interfaces\SubmitDataRequest
{

    protected $productattribute = null;

    public function __construct($id, $productattribute) {
        parent::__construct($id);
        $this->productattribute = $productattribute;
    }

    public function getContent($attributes) {
        $ret = "";

        //'Есть/Нет'
        if ($this->productattribute->attributetype == 1) {

            if ($this->productattribute->value == 1) {
                $checked = ' checked="on"';
            }
            $ret .= "<div class=\"checkbox\"><label><input type=\"checkbox\"  name=\"{$this->id}\" {$checked} /> " . $this->productattribute->attributename;
            $ret .= "</label></div>";
        }
        //'Число'
        if ($this->productattribute->attributetype == 2) {
            $ret .= "<div class=\"form-group\"><label  >{$this->productattribute->attributename}:</label>";
            $ret .= "<input name=\"{$this->id}\" type=\"text\" value=\"{$this->productattribute->value}\"  class=\"form-control\"  /> ";
            $ret .= "</div>";
        }
        //'Список'
        if ($this->productattribute->attributetype == 3) {
            $ret .= "<div class=\"form-group\"><label  >{$this->productattribute->attributename}:</label>";

            $ret .= "<select name=\"{$this->id}\" class=\"form-control\" ><option value=\"-1\">Не выбран</option>";
            $list = explode(',', $this->productattribute->valueslist);
            foreach ($list as $key => $value) {
                $value = trim($value);
                $sel = $sel . "<option value=\"{$key}\" " . ($this->productattribute->value === $value ? ' selected="on"' : '') . ">{$value}</option>";
            }
            $ret .= $sel . '</select></div>';
        }
        //'Набор'
        if ($this->productattribute->attributetype == 4) {
            $ret .= "<div class=\"form-group\"><label  >{$this->productattribute->attributename}:</label></div>";
            $ret .= "<div class=\"checkbox\">";


            $list = explode(',', $this->productattribute->valueslist);
            $values = explode(',', $this->productattribute->value);
            $i = 1;
            foreach ($list as $key => $value) {
                $ret .= "<label>";
                $value = trim($value);
                if (in_array($value, $values))
                    $checked = ' checked="on"';
                else
                    $checked = "";

                $name = $this->id . '_' . $i++;
                $ret = $ret . "<input name=\"{$name}\" type=\"checkbox\"  {$checked}> {$value}";
                $ret .= "</label><br>";
            }

            $ret .= "</div>";
        }
        //'Строка'
        if ($this->productattribute->attributetype == 5) {
            $ret .= "<div class=\"form-group\"><label  >{$this->productattribute->attributename}:</label>";
            $ret .= "<textarea name=\"{$this->id}\" type=\"text\"  cols=\"20\" rows=\"3\"   class=\"form-control\" >{$this->productattribute->value}</textarea> ";
            $ret .= "</div>";
        }

        return $ret;
    }

    //Вынмаем данные формы  после  сабмита
    public function getRequestData() {
        if ($this->productattribute->attributetype == 1) {
            $this->productattribute->attributevalue = isset($_POST[$this->id]) ? 1 : 0;
        };
        if ($this->productattribute->attributetype == 2 || $this->productattribute->attributetype == 5) {
            $this->productattribute->attributevalue = $_POST[$this->id];
        }
        if ($this->productattribute->attributetype == 3) {
            $list = explode(',', $this->productattribute->valueslist);

            $this->productattribute->attributevalue = $list[$_POST[$this->id]];
        }




        if ($this->productattribute->attributetype == 4) {
            $values = array();
            $list = explode(',', $this->productattribute->valueslist);
            $i = 1;
            foreach ($list as $key => $value) {
                $name = $this->id . '_' . $i++;
                if (isset($_POST[$name])) {
                    $values[] = trim($value);
                }
            }
            $this->productattribute->attributevalue = implode(',', $values);
        };
    }

    public function clean() {
        $this->value = array();
    }

}
