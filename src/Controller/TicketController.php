<?php

namespace App\Controller;

use App\Entity\Ticket;
use App\Repository\TicketRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;

class TicketController extends AbstractController
{
    /**
     * @Route("/ticket", name="ticket_home")
     */
    public function home(TicketRepository $ticketRepository)
    {
        $tickets = $ticketRepository->findAll();

        return $this->render('ticket/index.html.twig', [
            'controller_name' => 'TicketController',
            'tickets' => $tickets,
        ]);
    }

    /**
     * @Route("/ticket/{id}", name="ticket_show")
     */
    public function show(Ticket $ticket)
    {
        return $this->render('ticket/show.html.twig', [
            'controller_name' => 'TicketController',
            'ticket' => $ticket,
        ]);
    }

    /**
     * @Route("/email")
     */
    public function sendEmail(MailerInterface $mailer)
    {
        $email = (new Email())
            ->from('hello@example.com')
            ->to('eddyeddyeddyborremans@gmail.com')
            //->cc('cc@example.com')
            //->bcc('bcc@example.com')
            //->replyTo('fabien@example.com')
            //->priority(Email::PRIORITY_HIGH)
            ->subject('Time for Symfony Mailer!')
            ->text('Sending emails is fun again!')
            ->html('<p>Hallowkes See Twig integration for better HTML integration!</p>');

        $mailer->send($email);

        dd('great succes!');
    }
}
