<?php

namespace App\CoreModule\LoggingModule;

interface ReaderInterface
{
    public function clear();
    public function count();
    public function read();
    public function size();
}