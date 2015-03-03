<?php

namespace ZippyERP\ERP;

use \ZCL\DB\DB;

/**
 * Класс  для  рендеринга  печатных  форм
 */
class Report
{

    private $_template;

    /**
     * Путь к  файлу  шаблона
     * 
     * @param mixed $template
     */
    public function __construct($template)
    {
        $this->_template = $template;
    }

    /**
     * Генерация  простой формы
     * 
     * @param mixed $header    Массив  с даннымы  шапки
     * @param mixed $detail    Двумерный массив  табличной  части
     * @param mixed $summary   Список  полей  по  которым  вычисляются  итоговые  данные табличной части
     */
    public function generate(array $header, array $detail = array(), array $summary = array())
    {

        $header['_detail'] = $detail;

        if (false == file_exists(_ROOT . 'templates/erp/printforms/' . $this->_template)) {
            return "Файл  печатной формы " . $this->_template . " не найден";
        }

        $fenom = \Fenom::factory(_ROOT . 'templates/erp/printforms', _ROOT . 'cache', \Fenom::AUTO_RELOAD);

        $html = $fenom->fetch($this->_template, $header);

        return $html;
    }

}
