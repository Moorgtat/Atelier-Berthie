<?php

namespace App\EventSubscriber;

use ErrorException;
use App\Entity\Header;
use App\Entity\Produit;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityDeletedEvent;
use Symfony\Component\Filesystem\Filesystem;
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
            BeforeEntityPersistedEvent::class => ['setEntity'],
            BeforeEntityUpdatedEvent::class => ['updateEntity'],
            BeforeEntityDeletedEvent::class => ['deleteEntity']
        ];
    }

    public function uploadEntity($event, $entityName)
    {
        if ($entityName == 'Produit') 
        {
        $entity = $event->getEntityInstance();

        $tmp_name_couverture = $_FILES['Produit']['tmp_name']['couverture'];
        $tmp_name_image1 = $_FILES['Produit']['tmp_name']['image_1'];
        $tmp_name_image2 = $_FILES['Produit']['tmp_name']['image_2'];
        $tmp_name_image3 = $_FILES['Produit']['tmp_name']['image_3'];

        $filename_couverture = uniqid();
        $filename_image1 = uniqid();
        $filename_image2 = uniqid();
        $filename_image3 = uniqid();

        $extension_couverture = pathinfo($_FILES['Produit']['name']['couverture'], PATHINFO_EXTENSION);
        $extension_image1 = pathinfo($_FILES['Produit']['name']['image_1'], PATHINFO_EXTENSION);
        $extension_image2 = pathinfo($_FILES['Produit']['name']['image_2'], PATHINFO_EXTENSION);
        $extension_image3 = pathinfo($_FILES['Produit']['name']['image_3'], PATHINFO_EXTENSION);
        
        $project_dir = $this->appKernel->getProjectDir();

        move_uploaded_file($tmp_name_couverture, $project_dir.'/public/uploads/'.$filename_couverture.'.'.$extension_couverture);
        move_uploaded_file($tmp_name_image1, $project_dir.'/public/uploads/'.$filename_image1.'.'.$extension_image1);
        move_uploaded_file($tmp_name_image2, $project_dir.'/public/uploads/'.$filename_image2.'.'.$extension_image2);
        move_uploaded_file($tmp_name_image3, $project_dir.'/public/uploads/'.$filename_image3.'.'.$extension_image3);

        $entity->setCouverture($filename_couverture.'.'.$extension_couverture);
        $entity->setImage1($filename_image1.'.'.$extension_image1);
        $entity->setImage2($filename_image2.'.'.$extension_image2);
        $entity->setImage3($filename_image3.'.'.$extension_image3);
        }
        elseif ($entityName == 'Header')
        {
            $entity = $event->getEntityInstance();
            $tmp_name_image = $_FILES[$entityName]['tmp_name']['image'];
            $filename_image = uniqid();
            $extension_image = pathinfo($_FILES[$entityName]['name']['image'], PATHINFO_EXTENSION);
            $project_dir = $this->appKernel->getProjectDir();
            move_uploaded_file($tmp_name_image, $project_dir.'/public/uploads/'.$filename_image.'.'.$extension_image);
            $entity->setImage($filename_image.'.'.$extension_image);

        }
    }

    public function deleteEntity(BeforeEntityDeletedEvent $event)
    {
        if(!($event->getEntityInstance() instanceof Produit || $event->getEntityInstance() instanceof Header))
        {
            return;
        }

        $reflexion = new \ReflectionClass($event->getEntityInstance());
        $entityName = $reflexion->getShortName();
        $filesystem = new Filesystem();

        if ($entityName == 'Header') 
            {
                $entity = $event->getEntityInstance();
                $project_dir = $this->appKernel->getProjectDir();
                $oldImage = $entity->getImage();
                try {
                $filesystem->remove([$project_dir.'/public/uploads/'.$oldImage]);
                } catch (ErrorException $e) {
                    echo "Une erreur s'est produite durant la suppression de l'header.";
                }
            }    
        elseif ($entityName == 'Produit')
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
                }
            }
    }

    public function updateEntity(BeforeEntityUpdatedEvent $event)
    {
        if(!($event->getEntityInstance() instanceof Produit || $event->getEntityInstance() instanceof Header))
        {
            return;
        }

        $reflexion = new \ReflectionClass($event->getEntityInstance());
        $entityName = $reflexion->getShortName();
        $filesystem = new Filesystem();

        if ($entityName == 'Header') 
        {
            if($_FILES[$entityName]['tmp_name']['image'] != "") {
                $entity = $event->getEntityInstance();
                $tmp_name_image = $_FILES[$entityName]['tmp_name']['image'];
                $filename_image = uniqid();
                $extension_image = pathinfo($_FILES[$entityName]['name']['image'], PATHINFO_EXTENSION);
                $project_dir = $this->appKernel->getProjectDir();
                $oldImage = $entity->getImage();
                try {
                $filesystem->remove([$project_dir.'/public/uploads/'.$oldImage]);
                } catch (ErrorException $e) {
                    echo "Une erreur s'est produite durant la mise a jour de l'header.";
                }
                move_uploaded_file($tmp_name_image, $project_dir.'/public/uploads/'.$filename_image.'.'.$extension_image);
                $entity->setImage($filename_image.'.'.$extension_image);
            }
        }    
        elseif ($entityName == 'Produit')
        {
            if($_FILES[$entityName]['tmp_name']['couverture'] != "") {
                $entity = $event->getEntityInstance();
                $tmp_name_couverture = $_FILES['Produit']['tmp_name']['couverture'];
                $filename_couverture = uniqid();
                $extension_couverture = pathinfo($_FILES['Produit']['name']['couverture'], PATHINFO_EXTENSION);
                $project_dir = $this->appKernel->getProjectDir();
                $oldCouverture = $entity->getCouverture();
                try {
                $filesystem->remove([$project_dir.'/public/uploads/'.$oldCouverture]);
                } catch (ErrorException $e) {
                    echo "Une erreur s'est produite durant la mise a jour du produit.";
                }
                move_uploaded_file($tmp_name_couverture, $project_dir.'/public/uploads/'.$filename_couverture.'.'.$extension_couverture);

                $entity->setCouverture($filename_couverture.'.'.$extension_couverture);
            }

            if($_FILES[$entityName]['tmp_name']['image_1'] != "") {
                $entity = $event->getEntityInstance();
                $tmp_name_image1 = $_FILES['Produit']['tmp_name']['image_1'];
                $filename_image1 = uniqid();
                $extension_image1 = pathinfo($_FILES['Produit']['name']['image_1'], PATHINFO_EXTENSION);
                $project_dir = $this->appKernel->getProjectDir();
                $oldImage_1 = $entity->getImage1();
                try {
                $filesystem->remove([$project_dir.'/public/uploads/'.$oldImage_1]);
                } catch (ErrorException $e) {
                    echo "Une erreur s'est produite durant la mise a jour du produit.";
                }
                move_uploaded_file($tmp_name_image1, $project_dir.'/public/uploads/'.$filename_image1.'.'.$extension_image1);
                $entity->setImage1($filename_image1.'.'.$extension_image1);
            }

            if($_FILES[$entityName]['tmp_name']['image_2'] != "") {
                $entity = $event->getEntityInstance();
                $tmp_name_image2 = $_FILES['Produit']['tmp_name']['image_2'];
                $filename_image2 = uniqid();
                $extension_image2 = pathinfo($_FILES['Produit']['name']['image_2'], PATHINFO_EXTENSION);
                $project_dir = $this->appKernel->getProjectDir();
                $oldImage_2 = $entity->getImage2();
                try {
                $filesystem->remove([$project_dir.'/public/uploads/'.$oldImage_2]);
                } catch (ErrorException $e) {
                    echo "Une erreur s'est produite durant la mise a jour du produit.";
                }
                move_uploaded_file($tmp_name_image2, $project_dir.'/public/uploads/'.$filename_image2.'.'.$extension_image2);
                $entity->setImage2($filename_image2.'.'.$extension_image2);
            }

            if($_FILES[$entityName]['tmp_name']['image_3'] != "") {
                $entity = $event->getEntityInstance();
                $tmp_name_image3 = $_FILES['Produit']['tmp_name']['image_3'];
                $filename_image3 = uniqid();
                $extension_image3 = pathinfo($_FILES['Produit']['name']['image_3'], PATHINFO_EXTENSION);
                $project_dir = $this->appKernel->getProjectDir();
                $oldImage_3 = $entity->getImage3();
                try {
                $filesystem->remove([$project_dir.'/public/uploads/'.$oldImage_3]);
                } catch (ErrorException $e) {
                    echo "Une erreur s'est produite durant la mise a jour du produit.";
                }
                move_uploaded_file($tmp_name_image3, $project_dir.'/public/uploads/'.$filename_image3.'.'.$extension_image3);
                $entity->setImage3($filename_image3.'.'.$extension_image3);
            }
        }
    }

    public function setEntity(BeforeEntityPersistedEvent $event)
    {
        if(!($event->getEntityInstance() instanceof Produit || $event->getEntityInstance() instanceof Header))
        {
            return;
        }
        
        $reflexion = new \ReflectionClass($event->getEntityInstance());
        $entityName = $reflexion->getShortName();

        $this->uploadEntity($event, $entityName);
    }

}