<?php

/**
 * ECSHOP ȱ������������
 * ============================================================================
 * ��Ȩ���� 2005-2009 �Ϻ���������Ƽ����޹�˾������������Ȩ����
 * ��վ��ַ: http://www.ecshop.com��
 * ----------------------------------------------------------------------------
 * �ⲻ��һ�������������ֻ���ڲ�������ҵĿ�ĵ�ǰ���¶Գ����������޸ĺ�
 * ʹ�ã�������Գ���������κ���ʽ�κ�Ŀ�ĵ��ٷ�����
 * ============================================================================
 * $Author: liubo $
 * $Id: goods_booking.php 16881 2009-12-14 09:19:16Z liubo $
*/

define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');
admin_priv('booking');
/*------------------------------------------------------ */
//-- �г����ж�����Ϣ
/*------------------------------------------------------ */
if ($_REQUEST['act']=='list_all')
{
    $smarty->assign('ur_here',      $_LANG['list_all']);
    $smarty->assign('full_page',    1);

    $list = get_bookinglist();

    $smarty->assign('booking_list', $list['item']);
    $smarty->assign('filter',       $list['filter']);
    $smarty->assign('record_count', $list['record_count']);
    $smarty->assign('page_count',   $list['page_count']);

    $sort_flag  = sort_flag($list['filter']);
    $smarty->assign($sort_flag['tag'], $sort_flag['img']);

    assign_query_info();
    $smarty->display('booking_list.htm');
}

/*------------------------------------------------------ */
//-- ��ҳ������
/*------------------------------------------------------ */
if ($_REQUEST['act'] == 'query')
{
    $list = get_bookinglist();

    $smarty->assign('booking_list', $list['item']);
    $smarty->assign('filter',       $list['filter']);
    $smarty->assign('record_count', $list['record_count']);
    $smarty->assign('page_count',   $list['page_count']);

    $sort_flag  = sort_flag($list['filter']);
    $smarty->assign($sort_flag['tag'], $sort_flag['img']);

    make_json_result($smarty->fetch('booking_list.htm'), '',
        array('filter' => $list['filter'], 'page_count' => $list['page_count']));
}

/*------------------------------------------------------ */
//-- ɾ��ȱ���Ǽ�
/*------------------------------------------------------ */

if ($_REQUEST['act'] == 'remove')
{
    check_authz_json('booking');

    $id = intval($_GET['id']);

    $db->query("DELETE FROM " .$ecs->table('booking_goods'). " WHERE rec_id='$id'");

    $url = 'goods_booking.php?act=query&' . str_replace('act=remove', '', $_SERVER['QUERY_STRING']);

    ecs_header("Location: $url\n");
    exit;
}

/*------------------------------------------------------ */
//-- ��ʾ����
/*------------------------------------------------------ */
if ($_REQUEST['act']=='detail')
{
    $id = intval($_REQUEST['id']);

    $smarty->assign('booking',      get_booking_info($id));
    $smarty->assign('ur_here',      $_LANG['detail']);
    $smarty->assign('action_link',  array('text' => $_LANG['06_undispose_booking'], 'href'=>'goods_booking.php?act=list_all'));
    $smarty->display('booking_info.htm');
}

/*------------------------------------------------------ */
//-- �����ύ����
/*------------------------------------------------------ */
if ($_REQUEST['act'] =='update')
{
    /* Ȩ���ж� */
    admin_priv('booking');

    $dispose_note = !empty($_POST['dispose_note']) ? trim($_POST['dispose_note']) : '';

    $sql = "UPDATE  ".$ecs->table('booking_goods').
            " SET is_dispose='1', dispose_note='$dispose_note', ".
                    "dispose_time='" .gmtime(). "', dispose_user='".$_SESSION['admin_name']."'".
            " WHERE rec_id='$_REQUEST[rec_id]'";
    $db->query($sql);

    ecs_header("Location: ?act=detail&id=".$_REQUEST['rec_id']."\n");
    exit;
}

