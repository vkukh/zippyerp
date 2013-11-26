<?php
 
namespace ZippyERP\ERP\Blocks; 
 
use \Zippy\Html\Label;
use \Zippy\Html\Link\ClickLink;
use \Zippy\Html\Panel;
use \Zippy\Html\Link\RedirectLink;
use \ZippyERP\ERP\Helper;
use \ZippyERP\ERP\Entity\Doc\Document;
use \ZippyERP\System\Application as App;
use \ZippyERP\System\System;
use \ZippyERP\System\Session;



/**
* Виджет для  просмотра  документов 
*/
class DocView extends \Zippy\Html\PageFragment
{

        public function __construct($id)
        {
                parent::__construct($id);

                $this->add(new RedirectLink('print', ""));
                $this->add(new RedirectLink('pdf', ""));
                $this->add(new RedirectLink('word', ""));
                $this->add(new RedirectLink('excel', ""));
                $this->add(new Label('preview'));
        }
        
        // Устанавливаем  документ  для  просмотра
        public  function setDoc(\ZippyERP\ERP\Entity\Doc\Document $item){
                
                //  получение  екзамеляра  конкретного  документа   с  данными
                $type = Helper::getMetaType($item->type_id);
                $class = "\\ZippyERP\\ERP\\Entity\\Doc\\" . $type['meta_name'];
                $item = $class::load($item->document_id);
                
                // генерация  печатной   формы                
                $html = $item->generateReport();
                if (strlen($html) == 0) {
                        $this->owner->setError("Не найден шаблон печатной формы");
                        return;
                }               
                
                $this->preview->setText($html, true);

                Session::getSession()->printform = "<html><head><meta http-equiv=\"Content-Type\" content=\"text/html; charset=UTF-8\"></head><body>" . $html . "</body></html>";
                $reportpage = "ZippyERP/ERP/Pages/ShowDoc";

                $filename = $type['meta_name'];

                $this->print->pagename = $reportpage;
                $this->print->params = array('print', $filename);
                $this->pdf->pagename = $reportpage;
                $this->pdf->params = array('pdf', $filename);
                $this->word->pagename = $reportpage;
                $this->word->params = array('doc', $filename);
                $this->excel->pagename = $reportpage;
                $this->excel->params = array('xls', $filename);        
        }
}
