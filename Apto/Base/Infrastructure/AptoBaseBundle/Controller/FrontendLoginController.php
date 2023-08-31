<?php

namespace Apto\Base\Infrastructure\AptoBaseBundle\Controller;

use Apto\Base\Application\Backend\Query\FrontendUser\FrontendUserFinder;
use Apto\Base\Application\Core\Query\CustomerGroup\CustomerGroupFinder;
use Apto\Base\Infrastructure\AptoBaseBundle\Security\FrontendUser\FrontendUser;
use Apto\Catalog\Application\Core\Query\Shop\ShopFinder;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class FrontendLoginController extends AbstractController
{
    /**
     * @var FrontendUserFinder
     */
    private FrontendUserFinder $frontendUserFinder;

    /**
     * @var CustomerGroupFinder
     */
    private CustomerGroupFinder $customerGroupFinder;

    /**
     * @var ShopFinder
     */
    private ShopFinder $shopFinder;

    /**
     * @param FrontendUserFinder $frontendUserFinder
     * @param CustomerGroupFinder $customerGroupFinder
     * @param ShopFinder $shopFinder
     */
    public function __construct(
        FrontendUserFinder $frontendUserFinder,
        CustomerGroupFinder $customerGroupFinder,
        ShopFinder $shopFinder
    ) {
        $this->frontendUserFinder = $frontendUserFinder;
        $this->customerGroupFinder = $customerGroupFinder;
        $this->shopFinder = $shopFinder;
    }

    /**
     * @Route("/login", name="frontend_login")
     * @Route("/current-user", name="current-user")
     */
    public function loginAction(Request $request)
    {
        $user = $this->getUser();

        if($user instanceof FrontendUser) {
            $frontendUser = $this->getFrontendUser($request->getHost(), $user->getUserIdentifier());
            if (null !== $frontendUser) {
                return $this->json([
                    'username' => $user->getUserIdentifier(),
                    'user' => $frontendUser,
                    'roles' => $user->getRoles(),
                    'isLoggedIn' => true
                ]);
            }
        }

        return $this->json([
            'isLoggedIn' => false
        ]);
    }

    /**
     * @Route("/logout")
     * @param Request $request
     */
    public function logoutAction(Request $request)
    {
    }

    /**
     * @param string $host
     * @param string $userIdentifier
     * @return array|null
     */
    private function getFrontendUser(string $host, string $userIdentifier): ?array
    {
        $shop = $this->shopFinder->findByDomain($host);
        if (null === $shop) {
            return null;
        }

        $frontendUser = $this->frontendUserFinder->findByUsername($userIdentifier);
        if (null === $frontendUser) {
            return null;
        }

        $customerGroup = $this->customerGroupFinder->findByShopAndExternalId($shop['id'], $frontendUser['externalCustomerGroupId']);
        if (null === $customerGroup) {
            return null;
        }

        $frontendUser['customerGroup'] = $customerGroup;
        unset($frontendUser['externalCustomerGroupId']);

        return $frontendUser;
    }
}
