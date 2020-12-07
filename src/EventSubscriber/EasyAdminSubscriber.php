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
            BeforeEntityUpdatedEvent::class => ['updateEntity'],
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

    public function updateEntity(BeforeEntityUpdatedEvent $event)
    {
        if(!($event->getEntityInstance() instanceof Produit))
        {
            return;
        }

        $reflexion = new \ReflectionClass($event->getEntityInstance());
        $entityName = $reflexion->getShortName();
        $filesystem = new Filesystem();
        $project_dir = $this->appKernel->getProjectDir();
   
        if ($entityName == 'Produit')
        {
            if($_FILES[$entityName]['tmp_name']['couverture']['file'] != "") {
                $entity = $event->getEntityInstance();
                $tmp_name_couverture = $_FILES['Produit']['tmp_name']['couverture']['file'];
                $filename_couverture = uniqid();
                $extension_couverture = pathinfo($_FILES['Produit']['name']['couverture']['file'], PATHINFO_EXTENSION);
                $oldCouverture = $entity->getCouverture();
                move_uploaded_file($tmp_name_couverture, $project_dir.'/public/uploads/'.$filename_couverture.'.'.$extension_couverture);
                $entity->setCouverture($filename_couverture.'.'.$extension_couverture);
                try {
                $filesystem->remove([$project_dir.'/public/uploads/'.$oldCouverture]);
                } catch (ErrorException $e) {
                    echo "Une erreur s'est produite durant la mise a jour du produit.";
                } catch (IOException $e) {
                    echo "Une erreur s'est produite durant la mise a jour du produit.";
                }
            }

            if($_FILES[$entityName]['tmp_name']['image_1']['file'] != "") {
                $entity = $event->getEntityInstance();
                $tmp_name_image1 = $_FILES['Produit']['tmp_name']['image_1']['file'];
                $filename_image1 = uniqid();
                $extension_image1 = pathinfo($_FILES['Produit']['name']['image_1']['file'], PATHINFO_EXTENSION);
                $oldImage_1 = $entity->getImage1();
                try {
                $filesystem->remove([$project_dir.'/public/uploads/'.$oldImage_1]);
                } catch (ErrorException $e) {
                    echo "Une erreur s'est produite durant la mise a jour du produit.";
                }catch (IOException $e) {
                    echo "Une erreur s'est produite durant la mise a jour du produit.";
                }
                move_uploaded_file($tmp_name_image1, $project_dir.'/public/uploads/'.$filename_image1.'.'.$extension_image1);
                $entity->setImage1($filename_image1.'.'.$extension_image1);
            }

            if($_FILES[$entityName]['tmp_name']['image_2']['file'] != "") {
                $entity = $event->getEntityInstance();
                $tmp_name_image2 = $_FILES['Produit']['tmp_name']['image_2']['file'];
                $filename_image2 = uniqid();
                $extension_image2 = pathinfo($_FILES['Produit']['name']['image_2']['file'], PATHINFO_EXTENSION);
                $oldImage_2 = $entity->getImage2();
                try {
                $filesystem->remove([$project_dir.'/public/uploads/'.$oldImage_2]);
                } catch (ErrorException $e) {
                    echo "Une erreur s'est produite durant la mise a jour du produit.";
                } catch (IOException $e) {
                    echo "Une erreur s'est produite durant la mise a jour du produit.";
                }
                move_uploaded_file($tmp_name_image2, $project_dir.'/public/uploads/'.$filename_image2.'.'.$extension_image2);
                $entity->setImage2($filename_image2.'.'.$extension_image2);
            }

            if($_FILES[$entityName]['tmp_name']['image_3']['file'] != "") {
                $entity = $event->getEntityInstance();
                $tmp_name_image3 = $_FILES['Produit']['tmp_name']['image_3']['file'];
                $filename_image3 = uniqid();
                $extension_image3 = pathinfo($_FILES['Produit']['name']['image_3']['file'], PATHINFO_EXTENSION);
                $oldImage_3 = $entity->getImage3();
                try {
                $filesystem->remove([$project_dir.'/public/uploads/'.$oldImage_3]);
                } catch (ErrorException $e) {
                    echo "Une erreur s'est produite durant la mise a jour du produit.";
                } catch (IOException $e) {
                    echo "Une erreur s'est produite durant la mise a jour du produit.";
                }
                move_uploaded_file($tmp_name_image3, $project_dir.'/public/uploads/'.$filename_image3.'.'.$extension_image3);
                $entity->setImage3($filename_image3.'.'.$extension_image3);
            }
        }
    }

}