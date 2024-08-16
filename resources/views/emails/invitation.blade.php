@extends('emails.layouts.default')

@section('content')
    <p>Você foi convidado para entrar para o nosso time.</p>
    <p>Por favor, clique no botão abaixo para aceitar o convite.</p>
    <table role="presentation" border="0" cellpadding="0" cellspacing="0" class="btn btn-primary">
        <tbody>
        <tr>
            <td align="left">
                <table role="presentation" border="0" cellpadding="0" cellspacing="0">
                    <tbody>
                    <tr>
                        <td> <a href="{{ config('app.portal_url') }}/aceitar-convite?token={{ $teamInvitation->token }}" target="_blank">Aceitar convite</a> </td>
                    </tr>
                    </tbody>
                </table>
            </td>
        </tr>
        </tbody>
    </table>
@endsection
