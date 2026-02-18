<?php

namespace App\Help;

use App\AppController;

use App\Help\HelpService as Help;

class HelpController extends AppController
{
    protected $commands;

    private function cmd($type)
    {

        $paginate = paginate($this->data, Help::count($type), 10);
        $page = $paginate['page'];
        $limit = $paginate['limit'];
        $offset = $paginate['offset'];
        $total = $paginate['total'];

        $this->commands = Help::paginate($type, $limit, $offset);

        if(empty($this->data) || is_numeric($this->data)) {

            foreach ($this->commands as $item) {

                $cmd = strtoupper($item->cmd);
                $input = $item->input;
                $info = $item->info;

                echo "$cmd $input\n";
            }
            echo "\nHELP {$page}/{$total}\n";

            exit;

        } 

        if(!empty($this->data)) {

            $help = Help::search($this->data, $type);

            if(empty($help)) {
                echo 'ERROR: UNKNOWN COMMAND';
                exit;
            }

            $cmd = strtoupper($help->cmd);
            $input = $help->input;
            $info = $help->info;
            
            echo <<< EOT
            $cmd $input
            $info
            EOT;
            exit;
        }
    }

    public function visitor()
    {
       return $this->cmd('is_visitor');
    }

    public function guest()
    {
        return $this->cmd('is_guest');
    }

    public function host()
    {
        return $this->cmd('is_host');
    }

    public function user()
    {
        return $this->cmd('is_user');
    }

}