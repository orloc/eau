<?php

namespace AppBundle\Datatables;

use Sg\DatatablesBundle\Datatable\View\AbstractDatatableView;
use Sg\DatatablesBundle\Datatable\View\Style;

/**
 * Class UserDatatable
 *
 * @package AppBundle\Datatables
 */
class UserDatatable extends AbstractDatatableView
{
    /**
     * {@inheritdoc}
     */
    public function buildDatatable()
    {
        $this->features->setFeatures(array(
            "auto_width" => true,
            "defer_render" => false,
            "info" => true,
            "jquery_ui" => false,
            "length_change" => true,
            "ordering" => true,
            "paging" => true,
            "processing" => true,
            "scroll_x" => false,
            "scroll_y" => "",
            "searching" => true,
            "server_side" => false,
            "state_save" => false,
            "delay" => 0
        ));

        $this->ajax->setOptions(array(
            "url" => $this->router->generate("user"),
            "type" => "GET"
        ));
        
        $this->options->setOptions(array(
            "display_start" => 0,
            "dom" => "lfrtip",
            "length_menu" => array(10, 25, 50, 100),
            "order_classes" => true,
            "order" => [[0, "asc"]],
            "order_multi" => true,
            "page_length" => 10,
            "paging_type" => Style::FULL_NUMBERS_PAGINATION,
            "renderer" => "",
            "scroll_collapse" => false,
            "search_delay" => 0,
            "state_duration" => 7200,
            "stripe_classes" => array(),
            "responsive" => false,
            "class" => Style::BOOTSTRAP_3_STYLE,
            "individual_filtering" => false,
            "individual_filtering_position" => "foot",
            "use_integration_options" => false
        ));

        $this->columnBuilder
            ->add("id", "column", [
                "title" => "Id",
                "width" => '20px',
                "orderable" => true,
                "searchable" => true
            ])->add('username', 'column', [
                "title" => "Username",
                "orderable" => true,
                "searchable" => true
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getEntity()
    {
        return "AppBundle\Entity\User";
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return "user_datatable";
    }
}
