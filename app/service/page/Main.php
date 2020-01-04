<?php

class Service_Page_Main
{
    public function exec()
    {
        echo View::load('index', ['name' => 'World!']);
    }
}