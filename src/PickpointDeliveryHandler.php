<?php
/**
 * @link https://cms.skeeks.com/
 * @copyright Copyright (c) 2010 SkeekS
 * @license https://cms.skeeks.com/license/
 * @author Semenov Alexander <semenov@skeeks.com>
 */

namespace skeeks\cms\shop\pickpoint;

use skeeks\cms\shop\delivery\DeliveryHandler;
use skeeks\cms\shop\models\ShopOrder;
use skeeks\cms\shop\widgets\admin\SmartWeightInputWidget;
use skeeks\yii2\form\fields\FieldSet;
use skeeks\yii2\form\fields\NumberField;
use skeeks\yii2\form\fields\WidgetField;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;

/**
 * @author Semenov Alexander <semenov@skeeks.com>
 */
class PickpointDeliveryHandler extends DeliveryHandler
{
    /**
     * @var string
     */
    public $ikn = '';

    public $custom_city = 'Москва';
    public $weight = 1000;

    public $height = 20;
    public $width = 20;
    public $depth = 20;

    /**
     * @var string
     */
    public $checkoutModelClass = PickpointCheckoutModel::class;

    /**
     * @return array
     */
    static public function descriptorConfig()
    {
        return array_merge(parent::descriptorConfig(), [
            'name' => \Yii::t('skeeks/shop/app', 'Pickpoint'),
        ]);
    }


    public function rules()
    {
        return ArrayHelper::merge(parent::rules(), [
            [['ikn'], 'integer'],

            /*[['api_key'], 'required'],
            [['custom_city'], 'string'],
            [['api_key'], 'string'],

            [['weight'], 'integer'],

            [['height'], 'integer'],
            [['width'], 'integer'],
            [['depth'], 'integer'],*/
        ]);
    }

    public function attributeLabels()
    {
        return ArrayHelper::merge(parent::attributeLabels(), [
            'ikn'     => "Индивидуальный клиентский номер в PickPoint",

            /*'api_key'     => "Ключ api",

            'custom_city' => "Город",
            'weight' => "Вес заказа",

            'height' => "Высота коробки заказа",
            'width'  => "Ширина коробки заказа",
            'depth'  => "Глубина коробки заказа",*/
        ]);
    }

    public function attributeHints()
    {
        return ArrayHelper::merge(parent::attributeHints(), [
            'ikn' => "Необходимо получить в PickPoint",
        ]);
    }


    /**
     * @return array
     */
    public function getConfigFormFields()
    {
        return [
            'main'    => [
                'class'  => FieldSet::class,
                'name'   => 'Основные',
                'fields' => [
                    'ikn',
                ],
            ],
            /*'default' => [
                'class'  => FieldSet::class,
                'name'   => 'Данные по умолчанию',
                'fields' => [
                    'custom_city',

                    'weight' => [
                        'class' => WidgetField::class,
                        'widgetClass' => SmartWeightInputWidget::class
                    ],

                    'height' => [
                        'class' => NumberField::class,
                        'append' => 'см.'
                    ],

                    'width' => [
                        'class' => NumberField::class,
                        'append' => 'см.'
                    ],

                    'depth' => [
                        'class' => NumberField::class,
                        'append' => 'см.'
                    ]
                ],
            ],*/
        ];
    }


    /**
     * @param ActiveForm $activeForm
     * @return string
     */
    public function renderCheckoutForm(ActiveForm $activeForm, ShopOrder $shopOrder)
    {
        //\Yii::$app->view->registerJsFile("//pickpoint.ru/select/postamat.js");

        /*$apiKey = $this->api_key;
        $custom_city = $this->custom_city;

        $weight = $shopOrder->weight ? $shopOrder->weight : 1000;
        $money = (float)$shopOrder->money->amount;*/

        \Yii::$app->view->registerJs(<<<JS
if (!$("#sx-postamat-js").length) {
    var script = document.createElement('script');
    script.src = "//pickpoint.ru/select/postamat.js";
    script.id = "sx-postamat-js";
    document.head.append(script);
    
    /*script.onload = function() {
      // в скрипте создаётся вспомогательная функция с именем "_"
      alert("pickpoint"); // функция доступна
    };*/
}




$("#sx-pickpoint-open").on("click", function() {
    console.log("sx-pickpoint-open");
    PickPoint.open(callback_function, {ikn: '{$this->ikn}'});
    return false;
});

function callback_function(result){
    
    console.log(result);
    
    
    var data = JSON.stringify(result);
    $("#shoporder-delivery_handler_data_jsoned").empty().append(data).change();
    /*if (result.prepaid == '1') {
        alert('Отделение работает только по предоплате!');
    }*/
}
JS
        );

        $result = '<div style="display: none;">';
        $result .= $activeForm->field($shopOrder->deliveryHandlerCheckoutModel, "id");
        $result .= '</div>';

        if ($shopOrder->deliveryHandlerCheckoutModel && $shopOrder->deliveryHandlerCheckoutModel instanceof PickpointCheckoutModel && $shopOrder->deliveryHandlerCheckoutModel->id) {
            $result .= <<<HTML
            <div>Адрес: {$shopOrder->deliveryHandlerCheckoutModel->address}</div>
            <div>{$shopOrder->deliveryHandlerCheckoutModel->name}</div>
            <a href="#" class="sx-dashed" id="sx-pickpoint-open">Изменить пункт выдачи Pickpoint</a>
HTML;
        } else {
            $result .= <<<HTML
            <a href="#" class="sx-dashed" id="sx-pickpoint-open" style="color: red;">Выбрать пункт выдачи Pickpoint</a>
HTML;
        }


        return $result;
    }

}