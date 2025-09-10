<?php

declare(strict_types=1);

namespace App\Mail;

use App\Domain\User\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AdminInviteMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public User $user) {}

    public function build(): self
    {
        $inviteUrl = config('app.url').'/accept-invite?token='.$this->user->invite_token.'&email='.urlencode($this->user->email);

        return $this->subject('You are invited to Farmer App')
            ->view('emails.admin_invite')
            ->with(['user' => $this->user, 'inviteUrl' => $inviteUrl]);
    }
}
