<?php
/**
 * User: Wajdi Jurry
 * Date: ١١‏/٥‏/٢٠٢٠
 * Time: ٢:١٦ ص
 */

namespace App\Services;


use App\Exception\UnableToInvokeException;
use App\Exception\ValidationFailed;
use PhpAmqpLib\Message\AMQPMessage;
use Psr\Log\LoggerInterface;

class ConsumerService
{
    /** @var ServiceFactory */
    private $factory;

    private $logger;

    /**
     * ConsumerService constructor.
     *
     * @param ServiceFactory $factory
     * @param LoggerInterface $logger
     */
    public function __construct(ServiceFactory $factory, LoggerInterface $logger)
    {
        $this->factory = $factory;
        $this->logger = $logger;
    }

    /**
     * @param object $message
     * @return mixed
     * @throws \App\Exception\ServiceNotFoundException
     */
    private function getCallableService(object $message)
    {
        return $this->factory
            ->setServiceName($message->service)
            ->createService();
    }

    /**
     * @param object $message
     * @throws ValidationFailed
     */
    private function validateMessage(object $message)
    {
        if (empty($message->service) || empty($message->method) || !is_array($message->params)) {
            throw new ValidationFailed(['message' => 'invalid message params']);
        }
    }

    /**
     * @param AMQPMessage $msg
     * @return mixed|void
     */
    public function execute(AMQPMessage $msg)
    {
        $message = json_decode($msg->getBody());

        if (!$message) {
            return;
        }

        try {
            $this->validateMessage($message);

            $callableService = $this->getCallableService($message);

            if (!is_callable([$callableService, $message->method])) {
                throw new UnableToInvokeException();
            }

            call_user_func_array([$callableService, $message->method], $message->params);
        } catch (\Throwable $exception) {
            $this->logError($exception);
        }
    }

    private function logError(\Throwable $exception)
    {
        $this->logger->error($exception->getMessage());
        $this->logger->info($exception->getTraceAsString());
    }
}