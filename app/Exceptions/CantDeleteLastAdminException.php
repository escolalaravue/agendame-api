<?php

namespace App\Exceptions;

use App\Traits\RenderToJson;
use Exception;

class CantDeleteLastAdminException extends Exception
{
    use RenderToJson;

    protected $message = 'You cant delete the last admin of a team.';
    protected $code = 400;
}
