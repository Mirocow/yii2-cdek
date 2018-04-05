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

    // Экспресс лайт дверь-дверь срок: 3-4 дней
    const TARIF_DOR_DOR = 1;

    // Супер-экспресс дверь-дверь срок: 1 дней
    const TARIF_SUPPER_EXPRESS_DOR_DOR = 3;

    // Экспресс лайт склад-склад срок: 3-4 дней
    const TARIF_EXPRESS_STORE_STORE = 10;

    // Экспресс лайт склад-дверь срок: 3-4 дней
    const TARIF_EXPRESS_LIGHT_STORE_STORE = 11;

    // Экспресс лайт дверь-склад срок: 3-4 дней
    const TARIF_EXPRESS_LIGHT_DOR_STORE = 12;

    public $authLogin = null;

    public $authPassword = null;

    public $senderCityId = null;

    public $receiverCityId = null;

    public $dateExecute = null;

    public $tariffId = self::TARIF_DOR_DOR;

    public $weight = 1000;

    public $length = 10;

    public $width = 10;

    public $height = 20;

    public function calculate()
    {

        $calc = new CalculatePriceDeliveryCdek;

        if($this->authLogin && $this->authPassword) {
            $calc->setAuth($this->authLogin, $this->authPassword);
        }

        //устанавливаем город-отправитель
        $calc->setSenderCityId($this->senderCityId);

        //устанавливаем город-получатель
        $calc->setReceiverCityId($this->receiverCityId);

        //устанавливаем дату планируемой отправки
        if($this->dateExecute) {
            $calc->setDateExecute($this->dateExecute);
        }

        //задаём список тарифов с приоритетами
        $calc->addTariffPriority(self::TARIF_DOR_DOR, 1);
        $calc->addTariffPriority(self::TARIF_SUPPER_EXPRESS_DOR_DOR, 3);
        $calc->addTariffPriority(self::TARIF_EXPRESS_STORE_STORE, 2);
        $calc->addTariffPriority(self::TARIF_EXPRESS_LIGHT_DOR_STORE, 4);

        //устанавливаем тариф по-умолчанию
        $calc->setTariffId($this->tariffId);

        //добавляем места в отправление
        if($this->weight) {
            $calc->addGoodsItemBySize($this->weight / 1000, $this->length, $this->width, $this->height);
            //$calc->addGoodsItemByVolume($_REQUEST['weight2'], $_REQUEST['volume2']);
        }

        if ($calc->calculate() === true) {
            $res = $calc->getResult();
            if(isset($res['result'])){
                return $res['result'];
            }
        } else {
            $err = $calc->getError();
            if( isset($err['error']) && !empty($err) ) {
                $error = '';
                foreach($err['error'] as $e) {
                    $error .= "Код ошибки: {$e['code']}\n";
                    $error .= "Текст ошибки: {$e['text']}\n\n";
                }
                throw new Exception($error);
            }
        }
    }
}