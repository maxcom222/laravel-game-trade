<?php

namespace App\Charts;

use ConsoleTVs\Charts\Classes\Chartjs\Chart;

class General extends Chart
{
    /**
     * Initializes the chart.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Set the chart options.
     *
     * @param array|Collection $options
     * @param bool             $overwrite
     *
     * @return self
     */
    public function options($options, bool $overwrite = false)
    {

        $json_options = '{"responsive":true,"maintainAspectRatio":false,"title":{"display":false,"text":""},"tooltips":{"enabled":true,"intersect":false,"mode":"nearest","bodySpacing":5,"yPadding":10,"xPadding":10,"caretPadding":0,"displayColors":false,"backgroundColor":"#586c82","titleFontColor":"#ffffff","cornerRadius":6,"footerSpacing":0,"titleSpacing":0},"legend":{"display":false,"labels":{"usePointStyle":false}},"hover":{"mode":"index"},"scales":{"xAxes":[{"display":false,"scaleLabel":{"display":false,"labelString":"Month"},"ticks":{"display":false,"beginAtZero":true}}],"yAxes":[{"display":true,"stacked":false,"scaleLabel":{"display":false,"labelString":"Value"},"gridLines":{"color":"#eef2f9","drawBorder":false,"offsetGridLines":true,"drawTicks":false},"ticks":{"display":false,"beginAtZero":true}}]},"elements":{"point":{"radius":0,"borderWidth":0,"hoverRadius":0,"hoverBorderWidth":0}},"layout":{"padding":{"left":0,"right":0,"top":0,"bottom":0}}}';

        $options = [
                        'legend' => [
                            'display' => false
                        ],
                        'layout' => [
                            'padding' => [
                                'left' => 0,
                                'right' => 0,
                                'top' => 0,
                                'bottom' => 0
                            ]
                        ]
                    ];

        $this->options = json_decode($json_options);


        return $this;
    }
}
