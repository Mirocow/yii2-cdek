<?php

namespace mirocow\cdek\components;

use mirocow\cdek\api\CalculatePriceDeliveryCdek;
use Yii;
use yii\base\Component;
use yii\base\Exception;
use yii\web\NotFoundHttpException;

/**
 * Class CDEK
 * @package mirocow\cdek\components
 *
 * @see
 */
class CDEK extends Component
{
    public $authLogin = '';

    public $authPassword = '';

    public $senderCityId = '';

    public $receiverCityId = '';

    public function calculate()
    {
        $calc = new CalculatePriceDeliveryCdek;

        if($this->authLogin && $this->authPassword) {
            $calc->setAuth($this->authLogin, $this->authPassword);
        }

        //устанавливаем город-отправитель
        $calc->setSenderCityId($_REQUEST['senderCityId']);

        //устанавливаем город-получатель
        $calc->setReceiverCityId($_REQUEST['receiverCityId']);

        //устанавливаем дату планируемой отправки
        $calc->setDateExecute($_REQUEST['dateExecute']);

        //задаём список тарифов с приоритетами
        $calc->addTariffPriority($_REQUEST['tariffList1']);
        $calc->addTariffPriority($_REQUEST['tariffList2']);

        //устанавливаем тариф по-умолчанию
        //$calc->setTariffId('137');

        //устанавливаем режим доставки
        $calc->setModeDeliveryId($_REQUEST['modeId']);

        //добавляем места в отправление
        $calc->addGoodsItemBySize($_REQUEST['weight1'], $_REQUEST['length1'], $_REQUEST['width1'], $_REQUEST['height1']);
        $calc->addGoodsItemByVolume($_REQUEST['weight2'], $_REQUEST['volume2']);

        if ($calc->calculate() === true) {
            $res = $calc->getResult();
            //echo 'Цена доставки: ' . $res['result']['price'] . 'руб.<br />';
            //echo 'Срок доставки: ' . $res['result']['deliveryPeriodMin'] . '-' .$res['result']['deliveryPeriodMax'] . ' дн.<br />';
            //echo 'Планируемая дата доставки: c ' . $res['result']['deliveryDateMin'] . ' по ' . $res['result']['deliveryDateMax'] . '.<br />';
            //echo 'id тарифа, по которому произведён расчёт: ' . $res['result']['tariffId'] . '.<br />';
            //if(array_key_exists('cashOnDelivery', $res['result'])) {
            //    echo 'Ограничение оплаты наличными, от (руб): ' . $res['result']['cashOnDelivery'] . '.<br />';
            //}
        } else {
            //$err = $calc->getError();
            /*if( isset($err['error']) && !empty($err) ) {
                //var_dump($err);
                foreach($err['error'] as $e) {
                    echo 'Код ошибки: ' . $e['code'] . '.<br />';
                    echo 'Текст ошибки: ' . $e['text'] . '.<br />';
                }
            }*/
        }
    }
}