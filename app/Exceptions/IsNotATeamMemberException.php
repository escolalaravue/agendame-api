<?php

namespace App\Exceptions;

use App\Traits\RenderToJson;
use Exception;

class IsNotATeamMemberException extends Exception
{
    use RenderToJson;

    protected $message = 'This user is not a team member.';
    protected $code = 400;
}
