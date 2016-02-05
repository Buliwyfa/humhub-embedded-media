<?php

/**
 * MainController shows the example page
 *
 * @author David Findlay <davidjwfindlay@gmail.com>
 */
class MainController extends Controller
{

    public function actionIndex()
    {
        $this->render('index');
    }

}
