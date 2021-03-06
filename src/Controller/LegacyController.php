<?php

namespace App\Controller;

use App\Doctrine\MemberStatusType;
use App\Entity\Member;
use Doctrine\DBAL\Statement;
use EnvironmentExplorer;
use PDO;
use Rox\Framework\SessionSingleton;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class LegacyController extends AbstractController
{
    /**
     * @param Request            $request
     * @param ContainerInterface $container
     *
     * @throws AccessDeniedException
     *
     * @return Response
     *
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function showAction(Request $request, ContainerInterface $container)
    {
        // Kick-start the Symfony session. This replaces session_start() in the
        // old code, which is now turned off.
        /** @var Session $session */
        $session = $container->get('session');
        $session->start();

        // Make sure the Rox classes find this session
        SessionSingleton::createInstance($session);

        $environmentExplorer = new EnvironmentExplorer();
        $environmentExplorer->initializeGlobalState(
            $container->getParameter('database_host'),
            $container->getParameter('database_name'),
            $container->getParameter('database_user'),
            $container->getParameter('database_password')
        );

        $pathInfo = $request->getPathInfo();
        $public = (false === strpos($pathInfo, '/safety')) ||
            (false === strpos($pathInfo, '/about')) ||
            (false === strpos($pathInfo, '/signup'));
        if (!$session->has('IdMember')) {
            $rememberMeToken = unserialize($session->get('_security_default'));
            if (null === $rememberMeToken && !$public) {
                throw new AccessDeniedException();
            }
            if (false !== $rememberMeToken) {
                /** @var Member $user */
                $user = $rememberMeToken->getUser();
                if (null !== $user) {
                    $session->set('IdMember', $user->getId());
                    // \todo Status isn't set correctly. Force for now.
                    $session->set('MemberStatus', MemberStatusType::ACTIVE);
                    $session->set('Username', $user->getUsername());
                    $connection = $this->getDoctrine()->getConnection();
                    /** @var Statement $stmt */
                    $stmt = $connection->prepare('
                        SELECT 
                            id
                        FROM
                            user
                        WHERE
                            handle = :username
                    ');
                    $stmt->execute([':username' => $user->getUsername()]);
                    $id = $stmt->fetch(PDO::FETCH_COLUMN);
                    $session->set('APP_User_id', $id);
                }
            }
        }

        $kernel = $container->get('rox.legacy_kernel');

        return $kernel->handle(
            $request
        );
    }
}
