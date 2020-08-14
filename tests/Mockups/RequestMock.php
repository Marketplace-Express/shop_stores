<?php
/**
 * User: Wajdi Jurry
 * Date: ١٤‏/٨‏/٢٠٢٠
 * Time: ١١:٥٠ ص
 */

namespace App\Tests\Mockups;


use Symfony\Component\HttpFoundation\Request;

class RequestMock extends Request
{
    public function __set($name, $value)
    {
        $this->attributes->set($name, $value);
    }

    public function getContent(bool $asResource = false)
    {
        return $this->content;
    }

    public function setContent($content)
    {
        $this->content = $content;
    }
}