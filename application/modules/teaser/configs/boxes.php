<?php


return array(
    "slider-main" => array(
        "code" => "slider-main",
        "name" => "Main Slider",
        "template" => "slider-main.phtml",
        "icon"  => '/themes/frisco2014/images/teaser/previews/slider-main.jpg',
        "type" => "slider",
        "elements" => array("subtitle", "images", "link", "speed"),
        "params" => array(
            "images_dims" => array(1920, 400, 1366, 540, 1366, 540, 960, 350, 960, 350, 768, 210, 768, 210, 480, 180, 480, 180),
            'images_section'    => 'slider'
        )
    ),
    "intro-slider" => array(
        "code" => "intro-slider",
        "name" => "Intro Slider",
        "template" => "intro-slider.phtml",
        "icon"  => '/themes/frisco2014/images/teaser/previews/intro-slider.jpg',
        "type" => "slider",
        "elements" => array("subtitle", "images", "link", "speed"),
        "params" => array(
            'images'    => array(
                'img_1366_p'  => array(
                    "name" => "Image for viewport width 1366 and portrait orientation",
                    "media_query" => "(min-width: 1366px) and (orientation: portrait)",
                    "options" => array(
                        "minwidth" => 2700,
                        "maxwidth" => 2700,
                        "minheight" => 1080,
                        "maxheight" => 1080
                    )
                ),
                'img_768_l'  => array(
                    "name" => "Image for viewport width 768 and landscape orientation",
                    "media_query" => "(min-width: 768px) and (orientation: landscape)",
                    "options" => array(
                        "minwidth" => 2700,
                        "maxwidth" => 2700,
                        "minheight" => 1080,
                        "maxheight" => 1080
                    )
                ),
                'img_480_p'  => array(
                    "name" => "Image for viewport width 480 and portrait orientation",
                    "media_query" => "(min-width: 480px) and (orientation: portrait)",
                    "options" => array(
                        "minwidth" => 1600,
                        "maxwidth" => 1600,
                        "minheight" => 1024,
                        "maxheight" => 1024
                    )
                ),
                'img_480_l'  => array(
                    "name" => "Image for viewport width 480 and landscape orientation",
                    "media_query" => "(min-width: 480px) and (orientation: landscape)",
                    "options" => array(
                        "minwidth" => 870,
                        "maxwidth" => 870,
                        "minheight" => 320,
                        "maxheight" => 320
                    )
                ),
                'img_320_p'  => array(
                    "name" => "Image for viewport width 320 and portrait orientation",
                    "media_query" => "(min-width: 320px) and (orientation: portrait)",
                    "options" => array(
                        "minwidth" => 1600,
                        "maxwidth" => 1600,
                        "minheight" => 1024,
                        "maxheight" => 1024
                    )
                ),
                'img_320_l'  => array(
                    "name" => "Image for viewport width 320 and landscape orientation",
                    "media_query" => "(min-width: 320px) and (orientation: landscape)",
                    "options" => array(
                        "minwidth" => 850,
                        "maxwidth" => 850,
                        "minheight" => 320,
                        "maxheight" => 320
                    )
                )
            )
        )
    ),
    "teaser1" => array(
        "code" => "teaser1",
        "name" => "Teaser1",
        "template" => "teaser.phtml",
        "icon"  => '/themes/frisco2014/images/teaser/previews/teaser1.jpg',
        "type" => "teaser",
        "elements" => array(
            "subtitle",
            "images",
            "link",
            "text"
        ),
        "params" => array(
            "images_dims" => array(333, 333, 960, 576, 333, 333, 333, 333, 480, 287, 480, 287, 640, 370, 320, 185)
        )
    ),
    "teaser2" => array(
        "code" => "teaser2",
        "name" => "Teaser2",
        "template" => "teaser.phtml",
        "icon"  => '/themes/frisco2014/images/teaser/previews/teaser2.jpg',
        "type" => "teaser",
        "elements" => array(
            "subtitle",
            "images",
            "link",
            "text"
        ),
        "params" => array(
            "images_dims" => array(333, 333, 960, 576, 333, 333, 333, 333, 480, 287, 480, 287, 640, 370, 320, 185)
        )
    ),
    "teaser3" => array(
        "code" => "teaser3",
        "name" => "Teaser3",
        "template" => "teaser.phtml",
        "icon"  => '/themes/frisco2014/images/teaser/previews/teaser3.jpg',
        "type" => "teaser",
        "elements" => array(
            "subtitle",
            "images",
            "link",
            "text"
        ),
        "params" => array(
            "images_dims" => array(333, 333, 960, 576, 333, 333, 333, 333, 480, 287, 480, 287, 640, 370, 320, 185)
        )
    ),
    "teaser4" => array(
        "code" => "teaser4",
        "name" => "Teaser4",
        "template" => "teaser.phtml",
        "icon"  => '/themes/frisco2014/images/teaser/previews/teaser4.jpg',
        "type" => "teaser",
        "elements" => array(
            "subtitle",
            "images",
            "link",
            "text"
        ),
        "params" => array(
            "images_dims" => array(333, 333, 960, 576, 333, 333, 333, 333, 480, 287, 480, 287, 640, 370, 320, 185)
        )
    )
);