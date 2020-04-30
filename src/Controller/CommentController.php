<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Ticket;
use App\Form\CommentType;
use App\Repository\CommentRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CommentController extends AbstractController
{
    /**
     * @Route("ticket/all/comment", name="comment_index_all", methods={"GET"})
     */
    public function indexCommentsAllTickets(CommentRepository $commentRepository): Response
    {
        return $this->render('comment/index_all.html.twig', [
            'comments' => $commentRepository->findAll(),
        ]);
    }

    /**
     * @Route("ticket/{ticketid}/comment", name="comment_index_per_ticket", methods={"GET"})
     * @ParamConverter("ticket", options={"mapping": {"ticketid" : "id"}})
     */
    public function indexCommentsPerTicket(Ticket $ticket, CommentRepository $commentRepository): Response
    {
        $comment = $ticket->getComments();

        return $this->render('comment/index_per_ticket.html.twig', [
            'comments' => $comment,
            'ticket' => $ticket,
        ]);
    }

    /**
     * @Route("/ticket/{ticketid}/comment/new", name="comment_new", methods={"GET","POST"})
     * @ParamConverter("ticket", options={"mapping": {"ticketid" : "id"}})
     */
    public function new(Ticket $ticket, Request $request): Response
    {
        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $comment->setTicket($ticket);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($comment);
            $entityManager->flush();

            $ticketid = $ticket->getId();
            return $this->redirectToRoute('ticket_show', array('id' => $ticketid));
        }

        return $this->render('comment/new.html.twig', [
            'comment' => $comment,
            'form' => $form->createView(),
            'ticket' => $ticket
        ]);
    }

    /**
     * @Route("/ticket/{ticketid}/comment/{commentid}", name="comment_show", methods={"GET"})
     * @ParamConverter("ticket", options={"mapping": {"ticketid" : "id"}})
     * @ParamConverter("comment", options={"mapping": {"commentid" : "id"}})
     */
    public function show(Ticket $ticket, Comment $comment): Response
    {
//        dd($ticket);
        return $this->render('comment/show.html.twig', [
            'comment' => $comment,
        ]);
    }

    /**
     * @Route("/ticket/{ticketid}/comment/{commentid}/edit", name="comment_edit", methods={"GET","POST"})
     * @ParamConverter("ticket", options={"mapping": {"ticketid" : "id"}})
     * @ParamConverter("comment", options={"mapping": {"commentid" : "id"}})
     */
    public function edit(Request $request, Comment $comment): Response
    {
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('comment_index');
        }

        return $this->render('comment/edit.html.twig', [
            'comment' => $comment,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="comment_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Comment $comment): Response
    {
        if ($this->isCsrfTokenValid('delete'.$comment->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($comment);
            $entityManager->flush();
        }

        return $this->redirectToRoute('comment_index');
    }
}
