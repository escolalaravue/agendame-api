<?php

namespace App\Exceptions;

use App\Traits\RenderToJson;
use Exception;

class UserHasBeenInvitedException extends Exception
{
    use RenderToJson;

    protected $message = 'This user has been invited for this team.';
    protected $code = 400;
}
