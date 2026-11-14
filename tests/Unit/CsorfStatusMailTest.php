<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Mail\CsorfStatusMail;

class CsorfStatusMailTest extends TestCase
{
    public function test_csorf_status_mail_subject_with_approved_and_customer_name()
    {
        $data = [
            'orf_id' => 'ORF/MSE/2026/0001',
            'status' => 'approved',
            'customer_name' => 'SIMPSON AND COMPANY LIMITED',
            'dispatchdate' => '2026-11-15',
            'csreject_remark' => null,
            'auth_user' => 'Admin User',
        ];

        $mailable = new CsorfStatusMail($data);
        $mailable->build();

        $this->assertEquals('ORF Status Update – Approved – SIMPSON AND COMPANY LIMITED', $mailable->subject);
    }

    public function test_csorf_status_mail_subject_with_rejected_and_customer_name()
    {
        $data = [
            'orf_id' => 'ORF/MSE/2026/0002',
            'status' => 'rejected',
            'customer_name' => 'TATA MOTORS',
            'dispatchdate' => null,
            'csreject_remark' => 'Technical specifications mismatch',
            'auth_user' => 'Admin User',
        ];

        $mailable = new CsorfStatusMail($data);
        $mailable->build();

        $this->assertEquals('ORF Status Update – Rejected – TATA MOTORS', $mailable->subject);
    }

    public function test_csorf_status_mail_subject_fallback_when_customer_name_is_empty()
    {
        $data = [
            'orf_id' => 'ORF/MSE/2026/0003',
            'status' => 'approved',
            'customer_name' => '',
            'dispatchdate' => '2026-12-01',
            'csreject_remark' => null,
            'auth_user' => 'Admin User',
        ];

        $mailable = new CsorfStatusMail($data);
        $mailable->build();

        $this->assertEquals('ORF Status Update – Approved', $mailable->subject);
    }

    public function test_csorf_status_mail_body_renders_customer_name()
    {
        $data = [
            'orf_id' => 'ORF/MSE/2026/0001',
            'status' => 'approved',
            'customer_name' => 'SIMPSON AND COMPANY LIMITED',
            'dispatchdate' => '2026-11-15',
            'csreject_remark' => null,
            'auth_user' => 'Admin User',
        ];

        $mailable = new CsorfStatusMail($data);
        $rendered = $mailable->render();

        $this->assertStringContainsString('SIMPSON AND COMPANY LIMITED', $rendered);
        $this->assertStringContainsString('ORF/MSE/2026/0001', $rendered);
        $this->assertStringContainsString('Approved', $rendered);
    }
}
