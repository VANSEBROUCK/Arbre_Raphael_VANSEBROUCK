<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\HttpKernel\Security\Firewall;

use Symfony\Component\Security\SecurityContext;
use Symfony\Component\Security\Authentication\AuthenticationManagerInterface;
use Symfony\Component\HttpKernel\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventInterface;
use Symfony\Component\Security\Authentication\Token\PreAuthenticatedToken;
use Symfony\Component\Security\Exception\AuthenticationException;
use Symfony\Component\HttpFoundation\Request;

/**
 * PreAuthenticatedListener is the base class for all listener that
 * authenticates users based on a pre-authenticated request (like a certificate
 * for instance).
 *
 * @author Fabien Potencier <fabien.potencier@symfony-project.com>
 */
abstract class PreAuthenticatedListener implements ListenerInterface
{
    protected $securityContext;
    protected $authenticationManager;
    protected $logger;

    public function __construct(SecurityContext $securityContext, AuthenticationManagerInterface $authenticationManager, LoggerInterface $logger = null)
    {
        $this->securityContext = $securityContext;
        $this->authenticationManager = $authenticationManager;
        $this->logger = $logger;
    }

    /**
     * 
     *
     * @param EventDispatcherInterface $dispatcher An EventDispatcherInterface instance
     * @param integer                  $priority   The priority
     */
    public function register(EventDispatcherInterface $dispatcher)
    {
        $dispatcher->connect('core.security', array($this, 'handle'), 0);
    }
    
    /**
     * {@inheritDoc}
     */
    public function unregister(EventDispatcherInterface $dispatcher)
    {
    }
    
    /**
     * Handles X509 authentication.
     *
     * @param EventInterface $event An EventInterface instance
     */
    public function handle(EventInterface $event)
    {
        $request = $event->get('request');

        if (null !== $this->logger) {
            $this->logger->debug(sprintf('Checking secure context token: %s', $this->securityContext->getToken()));
        }

        list($user, $credentials) = $this->getPreAuthenticatedData($request);

        if (null !== $token = $this->securityContext->getToken()) {
            if ($token->isImmutable()) {
                return;
            }

            if ($token instanceof PreAuthenticatedToken && $token->isAuthenticated() && (string) $token === $user) {
                return;
            }
        }

        if (null !== $this->logger) {
            $this->logger->debug(sprintf('Trying to pre-authenticate user "%s"', $user));
        }

        try {
            $token = $this->authenticationManager->authenticate(new PreAuthenticatedToken($user, $credentials));

            if (null !== $this->logger) {
                $this->logger->debug(sprintf('Authentication success: %s', $token));
            }
            $this->securityContext->setToken($token);
        } catch (AuthenticationException $failed) {
            $this->securityContext->setToken(null);

            if (null !== $this->logger) {
                $this->logger->debug(sprintf("Cleared security context due to exception: %s", $failed->getMessage()));
            }
        }
    }

    /**
     * Gets the user and credentials from the Request.
     *
     * @param Request $request A Request instance
     *
     * @return array An array composed of the user and the credentials
     */
    abstract protected function getPreAuthenticatedData(Request $request);
}
