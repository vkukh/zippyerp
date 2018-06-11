<?php

namespace ZippyERP\ERP\Pages;

use \Carbon\Carbon;
use \Zippy\Html\DataList\ArrayDataSource;
use \Zippy\Html\DataList\DataView;
use \Zippy\Html\DataList\Paginator;
use \Zippy\Html\Label;
use \ZippyERP\ERP\DataItem;
use \ZippyERP\ERP\Entity\Doc\Document;
use \ZippyERP\ERP\Entity\Item;

;
;

class Main extends \ZippyERP\ERP\Pages\Base
{

    public function __construct() {
        parent::__construct();


        $this->add(new \ZippyERP\ERP\Blocks\WPlannedDocs("wplan"));
        $this->add(new \ZippyERP\ERP\Blocks\WNoliq("wnoliq"));
        $this->add(new \ZippyERP\ERP\Blocks\WHLItems("whlitems"));
    }

    /**
     * @see base
     *
     */
    public function getPageInfo() {
        return "Статистика на  початок дня";
    }

}
