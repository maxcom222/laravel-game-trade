<?php

namespace App\Backport\Extensions;

use Wiledia\Backport\Form\Field;
use App\Models\Page;

class PageLink extends Field
{
    protected $view = 'backend.extensions.pagelink';

    public function render()
    {
        $this->addVariables(['data' => $this->data, 'pages' => Page::all()]);


        $this->script = <<<EOT

$("#page_or_link_select").change(function(e) {
    $(".page_or_link_value input").attr('disabled', 'disabled');
    $(".page_or_link_value select").attr('disabled', 'disabled');
    $(".page_or_link_value").removeClass("d-none").addClass("d-none");


    switch($(this).val()) {
        case 'external_link':
            $("#page_or_link_external_link input").removeAttr('disabled');
            $("#page_or_link_external_link").removeClass('d-none');
            break;

        case 'internal_link':
            $("#page_or_link_internal_link input").removeAttr('disabled');
            $("#page_or_link_internal_link").removeClass('d-none');
            break;

        default: // page_link
            $("#page_or_link_page select").removeAttr('disabled');
            $("#page_or_link_page").removeClass('d-none');
    }
});

EOT;
        return parent::render();

    }
}
