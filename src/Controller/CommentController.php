<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Ticket;
use App\Form\CommentType;
use App\Repository\CommentRepository;
use phpDocumentor\Reflection\Types\Context;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

class CommentController extends AbstractController
{
    use TargetPathTrait;

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
    public function new(Ticket $ticket, Request $request, MailerInterface $mailer, Security $security): Response
    {
        $comment = new Comment();
        if (!$this->isGranted('COMMENT_CREATE', $comment)) {
            throw $this->createAccessDeniedException('No access!');
        }

        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();

            $ticketAuthor = $ticket->getAuthor();
            $user = $security->getUser();
            $ticketid = $ticket->getId();
            if ($ticketAuthor === $user && $ticket::EXTERNAL_STATUS_MESSAGE_WAITING === $ticket->getExternalStatusMessage()) {
                $agent = $ticket->getUsers();
                if (!$agent->isEmpty()) {
                    $agentEmail = $agent->getEmail();
                }
                if ($agent->isEmpty()) {
                    $agentEmail = "customerrespondedbutnoagenthasbeenassigned@moshimoshicallcenter.com";
                }
                $email = (new TemplatedEmail())
                    ->from(new Address('customerresponded@moshimoshicallcenter.com', 'Reset Password Bot'))
                    ->to($agentEmail)
                    ->subject('customer responded to request for feedback')
                    ->htmlTemplate('comment/customer_responded_email.html.twig')
                    ->context(['ticketid' => $ticketid]);
                $mailer->send($email);

                $ticket->setExternalStatusMessage($ticket::EXTERNAL_STATUS_MESSAGE_PROGRESS_);
                $entityManager->persist($ticket);
            }
            $comment->setTicket($ticket);
            $entityManager->persist($comment);
            $entityManager->flush();

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
    public function show(Ticket $ticket, Comment $comment, Request $request): Response
    {
        if (!$this->isGranted('COMMENT_VIEW', $comment)) {
            throw $this->createAccessDeniedException('No access!');
        }

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
        if (!$this->isGranted('COMMENT_EDIT', $comment)) {
            throw $this->createAccessDeniedException('No access!');
        }

//        $uri = $request->headers->get('referer');
//        dd($uri);
//        $test = $this->saveTargetPath($request->getSession(), 'main', $uri);
//        dd($targetPath);
        // $previous_page = $request->headers->get('referer');
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
//            $targetPath = $this->getTargetPath($request->getSession(), 'main');
//            dd($targetPath);
//dd($form);
            return $this->redirectToRoute('ticket_show', array('id' => $comment->getTicket()->getId()));
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
        if (!$this->isGranted('COMMENT_DELETE', $comment)) {
            throw $this->createAccessDeniedException('No access!');
        }

        if ($this->isCsrfTokenValid('delete' . $comment->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($comment);
            $entityManager->flush();
        }

        return $this->redirectToRoute('comment_index');
    }
}
