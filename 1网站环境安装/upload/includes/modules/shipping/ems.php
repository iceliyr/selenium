<?php

/**
 * ECSHOP EMS���
 * ============================================================================
 * ��Ȩ���� 2005-2009 �Ϻ���������Ƽ����޹�˾������������Ȩ����
 * ��վ��ַ: http://www.ecshop.com��
 * ----------------------------------------------------------------------------
 * �ⲻ��һ�������������ֻ���ڲ�������ҵĿ�ĵ�ǰ���¶Գ����������޸ĺ�
 * ʹ�ã�������Գ���������κ���ʽ�κ�Ŀ�ĵ��ٷ�����
 * ============================================================================
 * $Author: liubo $
 * $Id: ems.php 16881 2009-12-14 09:19:16Z liubo $
 */

if (!defined('IN_ECS'))
{
    die('Hacking attempt');
}

$shipping_lang = ROOT_PATH.'languages/' .$GLOBALS['_CFG']['lang']. '/shipping/ems.php';
if (file_exists($shipping_lang))
{
    global $_LANG;
    include_once($shipping_lang);
}

/* ģ��Ļ�����Ϣ */
if (isset($set_modules) && $set_modules == TRUE)
{
    $i = (isset($modules)) ? count($modules) : 0;

    /* ���ͷ�ʽ����Ĵ��������ļ�������һ�� */
    $modules[$i]['code']    = basename(__FILE__, '.php');

    $modules[$i]['version'] = '1.0.0';

    /* ���ͷ�ʽ������ */
    $modules[$i]['desc']    = 'ems_express_desc';

    /* ���ͷ�ʽ�Ƿ�֧�ֻ������� */
    $modules[$i]['cod']     = false;

    /* ��������� */
    $modules[$i]['author']  = 'ECSHOP TEAM';

    /* ������ߵĹٷ���վ */
    $modules[$i]['website'] = 'http://www.ecshop.com';

    /* ���ͽӿ���Ҫ�Ĳ��� */
    $modules[$i]['configure'] = array(
                                    array('name' => 'item_fee',     'value'=>20),
                                    array('name' => 'base_fee',     'value'=>20),
                                    array('name' => 'step_fee',     'value'=>15),
                                );

    return;
}

/**
 * ������ݰ������ü��㷽ʽ
 * ====================================================================================
 * 500g��500g����                             20Ԫ
 * -------------------------------------------------------------------------------------
 * ����ÿ500�˻�������                        6Ԫ/9Ԫ/15Ԫ(��������ͬ�շѲ�ͬ�����������ʽ����ļ��˲���绰�򵽵����ʾ�Ӫҵ������ѯ���ͷ��绰11185��)
 * -------------------------------------------------------------------------------------
 *
 */
class ems
{
    /*------------------------------------------------------ */
    //-- PUBLIC ATTRIBUTEs
    /*------------------------------------------------------ */

    /**
     * ������Ϣ
     */
    var $configure;

    /*------------------------------------------------------ */
    //-- PUBLIC METHODs
    /*------------------------------------------------------ */

    /**
     * ���캯��
     *
     * @param: $configure[array]    ���ͷ�ʽ�Ĳ���������
     *
     * @return null
     */
    function ems($cfg=array())
    {
        foreach ($cfg AS $key=>$val)
        {
            $this->configure[$val['name']] = $val['value'];
        }
    }

    /**
     * ���㶩�������ͷ��õĺ���
     *
     * @param   float   $goods_weight   ��Ʒ����
     * @param   float   $goods_amount   ��Ʒ���
     * @param   float   $goods_number   ��Ʒ����
     * @return  decimal
     */
    function calculate($goods_weight, $goods_amount, $goods_number)
    {
        if ($this->configure['free_money'] > 0 && $goods_amount >= $this->configure['free_money'])
        {
            return 0;
        }
        else
        {
            $fee = $this->configure['base_fee'];
            $this->configure['fee_compute_mode'] = !empty($this->configure['fee_compute_mode']) ? $this->configure['fee_compute_mode'] : 'by_weight';

            if ($this->configure['fee_compute_mode'] == 'by_number')
            {
                $fee = $goods_number * $this->configure['item_fee'];
            }
            else
            {
                if ($goods_weight > 0.5)
                {
                    $fee += (ceil(($goods_weight - 0.5) / 0.5)) * $this->configure['step_fee'];
                }
            }
            return $fee;
        }
    }

    /**
     * ��ѯ����״̬
     *
     * @access  public
     * @param   string  $invoice_sn     ��������
     * @return  string
     */
    function query($invoice_sn)
    {
        $str = '<form style="margin:0px" method="post" '.
            'action="http://www.ems.com.cn/qcgzOutQueryAction.do" name="queryForm_' .$invoice_sn. '" target="_blank">'.
            '<input type="hidden" name="mailNum" value="' .$invoice_sn. '" />'.
            '<a href="javascript:document.forms[\'queryForm_' .$invoice_sn. '\'].submit();">' .$invoice_sn. '</a>'.
            '<input type="hidden" name="reqCode" value="browseBASE" />'.
            '<input type="hidden" name="checknum" value="0568792906411" />'.
            '</form>';

        return $str;
    }
}

?>