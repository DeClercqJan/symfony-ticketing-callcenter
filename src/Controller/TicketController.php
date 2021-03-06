<?php

namespace App\Controller;

use App\Entity\Tag;
use App\Entity\Task;
use App\Entity\Ticket;
use App\Form\TaskType;
use App\Form\TicketType;
use App\Form\TicketTypeAssignSelf;
use App\Form\TicketTypeReopen;
use App\Repository\TicketRepository;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\Address;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @Route("/ticket")
 */
class TicketController extends AbstractController
{
    /**
     * @Route("/", name="ticket_index", methods={"GET"})
     */
    public function index(TicketRepository $ticketRepository): Response
    {
        return $this->render('ticket/index.html.twig', [
            'tickets' => $ticketRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="ticket_new", methods={"GET","POST"})
     */
    public function new(Request $request, Security $security): Response
    {
        $ticket = new Ticket();
        if (!$this->isGranted('TICKET_CREATE', $ticket)) {
            throw $this->createAccessDeniedException('No access!');
        }
        $form = $this->createForm(TicketType::class, $ticket);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $ticket->setPriorityLevel(0);
            $ticket->setExternalStatusMessage($ticket::EXTERNAL_STATUS_MESSAGE_OPEN);
            $ticket->setAuthor($security->getUser());
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($ticket);
            $entityManager->flush();

            return $this->redirectToRoute('ticket_index');
        }

        return $this->render('ticket/new.html.twig', [
            'ticket' => $ticket,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="ticket_show", methods={"GET"})
     */
    public function show(Ticket $ticket): Response
    {
        if (!$this->isGranted('TICKET_VIEW', $ticket)) {
            throw $this->createAccessDeniedException('No access!');
        }

        return $this->render('ticket/show.html.twig', [
            'ticket' => $ticket,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="ticket_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Ticket $ticket): Response
    {
        if (!$this->isGranted('TICKET_EDIT', $ticket)) {
            throw $this->createAccessDeniedException('No access!');
        }

        $form = $this->createForm(TicketType::class, $ticket);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            if ($ticket::EXTERNAL_STATUS_MESSAGE_CLOSED === $ticket->getExternalStatusMessage()) {
                $ticket->setCanReopenUntil();
            }

            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('ticket_index');
        }

        return $this->render('ticket/edit.html.twig', [
            'ticket' => $ticket,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="ticket_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Ticket $ticket): Response
    {
        if (!$this->isGranted('TICKET_DELETE', $ticket)) {
            throw $this->createAccessDeniedException('No access!');
        }

        if ($this->isCsrfTokenValid('delete' . $ticket->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($ticket);
            $entityManager->flush();
        }

        return $this->redirectToRoute('ticket_index');
    }

    /**
     * @Route("/{id}/reopen", name="ticket_reopen", methods={"GET","POST"})
     */
    public function reopen(Request $request, Ticket $ticket): Response
    {
        if (!$this->isGranted('TICKET_REOPEN', $ticket)) {
            throw $this->createAccessDeniedException('No access!');
        }

        if ($ticket->getCanReopenUntil()) {
            throw $this->createAccessDeniedException('You can only reopen a ticket up until an hour after it has been been closed !');
        }

        $form = $this->createForm(TicketTypeReopen::class, $ticket);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // dd($form);
            $ticket->setExternalStatusMessage($ticket::EXTERNAL_STATUS_MESSAGE_OPEN);
            $entityManager = $this->getDoctrine()->getManager();
            // persisting related comments happens by way of cascade="persist" annotation
            $entityManager->persist($ticket);
            $entityManager->flush();
            return $this->redirectToRoute('ticket_index');
        }

        return $this->render('ticket/reopen.html.twig', [
            'ticket' => $ticket,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/assign_self", name="ticket_assign_self", methods={"GET","POST"})
     */
    public function assignSelf(Request $request, Ticket $ticket): Response
    {
        if (!$this->isGranted('TICKET_ASSIGN_SELF', $ticket)) {
            throw $this->createAccessDeniedException('No access!');
        }

        $form = $this->createForm(TicketTypeAssignSelf::class, $ticket);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $ticket->setExternalStatusMessage($ticket::EXTERNAL_STATUS_MESSAGE_PROGRESS_);
            $ticket->addUser($this->getUser());


            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('ticket_index');
        }

        return $this->render('ticket/assign_self.html.twig', [
            'ticket' => $ticket,
            'form' => $form->createView(),
        ]);
    }
}
