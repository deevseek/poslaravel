<?php

namespace App\Mail;

use App\Tenancy\Models\TenantRegistration;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TenantRegistrationApproved extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public TenantRegistration $registration)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Pendaftaran Tenant Anda Telah Diaktifkan',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.tenant-registration-approved',
        );
    }
}
