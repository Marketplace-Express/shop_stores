<?php


namespace App\Controller\Validator\Store;


use Symfony\Component\Validator\Context\ExecutionContext;

abstract class CommonDataValidator
{
    /**
     * @param $text
     * @param ExecutionContext $context
     * @param string $field
     * @return bool
     */
    protected function validateDescriptionAndName($text, ExecutionContext $context, string $field): bool
    {
        if ($text != strip_tags($text)) {
            $context->addViolation(sprintf('%s contains invalid characters', $field));
            return false;
        }
        return true;
    }

    /**
     * @param $url
     * @param ExecutionContext $context
     * @return bool
     */
    protected function validatePhotoAndCoverUrls($url, ExecutionContext $context): bool
    {
        if ($url != strip_tags($url)) {
            $context->addViolation('url contains invalid characters');
            return false;
        }
        return true;
    }

    /**
     * @param $location
     * @param ExecutionContext $context
     * @return bool
     */
    protected function validateLocation($location, ExecutionContext $context): bool
    {
        if (!empty($location) && is_array($location)) {
            if (!array_key_exists('coordinates', $location)) {
                $context->addViolation('Invalid location');
                return false;
            }
        }
        return true;
    }
}