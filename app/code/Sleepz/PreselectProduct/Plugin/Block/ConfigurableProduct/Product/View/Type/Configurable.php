<?php

namespace Sleepz\PreselectProduct\Plugin\Block\ConfigurableProduct\Product\View\Type;

use Sleepz\PreselectProduct\Model\Product\Defaults;
use Magento\Framework\App\RequestInterface;
use \Magento\Framework\Json\EncoderInterface;
use \Magento\Framework\Json\DecoderInterface;

class Configurable
{

    /**
     * @var EncoderInterface
     */
    private $jsonEncoder;

    /**
     * @var DecoderInterface
     */
    private $jsonDecoder;

    /**
     * @var Defaults
     */
    private $productDefaults;

    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var \Magento\ConfigurableProduct\Block\Product\View\Type\Configurable
     */
    private $subject;

    /**
     * @param EncoderInterface           $jsonEncoder
     * @param DecoderInterface           $jsonDecoder
     * @param Defaults                   $productDefaults
     * @param RequestInterface           $request
     */
    public function __construct(
        EncoderInterface $jsonEncoder,
        DecoderInterface $jsonDecoder,
        Defaults $productDefaults,
        RequestInterface $request
    ) {
        $this->jsonEncoder                 = $jsonEncoder;
        $this->jsonDecoder                 = $jsonDecoder;
        $this->productDefaults             = $productDefaults;
        $this->request                     = $request;
    }

    public function afterGetJsonConfig(
        \Magento\ConfigurableProduct\Block\Product\View\Type\Configurable $subject,
        $result
    ) {
        $this->subject = $subject;
        $config = $this->jsonDecoder->decode($result);

        $productId = $this->subject->getProduct()->getId();
        $usedProductId = $this->productDefaults->getDefaultProductId($this->subject->getProduct());

        if(!isset($config['defaultValues'])) {
            $config['defaultValues'] = $this->prepareDefaultValues($config, $usedProductId);
        }

        $result = $this->jsonEncoder->encode($config);

        return $result;
    }

    private function prepareDefaultValues($config, $productId)
    {
        $defaultValues = [];
        $simpleProductFound = true;
        foreach ($config['attributes'] as $attributeId => $attribute) {
            $attributeProductFound = false;
            foreach ($attribute['options'] as $option) {
                $optionId = $option['id'];
                if (in_array($productId, $option['products'])) {
                    $defaultValues[$attributeId] = $optionId;
                    $attributeProductFound = true;
                    break;
                }
            }
            if(!$attributeProductFound) {
                $simpleProductFound = false;
                break;
            }
        }
        if(!$simpleProductFound) {
            foreach ($config['attributes'] as $attributeId => $attribute) {
                $defaultValues[$attributeId] = $attribute['options'][0]['id'];
            }
        }

        return $defaultValues;
    }
}
