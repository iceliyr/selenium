<?php

/**
 * ECSHOP v2.1.2b 升级程序
 * ============================================================================
 * 版权所有 (C) 2005-2007 北京亿商互动科技发展有限公司，并保留所有权利。
 * 网站地址: http://www.ecshop.com
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和
 * 使用；不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * $Author: testyang $
 * $Date: 2008-10-29 16:46:41 +0800 (三, 2008-10-29) $
 * $Id: v2.1.2b.php 15130 2008-10-29 08:46:41Z testyang $
 */

class up_v2_1_2b
{
    var $sql_files = array('structure' => 'structure.sql',
                           'data' => 'data.sql');

    var $auto_match = true;

    function __construct(){}
    function up_v2_1_2b(){}

    function update_database_optionally(){}
    function update_files(){}
}

?>