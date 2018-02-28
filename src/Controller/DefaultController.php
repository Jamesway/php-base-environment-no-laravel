<?php
/**
 * Created by PhpStorm.
 * User: jamesroberson
 * Date: 2/27/18
 * Time: 2:00 PM
 */

namespace MyProject\Controller;



class DefaultController implements IController
{

    public function execute(array $params)
    {

        echo "default route";
    }

}