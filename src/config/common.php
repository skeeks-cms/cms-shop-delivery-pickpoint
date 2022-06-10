<?php
return [
    'components' => [
        'shop' => [
            'deliveryHandlers'             => [
                'pickpoint' => [
                    'class' => \skeeks\cms\shop\pickpoint\PickpointDeliveryHandler::class
                ]

            ]
        ],
    ],
];