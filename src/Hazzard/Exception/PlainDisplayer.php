<?php namespace Hazzard\Exception;

use Whoops\Handler\Handler as WhoopsHandler;
use Whoops\Util\Misc;

class PlainDisplayer extends WhoopsHandler {

    /**
     * @return int
     */
    public function handle()
    {
        if ($this->isAjaxRequest()) {
            if (Misc::canSendHeaders()) {
                header('Content-Type: application/json');
            }

            $response = json_encode(array('error' => 'Whoops! There was an error.'));
        } else {
            $response = file_get_contents(__DIR__.'/resources/plain.html');
        }

        echo $response;
        
        return WhoopsHandler::QUIT;
    }

    /**
     * Check if is an AJAX request.
     *
     * @return bool
     */
    protected function isAjaxRequest()
    {
       return (!empty($_SERVER['HTTP_X_REQUESTED_WITH'])
            && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest');
    }
}
