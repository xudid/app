<?php

namespace App\CoreModule\LoggingModule;

class FileManager implements ReaderInterface
{

    private string $filePath;
    private $fileHandler;

    public function __construct($filePath)
    {
        $this->filePath = $filePath;
    }

    public function getFileHandler()
    {
        if (!$this->fileHandler) {
            $this->fileHandler = fopen($this->filePath,'r');
        }

        return $this->fileHandler;
    }

    public function clear()
    {
        file_put_contents($this->filePath, '');
    }

    public function count()
    {
        $count = 0;
        $handle = $this->getFileHandler();
        while(!feof($handle)){
            $line = fgets($handle);
            if ($line) {
                $count++;
            }
        }

        rewind($handle);
        return $count;
    }

    public function read()
    {
        $handler = $this->getFileHandler();
        return fgets($handler);
    }

    public function readReverse()
    {
        $handler = $this->getFileHandler();
        $position = ftell($handler);
        $eolSize = strlen(PHP_EOL);
        if ($position == 0) {
            fseek($handler, 0, SEEK_END);
        } else {
            fseek($handler, $eolSize, SEEK_CUR); //move for EOL
        }

        do {
            fseek($handler, -1, SEEK_CUR); //seek 1 by 1 char from EOF
            $eol = fgetc($handler) ; //search for EOL (remove 1 fgetc if needed)
            fseek($handler, -$eolSize, SEEK_CUR); //go back for EOL
        } while ($eol != PHP_EOL || '\r\n' && ftell($handler) > 0 ); //check EOL and BOF



        $position = ftell($handler); //save current position
        if ($position != 0) {
            fseek($handler, $eolSize, SEEK_CUR);
        } //move for EOL
        $line = fgets($handler); //read LINE or do whatever is needed
        return $line;
    }

    public function size()
    {
        if (!file_exists($this->filePath)) {
            return 0;
        }

        return filesize($this->filePath);
    }
}