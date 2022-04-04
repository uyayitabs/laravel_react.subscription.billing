<?php

namespace App\Services;

use Illuminate\Support\Str;
use Mail;
use Carbon\Carbon;

class EmailTemplateService
{
    protected $emailTemplate;
    protected $data;
    protected $tenant;

    /**
     * @return void
     */
    public function __construct($tenant, $data, $emailTemplateId = null)
    {
        $this->tenant = $tenant;
        $this->data = $data;
        $this->emailTemplate = $this->tenant->emailTemplates();
        if ($emailTemplateId) {
            $this->emailTemplate = $this->tenant->emailTemplates()->where('id', $emailTemplateId);
        }
    }

    public function setType($type)
    {
        $this->emailTemplate->where('type', $type);
    }

    public function setTemplate($emailTemplate)
    {
        return $this->emailTemplate = $emailTemplate;
    }

    public function getTemplate()
    {
        return $this->emailTemplate->first();
    }

    public function content()
    {
        return getStringBladeView($this->getTemplate()->body_html, $this->data);
    }

    public function subject()
    {
        return getStringBladeView($this->getTemplate()->subject, $this->data);
    }

    public function bcc()
    {
        return !empty($this->getTemplate()->bcc_email) ? explode(",", $this->getTemplate()->bcc_email) : [];
    }

    public function fromEmail()
    {
        return $this->getTemplate()->from_email;
    }

    public function fromName()
    {
        return $this->getTemplate()->from_name;
    }
}
