<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PdfTemplate extends BaseModel
{
    protected $fillable = [
        "tenant_id",
        "header_html",
        "main_html",
        "footer_html",
        "type",
        "version",
        "notes",
    ];

    public static $fields = [
        "tenant_id",
        "header_html",
        "main_html",
        "footer_html",
        "type",
        "version",
        "notes",
    ];

    protected $appends = [];

    protected $casts = [];

    public static $includes = [];

    public static $scopes = [];

    public static $sorts = [];

    public static $withScopes = [];

    /**
     * Get binding Tenant
     *
     * @return \Tenant
     */

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }



    // public function getHeaderHtmlAttribute($value)
    // {
    //     return htmlentities($value);
    // }

    // public function getMainHtmlAttribute($value)
    // {
    //     return htmlentities($value);
    // }

    // public function getFooterHtmlAttribute($value)
    // {
    //     return htmlentities($value);
    // }
}
