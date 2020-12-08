<?php

namespace App\EventSubscriber;

use ErrorException;
use App\Entity\Produit;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityDeletedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityUpdatedEvent;

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
            BeforeEntityDeletedEvent::class => ['deleteEntity']
        ];
    }

    public function deleteEntity(BeforeEntityDeletedEvent $event)
    {
        if(!($event->getEntityInstance() instanceof Produit))
        {
            return;
        }

        $reflexion = new \ReflectionClass($event->getEntityInstance());
        $entityName = $reflexion->getShortName();
        $filesystem = new Filesystem();
   
        if ($entityName == 'Produit')
            {
                $entity = $event->getEntityInstance();
                $project_dir = $this->appKernel->getProjectDir();
                $oldCouverture = $entity->getCouverture();
                $oldImage_1 = $entity->getImage1();
                $oldImage_2 = $entity->getImage2();
                $oldImage_3 = $entity->getImage3();
                try {
                    $filesystem->remove([$project_dir.'/public/uploads/'.$oldCouverture]);
                    $filesystem->remove([$project_dir.'/public/uploads/'.$oldImage_1]);
                    $filesystem->remove([$project_dir.'/public/uploads/'.$oldImage_2]);
                    $filesystem->remove([$project_dir.'/public/uploads/'.$oldImage_3]);
                } catch (ErrorException $e) {
                    echo "Une erreur s'est produite durant la suppression du produit.";
                } catch (IOException $e) {
                    echo "Une erreur s'est produite durant la suppression du produit.";
                }
            }
    }
}