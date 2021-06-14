<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Http\Authenticator\AuthenticatorInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\CustomCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\PassportInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\UserPassportInterface;
use Trikoder\Bundle\OAuth2Bundle\Security\Guard\Authenticator\OAuth2Authenticator as TrikoderOAuth2Authenticator;

class OAuth2Authenticator implements AuthenticatorInterface
{
    private TrikoderOAuth2Authenticator $decorated;
    private UserProviderInterface $userProvider;

    public function __construct(TrikoderOAuth2Authenticator $decorated, UserProviderInterface $userProvider)
    {
        $this->decorated = $decorated;
        $this->userProvider = $userProvider;
    }

    public function supports(Request $request): ?bool
    {
        return $this->decorated->supports($request);
    }

    public function authenticate(Request $request): PassportInterface
    {

        return new Passport(
            new UserBadge(
                $this->decorated->getCredentials($request),
                fn ($id) => $this->decorated->getUser($id, $this->userProvider)
            ),
            new CustomCredentials(
                fn ($credentials, $user) => $this->decorated->checkCredentials($credentials, $user),
                $this->decorated->getCredentials($request)
            )
        );
    }

    public function createAuthenticatedToken(PassportInterface $passport, string $firewallName): TokenInterface
    {
        if (!$passport instanceof UserPassportInterface) {
            throw new \RuntimeException('Must be supplied with a UserPassport');
        }
        return $this->decorated->createAuthenticatedToken($passport->getUser(), $firewallName);
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        return $this->decorated->onAuthenticationSuccess($request, $token, $firewallName);
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        return $this->decorated->onAuthenticationFailure($request, $exception);
    }
}