/**
 * ��ȡ������Ϣ
 *
 * @access  public
 *
 * @return array
 */
function get_bookinglist()
{
    /* ��ѯ���� */
    $filter['keywords']   = empty($_REQUEST['keywords']) ? '' : trim($_REQUEST['keywords']);
    if ($_REQUEST['is_ajax'] == 1)
    {
        $filter['keywords'] = json_str_iconv($filter['keywords']);
    }
    $filter['dispose']    = empty($_REQUEST['dispose']) ? 0 : intval($_REQUEST['dispose']);
    $filter['sort_by']    = empty($_REQUEST['sort_by']) ? 'sort_order' : trim($_REQUEST['sort_by']);
    $filter['sort_order'] = empty($_REQUEST['sort_order']) ? 'DESC' : trim($_REQUEST['sort_order']);

    $where = (!empty($_REQUEST['keywords'])) ? " AND g.goods_name LIKE '%" . mysql_like_quote($filter['keywords']) . "%' " : '';
    $where .= (!empty($_REQUEST['dispose'])) ? " AND bg.is_dispose = '$filter[dispose]' " : '';

    $sql = 'SELECT COUNT(*) FROM ' .$GLOBALS['ecs']->table('booking_goods'). ' AS bg, '.
            $GLOBALS['ecs']->table('goods'). ' AS g '.
            "WHERE bg.goods_id = g.goods_id $where";
    $filter['record_count'] = $GLOBALS['db']->getOne($sql);

    /* ��ҳ��С */
    $filter = page_and_size($filter);

    /* ��ȡ����� */
    $sql = 'SELECT bg.rec_id, bg.link_man, g.goods_id, g.goods_name, bg.goods_number, bg.booking_time, bg.is_dispose '.
            'FROM ' .$GLOBALS['ecs']->table('booking_goods'). ' AS bg, ' .$GLOBALS['ecs']->table('goods'). ' AS g '.
            "WHERE bg.goods_id = g.goods_id $where " .
            "ORDER BY $filter[sort_by] $filter[sort_order] ".
            "LIMIT ". $filter['start'] .", $filter[page_size]";
    $row = $GLOBALS['db']->getAll($sql);

    foreach ($row AS $key => $val)
    {
        $row[$key]['booking_time'] = local_date($GLOBALS['_CFG']['time_format'], $val['booking_time']);
    }
    $filter['keywords'] = stripslashes($filter['keywords']);
    $arr = array('item' => $row, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count']);

    return $arr;
}

/**
 * ���ȱ���Ǽǵ���ϸ��Ϣ
 *
 * @param   integer     $id
 *
 * @return  array
 */
function get_booking_info($id)
{
    global $ecs, $db, $_CFG, $_LANG;

    $sql ="SELECT bg.rec_id, bg.user_id, IFNULL(u.user_name, '$_LANG[guest_user]') AS user_name, ".
                "bg.link_man, g.goods_name, bg.goods_id, bg.goods_number, ".
                "bg.booking_time, bg.goods_desc,bg.dispose_user, bg.dispose_time, bg.email, ".
                "bg.tel, bg.dispose_note ,bg.dispose_user, bg.dispose_time,bg.is_dispose  ".
            "FROM " . $ecs->table('booking_goods')." AS bg ".
            "LEFT JOIN " . $ecs->table('goods') . " AS g ON g.goods_id=bg.goods_id ".
            "LEFT JOIN " . $ecs->table('users') . " AS u ON u.user_id=bg.user_id ".
            "WHERE bg.rec_id ='$id'";

    $res = $db->GetRow($sql);

    /* ��ʽ��ʱ�� */
    $res['booking_time'] = local_date($_CFG['time_format'],$res['booking_time']);
    if (!empty($res['dispose_time']))
    {
        $res['dispose_time'] = local_date($_CFG['time_format'],$res['dispose_time']);
    }

    return $res;
}

?>