<?php
/**
 * Created by PhpStorm.
 * User: lewis
 * Date: 04/07/2018
 * Time: 01:24
 */

namespace Colourspace\Framework\Profiles;

use Colourspace\Framework\Profile;

class Website extends Profile
{

    /**
     * @return null|void
     */

    public function create()
    {

        $this->objects = [
            'url_root' => COLOURSPACE_URL_ROOT,
            'password_strict' => ACCOUNT_PASSWORD_STRICT,
            'google_recaptcha' => GOOGLE_ENABLED
        ];
    }
}