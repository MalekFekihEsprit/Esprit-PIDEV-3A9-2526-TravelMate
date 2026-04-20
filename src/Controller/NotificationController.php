<?php

namespace App\Controller;

use App\Entity\Notification;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController; // 👈 Add this
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class NotificationController extends AbstractController
{

    #[Route('/notifications', name: 'notifications_list')]
    public function list(EntityManagerInterface $em): Response
    {
        $notifications = $em->getRepository(Notification::class)
            ->findBy([], ['createdAt'=>'DESC']);

        return $this->render('notification/list.html.twig',[
            'notifications'=>$notifications
        ]);
    }

    #[Route('/notifications/delete/{id}', name: 'notification_delete')]
    public function delete(Notification $notification, EntityManagerInterface $em)
    {
        $em->remove($notification);
        $em->flush();

        return $this->redirectToRoute('notifications_list');
    }

}