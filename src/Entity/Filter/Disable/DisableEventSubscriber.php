<?php
/**
 * User: Wajdi Jurry
 * Date: ١‏/٥‏/٢٠٢٠
 * Time: ١٢:٣٧ ص
 */

namespace App\Entity\Filter\Disable;


use App\Entity\Interfaces\DisableInterface;
use App\Exception\DisabledEntityException;
use Doctrine\Common\EventArgs;
use Doctrine\Common\EventSubscriber;

class DisableEventSubscriber implements EventSubscriber
{

    /**
     * @return string[]
     */
    public function getSubscribedEvents()
    {
        return [
            'preDisable',
            'postLoad'
        ];
    }

    /**
     * @param EventArgs $args
     */
    public function preDisable(EventArgs $args)
    {
        $entity = $args->getObject();
        $entity->setDisabledAt(new \DateTime());
    }

    /**
     * @param EventArgs $args
     * @throws DisabledEntityException
     */
    public function postLoad(EventArgs $args)
    {
        $entity = $args->getObject();
        $eventManager = $args->getObjectManager();

        if (!$entity instanceof DisableInterface) {
            return;
        }

        if (!$eventManager->getConfiguration()->getDefaultQueryHint('withDisabled') && $entity->getDisabledAt()) {
            throw new DisabledEntityException();
        }
    }
}