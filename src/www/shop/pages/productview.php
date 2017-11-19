<?php

namespace ZippyERP\Shop\Pages;

use \Zippy\Html\Label;
use \Zippy\Binding\PropertyBinding;
use \Zippy\Html\Form\TextInput;
use \Zippy\Html\Form\TextArea;
use \Zippy\Binding\PropertyBinding as Bind;
use \ZippyERP\Shop\Helper;
use \Zippy\Html\Image;
use \ZippyERP\Shop\Entity\Product;
use \ZippyERP\Shop\Entity\ProductComment;
use \Zippy\Html\Panel;
use \Zippy\Html\Link\RedirectLink;
use \Zippy\Html\Link\ClickLink;
use \Zippy\Html\Link\BookmarkableLink;
use \Zippy\Html\DataList\DataView;
use \ZippyERP\System\System;
use \Zippy\WebApplication as App;

//детализация  по товару, отзывы
class ProductView extends Base
{

    public $msg, $attrlist, $clist;
    protected $product_id, $gotocomment;

    public function __construct($product_id = 0) {
        parent::__construct();

        $this->product_id = $product_id;



        $product = Product::load($product_id);
        if ($product == null) {
            App::Redirect404();
        }
        $this->_title = $product->productname;
        $this->_description = $product->description;
        //  $this->_keywords = $product->description;
        $this->add(new \Zippy\Html\Link\BookmarkableLink('product_image', "/simage/{$product->image_id}"))->setValue("/simage/{$product->image_id}/t");


        $this->add(new Label('productname', $product->productname));
        $this->add(new Label('onstore'));
        $this->add(new \Zippy\Html\Label('manufacturername', $product->manufacturername))->SetVisible($product->manufacturer_id > 0);

        $this->add(new Label("topsaled"))->setVisible($product->topsaled == 1);
        $this->add(new Label("novelty"))->setVisible($product->novelty == 1);

        $this->add(new Label('price', Helper::fm($product->price)));
        $this->add(new Label('oldprice', Helper::fm($product->old_price)))->setVisible($product->old_price > 0);
        $this->add(new Label('description', $product->description));
        $this->add(new Label('fulldescription', $product->fulldescription));
        $this->add(new TextInput('rated'))->setText($product->rated);
        $this->add(new BookmarkableLink('comments'))->setValue("Отзывов({$product->comments})");

        $list = Helper::getAttributeValuesByProduct($product);
        $this->add(new \Zippy\Html\DataList\DataView('attributelist', new \Zippy\Html\DataList\ArrayDataSource($list), $this, 'OnAddAttributeRow'))->Reload();
        $this->add(new ClickLink('buy', $this, 'OnBuy'));
        $this->add(new ClickLink('addtocompare', $this, 'OnAddCompare'));
        $this->add(new RedirectLink('compare', "\\ZippyERP\\Shop\\Pages\\Compare"))->setVisible(false);


        $form = $this->add(new \Zippy\Html\Form\Form('formcomment'));
        $form->onSubmit($this, 'OnComment');
        $form->add(new TextInput('nick'));
        $form->add(new TextInput('rating'));
        $form->add(new TextArea('comment'));
        $this->clist = ProductComment::findByProduct($product->product_id);
        $this->add(new \Zippy\Html\DataList\DataView('commentlist', new \Zippy\Html\DataList\ArrayDataSource(new PropertyBinding($this, 'clist')), $this, 'OnAddCommentRow'));
        $this->commentlist->setPageSize(25);
        $this->add(new \Zippy\Html\DataList\Pager("pag", $this->commentlist));
        $this->commentlist->Reload();

        if ($product->deleted == 1) {
            $this->onstore = "Снят с продажи";
            $this->buy->setVisible(false);
        } else {

            if ($product->cntonstore > 0) {
                $this->onstore->setText("В наличии");
                $this->buy->setValue("Купить");
            } else {
                $this->onstore->setText("Под заказ");
                $this->buy->setValue("Заказать");
            }
        }


        /*
          $recentlylist = \ZippyCMS\Core\System::getSession()->recently;
          if (!is_array($recentlylist))
          $recentlylist = array();
          $recentlylist[$product->product_id] = $product;
          \ZippyCMS\Core\System::getSession()->recently = $recentlylist;
          $this->recently->Update();
         */
        $recently = \ZippyERP\System\Session::getSession()->recently;
        if (!is_array($recently)) {
            $recently = array();
        }
        $recently[$product->product_id] = $product->product_id;
        \ZippyERP\System\Session::getSession()->recently = $recently;
    }

