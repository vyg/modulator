<?php

namespace Voyage\Modulator;

use Psr\Container\NotFoundExceptionInterface;

use SilverStripe\Control\Controller;
use SilverStripe\Control\HTTPRequest;
use SilverStripe\Control\Session;
use SilverStripe\Core\Injector\Injector;

class Utilities {
        /**
     * Get the current section (first looking at controller, then at a request instance and lastly return a fresh session)
     *
     * @param HTTPRequest $request the incoming request (optional)
     * @return Session
     */
    public static function getSession(HTTPRequest $request = null)
    {
        if ($request && ($session = $request->getSession())) {
            return $session;
        }
        if (Controller::has_curr() && ($request = Controller::curr()->getRequest())) {
            return $request->getSession();
        }
        try {
            if ($session = Injector::inst()->get(HTTPRequest::class)->getSession()) {
                return $session;
            }
        } catch (NotFoundExceptionInterface $e) {
            // No-Op
        }
        return new Session([]);
    }
}
