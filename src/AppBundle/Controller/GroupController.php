<?php

namespace AppBundle\Controller;

use AppBundle\Doctrine\GroupMembershipStatusType;
use AppBundle\Entity\Group;
use AppBundle\Entity\GroupMembership;
use AppBundle\Entity\Language;
use AppBundle\Entity\Member;
use AppBundle\Entity\MembersTrad;
use AppBundle\Form\CustomDataClass\GroupRequest;
use AppBundle\Form\GroupType;
use AppBundle\Repository\GroupRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Swift_Message;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class GroupController extends Controller
{
    /**
     * @Route("/groups/new", name="new_group")
     *
     * @param Request $request
     *
     * @return Response
     *
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     * Because of the mix between old code and new code this method is way too long.
     */
    public function createNewGroupAction(Request $request)
    {
        $groupRequest = new GroupRequest();
        $form = $this->createForm(GroupType::class, $groupRequest);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $member = $this->getUser();
            // if a file was uploaded move it into the image storage
            /** @var UploadedFile $file */
            $file = $data->picture;
            $fileName = '';
            if (null !== $file) {
                $fileName = $this->generateUniqueFileName().'.'.$file->guessExtension();

                // moves the file to the directory where brochures are stored
                $file->move(
                    $this->getParameter('group_directory'),
                    $fileName
                );
            }
            $em = $this->getDoctrine()->getManager();

            // \todo: This is convoluted due to having to support the old structure! When recoding groups this should be simpler
            // We need the current locale for the MembersTrad entity
            $languageRepository = $em->getRepository(Language::class);
            /** @var Language $language */
            $language = $languageRepository->findOneBy(['shortcode' => $request->getSession()->get('locale')]);
            /** @var Language $english */
            $english = $languageRepository->findOneBy(['shortcode' => 'en']);

            // We create the group entity and add the first member
            $group = new Group();
            $group
                ->setName($data->name)
                ->setType($data->type)
                ->setVisibleposts($data->membersOnly)
                ->setVisiblecomments(false)
                ->setMoreInfo('')
                ->setPicture($fileName)
            ;
            $em->persist($group);
            $em->flush();

            // Create the description as a member trad using the current language
            $description = new MembersTrad();
            $description
                ->setOwner($member)
                ->setIdTranslator($member->getId())
                ->setSentence($data->description)
                ->setIdrecord($group->getId())
                ->setLanguage($language);
            $em->persist($description);
            $em->flush();

            // Add a comment for the creator of the group in English
            $groupComment = new MembersTrad();
            $groupComment
                ->setOwner($member)
                ->setIdtranslator($member->getId())
                ->setSentence('Group creator')
                ->setIdrecord($group->getId())
                ->setLanguage($english);
            $em->persist($groupComment);
            $em->flush();

            $groupMembership = new GroupMembership();
            $groupMembership
                ->setStatus(GroupMembershipStatusType::CURRENT_MEMBER)
                ->addComment($groupComment)
                ->setGroup($group)
                ->setMember($member);

            $member->addGroupMembership($groupMembership);
            $group->addGroupMembership($groupMembership);

            // Link group and description
            $group->addDescription($description);
            $em->persist($group);
            $em->flush();

            // Now add the current member as admin for this group
            $connection = $this->getDoctrine()->getConnection();
            $stmt = $connection->prepare('
                REPLACE INTO 
                    `privilegescopes`
                SET
                    `Idmember` = :memberId,
                    `IdRole` = 2,
                    `IdPrivilege` = 3,
                    `IdType` = :groupId,
                    `updated` = :updated
            ');
            $stmt->execute([
                ':groupId' => $group->getId(),
                ':memberId' => $member->getId(),
                'updated' => (new \DateTime())->format('Y-m-d'),
            ]);

            $this->addFlash('notice', 'The group was created and is now awaiting approval. You get a notification with the result.');

            $this->sendNewGroupNotifications($group, $member);

            return $this->redirectToRoute('groups_overview');
        }

        return $this->render(':group:create.group.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/groups/new/check", name="new_group_check")
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function ajaxCheckNewGroupAction(Request $request)
    {
        $groupName = trim($request->request->get('name'));

        $html = '';
        if (!empty($groupName)) {
            $parts = explode(' ', $groupName);

            /** @var GroupRepository $groupRepository */
            $groupRepository = $this->getDoctrine()->getRepository(Group::class);
            $groups = $groupRepository->findByNameParts($parts);

            // Check if there are duplicate groups and provide a list of these

            $html = $this->renderView(':group:check.group.html.twig', [
                'groups' => $groups,
            ]);
        }

        return new JsonResponse([
            'html' => $html,
        ]);
    }

    /**
     * Allows to approved or dismiss group creation requests.
     *
     * @Route("/admin/groups/approval", name="admin_groups_approval")
     *
     * @return Response
     */
    public function approveGroupsAction()
    {
        if (!$this->isGranted([Member::ROLE_ADMIN_GROUP])) {
            throw $this->createAccessDeniedException('You need to have Group right to access this.');
        }

        // Fetch unapproved groups and decide on their fate
        $groupsRepository = $this->getDoctrine()->getRepository(Group::class);
        $groups = $groupsRepository->findBy(['approved' => Group::NOT_APPROVED]);

        return $this->render(':admin:groups/approve.html.twig', [
            'groups' => $groups,
        ]);
    }

    /**
     * Dismiss a group creation requests.
     *
     * @Route("/admin/groups/{id}/dismiss", name="admin_groups_dismiss")
     *
     * @param Request $request
     * @param Group   $group
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function dismissGroupAction(Request $request, Group $group)
    {
        if (!$this->isGranted([Member::ROLE_ADMIN_GROUP])) {
            throw $this->createAccessDeniedException('You need to have Group right to access this.');
        }

        $group->setApproved(Group::DISMISSED);
        $em = $this->getDoctrine()->getManager();
        $em->persist($group);
        $em->flush();

        $flashMessage = $this->get('translator')->trans('Dismissed group %name%', [
            '%name%' => $group->getName(),
        ]);

        $this->addFlash('notice', $flashMessage);

        $referrer = $request->headers->get('referer');

        return $this->redirect($referrer);
    }

    /**
     * Dismiss a group creation requests.
     *
     * @Route("/admin/groups/{id}/approve", name="admin_groups_approve")
     *
     * @param Request $request
     * @param Group   $group
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function approveGroupAction(Request $request, Group $group)
    {
        if (!$this->isGranted([Member::ROLE_ADMIN_GROUP])) {
            throw $this->createAccessDeniedException('You need to have Group right to access this.');
        }

        $group->setApproved(Group::APPROVED);
        $em = $this->getDoctrine()->getManager();
        $em->persist($group);
        $em->flush();

        $flashMessage = $this->get('rox.datacollector_translator')->trans('Approved creation for group %name% ', [
            '%name%' => $group->getName(),
        ]);
        $this->addFlash('notice', $flashMessage);

        $referrer = $request->headers->get('referer');

        return $this->redirect($referrer);
    }

    private function sendNewGroupNotifications(Group $group, Member $member)
    {
        $recipients = $this->getNewGroupNotificationRecipients();
        $subject = '[New Group] '.strip_tags($group->getName());
        $message = new Swift_Message();
        $message
            ->setSubject($subject)
            ->setFrom(
                [
                    'groups@bewelcome.org' => 'BeWelcome - Group Administration',
                ]
            )
            ->setTo($recipients)
            ->setBody(
                $this->renderView('emails/new.group.html.twig', [
                    'subject' => $subject,
                    'group' => $group,
                    'member' => $member,
                ]),
                'text/html'
            )
        ;
        $recipients = $this->get('mailer')->send($message);

        return (0 === $recipients) ? false : true;
    }

    private function getNewGroupNotificationRecipients()
    {
        $connection = $this->getDoctrine()->getConnection();
        $stmt = $connection->prepare("
            SELECT 
                m.Email 
            FROM 
                members m, 
                rightsvolunteers rv, 
                rights r 
            WHERE 
                r.Name = 'Group' 
                AND r.id = rv.IdRight 
                AND rv.Level = 10 
                AND rv.IdMember = m.id
                AND m.Status IN (".Member::ACTIVE_ALL.')
        ');
        $stmt->execute();
        $emails = $stmt->fetchAll();
        $recipients = [];
        foreach ($emails as $email) {
            if (!empty($email['Email'])) {
                $recipients[] = $email['Email'];
            }
        }

        return $recipients;
    }

    /**
     * @return string
     */
    private function generateUniqueFileName()
    {
        // md5() reduces the similarity of the file names generated by
        // uniqid(), which is based on timestamps
        return md5(uniqid());
    }
}