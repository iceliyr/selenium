<?php

/**
 * ECSHOP �Ǽʿ�ݲ��
 * ============================================================================
 * ��Ȩ���� 2005-2009 �Ϻ���������Ƽ����޹�˾������������Ȩ����
 * ��վ��ַ: http://www.ecshop.com��
 * ----------------------------------------------------------------------------
 * �ⲻ��һ�������������ֻ���ڲ�������ҵĿ�ĵ�ǰ���¶Գ����������޸ĺ�
 * ʹ�ã�������Գ���������κ���ʽ�κ�Ŀ�ĵ��ٷ�����
 * ============================================================================
 * $Author: liubo $
 * $Id: city_express.php 16881 2009-12-14 09:19:16Z liubo $
 */

if (!defined('IN_ECS'))
{
    die('Hacking attempt');
}

$shipping_lang = ROOT_PATH.'languages/' .$GLOBALS['_CFG']['lang']. '/shipping/city_express.php';
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
    $modules[$i]['code']    = 'city_express';

    $modules[$i]['version'] = '1.0.0';

    /* ���ͷ�ʽ������ */
    $modules[$i]['desc']    = 'city_express_desc';

    /* ���ͷ�ʽ�Ƿ�֧�ֻ������� */
    $modules[$i]['cod']     = TRUE;

    /* ��������� */
    $modules[$i]['author']  = 'ECSHOP TEAM';

    /* ������ߵĹٷ���վ */
    $modules[$i]['website'] = 'http://www.ecshop.com';

    /* ���ͽӿ���Ҫ�Ĳ��� */
    $modules[$i]['configure'] = array(
                                   array('name' => 'base_fee',     'value'=>10),
                                );

    return;
}

class city_express
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
    function city_express($cfg=array())
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
     * @return  decimal
     */
    function calculate($goods_weight, $goods_amount)
    {
        if ($this->configure['free_money'] > 0 && $goods_amount >= $this->configure['free_money'])
        {
            return 0;
        }
        else
        {
            return $this->configure['base_fee'];
        }
    }

    /**
     * ��ѯ����״̬
     * �����ͷ�ʽ��֧�ֲ�ѯ����״̬
     *
     * @access  public
     * @param   string  $invoice_sn     ��������
     * @return  string
     */
    function query($invoice_sn)
    {
        return $invoice_sn;
    }
}

?>
