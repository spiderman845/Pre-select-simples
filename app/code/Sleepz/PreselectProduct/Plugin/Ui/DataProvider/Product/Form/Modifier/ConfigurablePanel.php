<?php

namespace Sleepz\PreselectProduct\Plugin\Ui\DataProvider\Product\Form\Modifier;

use Magento\ConfigurableProduct\Ui\DataProvider\Product\Form\Modifier\ConfigurablePanel as OriginalConfigurablePanel;
use Magento\Ui\Component\Form;

class ConfigurablePanel
{
    public function afterModifyMeta(
        OriginalConfigurablePanel $subject,
        $meta
    ) {

        if (isset(
            $meta[OriginalConfigurablePanel::GROUP_CONFIGURABLE]['children']
            [OriginalConfigurablePanel::CONFIGURABLE_MATRIX]['children']
            ['record']['children']
        )) {
            $defaultColumnData = [
                'default_container' => [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'componentType' => Form\Field::NAME,
                                'formElement' => Form\Element\Input::NAME,
                                'component' => 'Sleepz_PreselectProduct/js/form/element/radio-set',
                                'elementTmpl' => 'Sleepz_PreselectProduct/form/components/radio-set',
                                'dataType' => Form\Element\DataType\Text::NAME,
                                'label' => __('Default'),
                                'dataScope' => 'default_value',
                                'dataName'  => OriginalConfigurablePanel::CONFIGURABLE_MATRIX . '[default]'
                            ],
                        ],
                    ],
                ],
            ];
            $matrixChildren = $meta[OriginalConfigurablePanel::GROUP_CONFIGURABLE]['children']
            [OriginalConfigurablePanel::CONFIGURABLE_MATRIX]['children']
            ['record']['children'];

            $meta[OriginalConfigurablePanel::GROUP_CONFIGURABLE]['children']
            [OriginalConfigurablePanel::CONFIGURABLE_MATRIX]['children']
            ['record']['children'] = $defaultColumnData + $matrixChildren;
        }

        return $meta;
    }
}
