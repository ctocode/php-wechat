<?php

use shiyunWechat\WechatFactory;

WechatFactory::getInstance()
    ->gzhApp()
    ->Custom_Menus()
    ->wxMenuCreate();
