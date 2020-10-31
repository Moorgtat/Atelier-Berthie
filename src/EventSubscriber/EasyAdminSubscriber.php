<?php

namespace App\EventSubscriber;

use App\Entity\Product;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityUpdatedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityPersistedEvent;

class EasyAdminSubscriber implements EventSubscriberInterface 
{
    private $appKernel;

    public function __construct(KernelInterface $appKernel)
    {
        $this->appKernel = $appKernel;
    }

    public static function getSubscribedEvents()
    {
        return [
            BeforeEntityPersistedEvent::class => ['setCover'],
            BeforeEntityUpdatedEvent::class => ['updateCover']
        ];
    }

    public function uploadCover($event)
    {
        $entity = $event->getEntityInstance();
        $tmp_name = $_FILES['Product']['tmp_name']['cover'];
        $filename = uniqid();
        $extension = pathinfo($_FILES['Product']['name']['cover'], PATHINFO_EXTENSION);
        
        $project_dir = $this->appKernel->getProjectDir();

        move_uploaded_file($tmp_name, $project_dir.'/public/uploads/'.$filename.'.'.$extension);

        $entity->setCover($filename.'.'.$extension);
    }

    public function updateCover(BeforeEntityUpdatedEvent $event)
    {
        if(!($event->getEntityInstance() instanceof Product))
        {
            return;
        }

        if($_FILES['Product']['tmp_name']['cover'] != "") {
            $this->uploadCover($event);
        }
    }


    public function setCover(BeforeEntityPersistedEvent $event)
    {
        if(!($event->getEntityInstance() instanceof Product))
        {
            return;
        }
        
        $this->uploadCover($event);
    }
}