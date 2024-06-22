<?php

/**
 * ECSHOP ��ͨ�ٵݲ��
 * ============================================================================
 * ��Ȩ���� 2005-2009 �Ϻ���������Ƽ����޹�˾������������Ȩ����
 * ��վ��ַ: http://www.ecshop.com��
 * ----------------------------------------------------------------------------
 * �ⲻ��һ�������������ֻ���ڲ�������ҵĿ�ĵ�ǰ���¶Գ����������޸ĺ�
 * ʹ�ã�������Գ���������κ���ʽ�κ�Ŀ�ĵ��ٷ�����
 * ============================================================================
 * $Author: liubo $
 * $Id: zto.php 16881 2009-12-14 09:19:16Z liubo $
*/

$shipping_lang = ROOT_PATH.'languages/' .$GLOBALS['_CFG']['lang']. '/shipping/zto.php';
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
    $modules[$i]['code']    = 'zto';

    $modules[$i]['version'] = '1.0.0';

    /* ���ͷ�ʽ������ */
    $modules[$i]['desc']    = 'zto_desc';

    /* �Ƿ�֧�ֱ��� */
    $modules[$i]['insure']  = '2%';

    /* ���ͷ�ʽ�Ƿ�֧�ֻ������� */
    $modules[$i]['cod']     = false;

    /* ��������� */
    $modules[$i]['author']  = '��ɫ��Ȼ';

    /* ������ߵĹٷ���վ */
    $modules[$i]['website'] = 'http://www.ecshop.com';

    /* ���ͽӿ���Ҫ�Ĳ��� */
    $modules[$i]['configure'] = array(
                                    array('name' => 'item_fee',     'value'=>15),    /* ������Ʒ���͵ļ۸� */
                                    array('name' => 'base_fee',    'value'=>10),    /* 1000�����ڵļ۸� */
                                    array('name' => 'step_fee',     'value'=>5),    /* ����ÿ1000�����ӵļ۸� */
                                );

    return;
}

class zto
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
    function zto($cfg = array())
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
            @$fee = $this->configure['base_fee'];
            $this->configure['fee_compute_mode'] = !empty($this->configure['fee_compute_mode']) ? $this->configure['fee_compute_mode'] : 'by_weight';

            if ($this->configure['fee_compute_mode'] == 'by_number')
            {
                $fee = $goods_number * $this->configure['item_fee'];
            }
            else
            {
                if ($goods_weight > 1)
                {
                    $fee += (ceil(($goods_weight - 1))) * $this->configure['step_fee'];
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
        $str = '<form style="margin:0px" methods="post" '.
            'action="http://www.zto.cn/bill.asp" name="queryForm_' .$invoice_sn. '" target="_blank">'.
            '<input type="hidden" name="ID" value="' .str_replace("<br>","\n",$invoice_sn). '" />'.
            '<a href="javascript:document.forms[\'queryForm_' .$invoice_sn. '\'].submit();">' .$invoice_sn. '</a>'.
            '<input type="hidden" name="imageField.x" value="26" />'.
            '<input type="hidden" name="imageField.x" value="43" />'.
            '</form>';

        return $str;
    }

    /**
     * ���㱣�۷���
     * ���۷Ѳ�����100Ԫ�����۽��ø���10000Ԫ�����۽���10000Ԫ�ģ������Ĳ�����Ч
     * @access  public
     * @param   int     $goods_amount       ���۷���
     * @param   int     $insure             ���۱���
     *
     * @return void
     */
    function calculate_insure ($goods_amount, $insure)
    {
        if ($goods_amount > 10000)
        {
            $goods_amount = 10000;
        }

        $fee = $goods_amount * $insure;

        if ($fee < 100)
        {
            $fee = 100;
        }

        return $fee;
    }

}

?>