<?php

namespace App\Cron;

use App\AppController;
use App\Cron\CronService as Cron;

class CronController extends AppController
{
    public function minify()
    {
        return Cron::minify();
    }

    public function stats()
    {
        return Cron::stats(1);
    }

    public function update()
    {
        return Cron::update();
    }

}