    //добавление в корзину
    public function OnBuy($sender) {
        $product = Product::load($this->product_id);
        $product->quantity = 1;
        \ZippyERP\Shop\Basket::getBasket()->addProduct($product);
        $this->setSuccess("Товар  добавлен  в   корзину");
    }

    //добавить к форме сравнения
    public function OnAddCompare($sender) {
        $product = Product::load($this->product_id);
        $comparelist = \ZippyERP\Shop\CompareList::getCompareList();
        if (false == $comparelist->addProduct($product)) {
            $this->setWarn('Добавлять можно только товары с одинаковой категорией');
        }
    }

    //добавать комментарий 
    public function OnComment($sender) {


        $comment = new \ZippyERP\Shop\Entity\ProductComment();
        $comment->product_id = $this->product_id;
        $comment->author = $this->formcomment->nick->getText();
        $comment->comment = $this->formcomment->comment->getText();
        $comment->rating = $this->formcomment->rating->getText();
        $comment->created = time();
        $comment->Save();
        $this->formcomment->nick->setText('');
        $this->formcomment->comment->setText('');
        $this->formcomment->rating->setText('0');
        $this->clist = ProductComment::findByProduct($this->product_id);
        $this->commentlist->Reload();

        $product = Product::load($comment->product_id);
        $this->rated->setText($product->rated);

        $this->gotocomment = true;
    }

    protected function beforeRender() {
        parent::beforeRender();

        if ($this->gotocomment == true) {
            App::addJavaScript("openComments();", true);
            $this->gotocomment = false;
        }
        if (\ZippyERP\Shop\CompareList::getCompareList()->hasProsuct($this->product_id)) {
            $this->compare->setVisible(true);
            $this->addtocompare->setVisible(false);
        } else {
            $this->compare->setVisible(false);
            $this->addtocompare->setVisible(true);
        }
    }

    public function OnAddCommentRow(\Zippy\Html\DataList\DataRow $datarow) {
        $item = $datarow->getDataItem();
        if ($item->moderated == 1) {
            $item->comment = "Отменено  модератором";
        }
        $datarow->add(new Label("nick", $item->author));
        $datarow->add(new Label("comment", $item->comment));
        $datarow->add(new Label("created", date('Y-m-d H:i', $item->created)));
        $datarow->add(new TextInput("rate"))->setText($item->rating);
        $datarow->add(new ClickLink('deletecomment', $this, 'OnDeleteComment'))->SetVisible(System::getUser()->userlogin == "admin" && $item->moderated != 1);
    }

    public function OnAddAttributeRow(\Zippy\Html\DataList\DataRow $datarow) {
        $item = $datarow->getDataItem();
        $datarow->add(new Label("attrname", $item->attributename));
        $meashure = "";
        if ($item->attributetype == 2)
            $meashure = $item->valueslist;
        if ($item->attributetype == 1) {
            $item->attributevalue = $item->attributetype == 1 ? "Есть" : "Нет";
        }

        $datarow->add(new Label("attrvalue", $item->attributevalue . $meashure));
    }

    //удалить коментарий
    public function OnDeleteComment($sender) {
        $comment = $sender->owner->getDataItem();
        $comment->moderated = 1;
        $comment->rating = 0;
        $comment->Save();
        // App::$app->getResponse()->addJavaScript("window.location='#{$comment->comment_id}'", true);
        //\Application::getApplication()->Redirect('\\ZippyCMS\\Modules\\Articles\\Pages\\ArticleList');
        $this->clist = ProductComment::findByProduct($this->product_id);
        $this->commentlist->Reload();
    }

}
