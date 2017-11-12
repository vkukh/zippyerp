<?php

namespace ZippyERP\Shop\Pages;

class LoadImage extends \Zippy\Html\WebPage
{

    public function __construct($image_id, $thumb = "") {
        parent::__construct();

        $image = \ZippyERP\Shop\Entity\Image::load($image_id);
        if ($image instanceof \ZippyERP\Shop\Entity\Image) {

            header("Content-Type: " . $image->mime);
            if ($thumb == "t" && strlen($image->thumb) > 0) {
                header("Content-Length: " . strlen($image->thumb));
                echo $image->thumb;
            } else {
                header("Content-Length: " . strlen($image->content));
                echo $image->content;
            }
        } else {


            $file = _ROOT . 'assets/images/noimage.jpg';
            $type = 'image/jpeg';
            header('Content-Type:' . $type);
            header('Content-Length: ' . filesize($file));
            readfile($file);
        }
        exit;
    }

}
