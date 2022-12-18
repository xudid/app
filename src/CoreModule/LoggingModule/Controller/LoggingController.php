<?php

namespace App\CoreModule\LoggingModule\Controller;

use App\Controller;
use App\CoreModule\LoggingModule\ReaderInterface;
use App\CoreModule\LoggingModule\View\LogTableFactory;
use Ui\Translation\FileSource;
use Ui\Translation\Translator;
use Ui\Widget\Button\Submit;
use Ui\X;

class LoggingController extends Controller
{
    private ReaderInterface $reader;
    private int $displayedLogs;

    public function __construct(ReaderInterface $reader, int $displayedLogs = 20)
    {
        parent::__construct();
        $this->reader = $reader;
        $this->displayedLogs = $displayedLogs;
    }

    /**
     * @param int $displayedLogs
     * @return LoggingController
     */
    public function setDisplayedLogs(int $displayedLogs): LoggingController
    {
        $this->displayedLogs = $displayedLogs;
        return $this;
    }

    public function index()
    {
        $this->log('Show logs', 'info');
        $this->log('Show logs', 'warning');
        $this->log('Show logs', 'emergency');
        $logFilePath =  $this->app::get('log_file');
        $content = X::H1('Logs')->setClass('text-center') . X::Br();
        $content .= X::Form((new Submit('Clear'))->setClass('btn-danger'))->setAction('/logging/clear')->setMethod('POST');
        if (!file_exists($logFilePath)) {
            $content .= 'No log file at: ' . $logFilePath;
        }

        $content .= 'Log file size: ' . filesize($logFilePath) . ' Bytes'. X::Br();
        $content .= 'Log contains: ' . $this->reader->count() . ' records';
        $content .= X::Br();
        $source = new FileSource(sys_get_temp_dir() . 'translations.php');
        $translator = new Translator($source);
        $tableFactory = new LogTableFactory($translator);
        $i = 0;
        do {
            $i++;
            $line = $this->reader->read();
            $logParts = explode(' > ', $line);
            if (!$logParts[0]) {
                continue;
            }

            $tableFactory->addLog($logParts[0], $logParts[1], $logParts[2]);
        } while ($line && $i < $this->displayedLogs);

        return $content . $tableFactory->getTable()->setClass('w-50');
    }

    public function clear()
    {
        $logFilePath =  $this->app::get('log_file');
        file_put_contents($logFilePath, '');
        $this->app::redirectToRoute('logging');
    }
}