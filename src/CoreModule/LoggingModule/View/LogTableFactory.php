<?php

namespace App\CoreModule\LoggingModule\View;

use Ui\Translation\TranslatorInterface;
use Ui\Widget\Table\ArrayTableFactory;
use Ui\Widget\Table\Cell\Cell;
use Ui\Widget\Table\Column\Column;
use Ui\Widget\Table\Row\TableRow;

class LogTableFactory extends ArrayTableFactory
{
    public function __construct(TranslatorInterface $translator)
    {
        parent::__construct($translator);
        $dateColumn = new Column('date', 'Date');
        $levelColumn = new Column('level', 'Level');
        $messageColumn = new Column('message', 'Message');
        $this->setColumns($dateColumn, $levelColumn, $messageColumn);
    }

    public function addLog($date, $level, $message)
    {
        $tableRow = new TableRow();
        if ($level == 'WARNING') {
            $tableRow->setClass('row-warning');
        }
        if (in_array($level, ['EMERGENCY', 'ALERT', 'CRITICAL', 'ERROR'])) {
            $tableRow->setClass('row-danger');
        }
        $tableRow->addCell(new Cell($date));
        $tableRow->addCell(new Cell($level));
        $tableRow->addCell(new Cell($message));
        $this->addRow($tableRow);
    }
}