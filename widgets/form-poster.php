<?php
namespace YXSuperCat\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
* Elementor Form Poster
*
* Elementor widget for Form Poster.
*/
class Yinxiang_Form_Poster extends Widget_Base {

    /**
    * Retrieve the widget name.
    *
    * @access public
    *
    * @return string Widget name.
    */
    public function get_name() {
        return 'yx-form-poster';
    }

    /**
    * Retrieve the widget title.
    *
    * @access public
    *
    * @return string Widget title.
    */
    public function get_title() {
        return __( 'YX Form Poster', 'yx-super-cat' );
    }

    /**
    * Retrieve the widget icon.
    *
    * @access public
    *
    * @return string Widget icon.
    */
    public function get_icon() {
        return 'eicon-code';
    }

    /**
    * Retrieve the list of categories the widget belongs to.
    *
    * Used to determine where to display the widget in the editor.
    *
    * Note that currently Elementor supports only one category.
    * When multiple categories passed, Elementor uses the first one.
    *
    * @access public
    *
    * @return array Widget categories.
    */
    public function get_categories() {
        return [ 'yx-super-cat' ];
    }

    /**
    * Retrieve the list of scripts the widget depended on.
    *
    * Used to set scripts dependencies required to run the widget.
    *
    * @access public
    *
    * @return array Widget scripts dependencies.
    */
    public function get_script_depends() {
        return [ 'yx-super-cat' ];
    }

    /**
    * Register the widget controls.
    *
    * Adds different input fields to allow the user to change and customize the widget settings.
    *
    * @access protected
    */
    protected function _register_controls() {
        $this->start_controls_section(
            'section_content',
            [
                'label' => __( 'Content', 'yx-super-cat' ),
            ]
        );

        $this->add_control(
            'formid',
            [
                'label' => __( 'CSS ID of the form widget', 'yx-super-cat' ),
                'type' => Controls_Manager::TEXT,
            ]
        );

        $this->add_control(
            'url',
            [
                'label' => __( 'Action URL', 'yx-super-cat' ),
                'type' => Controls_Manager::TEXT,
            ]
        );

        $this->add_control(
            'form_method',
            [
               'label' => __( 'Form method', 'yx-super-cat' ),
               'type' => Controls_Manager::CHOOSE,
                'options' => [
                    'GET' => [
                        'title' => __( 'GET', 'yx-super-cat' ),
                    ],
                    'POST' => [
                        'title' => __( 'POST', 'yx-super-cat' ),
                    ],
                ],
                'default' => 'POST',
                'toggle' => true,
            ]
        );

        $this->add_control(
            'action_server',
            [
               'label' => __( 'Action Server Name', 'yx-super-cat' ),
               'type' => Controls_Manager::CHOOSE,
                'options' => [
                    'unchange' => [
                        'title' => __( 'unchange', 'yx-super-cat' ),
                    ],
                    'wwwsite' => [
                        'title' => __( 'wwwsite', 'yx-super-cat' ),
                    ],
                    'appsite' => [
                        'title' => __( 'appsite', 'yx-super-cat' ),
                    ],
                    'staticsite' => [
                        'title' => __( 'staticsite', 'yx-super-cat' ),
                    ],
                ],
                'default' => 'wwwsite',
                'toggle' => true,
            ]
        );

        $this->add_control(
            'use_hpts',
            [
               'label' => __( 'Add hpts/hptsh hidden fields', 'yx-super-cat' ),
               'type' => Controls_Manager::CHOOSE,
                'options' => [
                    'no' => [
                        'title' => __( 'no', 'yx-super-cat' ),
                    ],
                    'yes' => [
                        'title' => __( 'yes', 'yx-super-cat' ),
                    ],
                ],
                'default' => 'no',
                'toggle' => true,
            ]
        );

        $this->add_control(
            'replace_underscores',
            [
                'label' => __( 'Replace _# with [#] in input names.<br><br>E.g.: <b>field_1_0</b> becomes <b>field[1][0]</b>', 'yx-super-cat' ),
                'type' => \Elementor\Controls_Manager::CHOOSE,
                'options' => [
                    'yes' => [
                        'title' => __( 'Yes', 'yx-super-cat' ),
                        'icon' => 'fa fa-check',
                    ],
                    'no' => [
                        'title' => __( 'No', 'yx-super-cat' ),
                        'icon' => 'fa fa-times',
                    ]
                ],
                'default' => 'yes'
            ]
        );

        $this->end_controls_section();


    }


    // get milliseconds of current time, plus optional offset seconds, as a string
    private function millitime($offset_secs=0) {
        $microtime = explode(' ', microtime());
        $millis = intval($microtime[0] * 1000);
        if ($offset_secs == 0) {
            return sprintf('%d%03d', $microtime[1], $millis);
        }

        // PHP ints are either 32-bit or 64-bit, depending on system. To avoid
        // overflow and maintain exactness, calculating secs and millis separately.
        $secs = $microtime[1] + $offset_secs;
        $forceSign = ''; // for adding a negative if needed
        if ($secs < 0 && $millis > 0) {
            $millis = 1000 - $millis;
            $secs = $secs + 1;
            $forceSign = $secs < 0 ? '' : '-';
        }
        return sprintf('%s%d%03d', $forceSign, $secs, $millis);
    }

    private function hashTimestamp($millis) {
        return base64_encode(hash_hmac("sha1", $millis, "68caXl0qWK", true));
    }

    /**
    * Render the widget output on the frontend.
    *
    * Written in PHP and used to generate the final HTML.
    *
    * @access protected
    */
    protected function render() {
        $settings = $this->get_settings_for_display();
	if ($settings['use_hpts']=="yes"){
            header("Cache-Control: no-cache, no-store, must-revalidate"); //HTTP 1.1
            header("Pragma: no-cache"); //HTTP 1.0
            header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past
        }
        // hash of current time in milliseconds for bots
        $hpts = $this->millitime();
        $hptsh = $this->hashTimestamp($hpts);

        ?>

        <script type='text/javascript'>

        var appHost = 'app.yinxiang.com';
        var curHost = window.location.hostname;
        if (curHost.match(/stage.*-www.yinxiang.com/) || curHost.match(/sandbox.*-www.yinxiang.com/)) {
            appHost = curHost.replace("-www","");
	} else if (curHost === 'staging.yinxiang.com') {
	    curHost = 'www.yinxiang.com';
        }

        document.addEventListener("DOMContentLoaded", function(event) {
            /* The ID assigned to the form widget via Elementor */
            var formID = "#<?php echo $settings['formid']; ?>";
            /* The URL to which you want to send the data */
            var actionURL = "<?php echo $settings['url']; ?>";
            var superGattoID = "#form-yx-super-gatto-for-<?php echo $settings['formid']; ?>";
            var formMethod = "<?php echo $settings['form_method']; ?>";
            var actionServer = "<?php echo $settings['action_server']; ?>";

            var $jq = jQuery.noConflict();
            $jq(superGattoID).attr("class", $jq(formID).attr("class"));
            $jq(superGattoID).html($jq(formID).html());
            $jq(formID).hide();
            $jq(superGattoID + " form").attr("method", formMethod);

	    if (actionServer === "wwwsite") {
               $jq(superGattoID + " form").attr("action", "https://" + curHost + actionURL);
	    } else if (actionServer === "appsite") {
               $jq(superGattoID + " form").attr("action", "https://" + appHost + actionURL);
	    } else if (actionServer === "staticsite") {
               $jq(superGattoID + " form").attr("action", "https://static." + appHost + actionURL);
	    } else if (actionServer === "unchange") {
               $jq(superGattoID + " form").attr("action", actionURL);
	    } else {
               $jq(superGattoID + " form").attr("action", actionURL);
            }

            $jq(superGattoID + " form").find('input, textarea, select').each(function(){
                var matches = $jq(this).attr("name").match(/form_fields\[(.*?)\]/);
                if (matches) {
                    var submatch = matches[1];
                    <?php if($settings['replace_underscores'] == "yes"){ ?> submatch = submatch.replace(/\_[0-9]+/g, function(x){return "["+x.replace("_", "")+"]";}); <?php } ?>
                    $jq(this).attr("name", submatch);
                }else{
                    $jq(this).remove();
                }
                var cls=$jq(this).attr("class");
                if ( cls && cls.match(/flatpickr/) ){
                    $jq(this).flatpickr();
                }
            });
            // $jq(superGattoID + " form").submit(function(){
            //     $jq(this).find('input, textarea, select').each(function(){
            //         /* SET THE COOKIE */
            //         var name = "supercat_form_" + $jq(this).attr("name");
            //         var value = $jq(this).val();
            //         var expires = "";
            //         document.cookie = encodeURIComponent(name) + "=" + encodeURIComponent(value) + expires + "; path=/";
            //     });
            // });

            <?php if($settings['use_hpts'] == "yes"){ ?>
            $jq(superGattoID + " form").find('input').each(function(){
                if ($jq(this).attr("name")==='hpts') {
                    $jq(this).attr("value", "<?php echo $hpts; ?>");
                }
                if ($jq(this).attr("name")==='hptsh') {
                    $jq(this).attr("value", "<?php echo $hptsh; ?>");
                }
            });
	    <?php } ?>
            <?php if($settings['formid'] == "form_new_biz_upgrade"){ ?>
	    $jq(document).on('keyup input', superGattoID + " form div div input", function(e) {
              var contactNameVal = encodeURIComponent($jq(superGattoID + " form div .elementor-field-group-contactName input").val());
              var businessNameVal = encodeURIComponent($jq(superGattoID + " form div .elementor-field-group-businessName input").val());
              $jq(superGattoID + " form div .elementor-field-group-targetUrl input").val('https://' + appHost + '/business/ExistingUserAddBusiness.action?createBusinessAccount=&flowExperiment=cross'
	        + '&contactName=' + contactNameVal + "&businessName=" + businessNameVal);
            });
	    <?php } ?>

            <?php if($settings['formid'] == "redeemcode"){ ?>
            $jq(document).on('keyup input', superGattoID + " form div div input", function(e) {
              var foo = $jq(this).val().split('-').join('');
              // remove hyphens
              if (foo.length > 0) {
                foo = foo.match(new RegExp('.{1,5}', 'g')).join('-');
              }
              $jq(this).val(foo.toUpperCase().substr(0,23));
            });
	    <?php } ?>

            <?php if($settings['formid'] == "reg_form"){ ?>

            (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
            (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
            m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
            })(window,document,'script','//www.google-analytics.com/analytics.js','ga');
            ga('create', 'UA-117332876-40', 'auto');

            $jq("#reg_form > div > div > button").prop("type", "button");
            $jq("#reg_form > div > div > button").css("background-color","#00b548");
            $jq("#reg_form > div > div > button").css("padding-left","188");
            $jq("#reg_form > div > div > button").css("padding-right","188");
            var $reg_timeout = 1000;
            $jq(document).on('click', "#reg_form > div > div > button", function(e) {
              if (navigator.userAgent.match(/Macintosh/i) !== null) { //mac
                $reg_timeout = 5000;
                window.location = 'https://www.yinxiang.com/download/get.php?file=EvernoteMac';
                ga('send','event','account_signup','auto_download', 'mac');
                Countly.q.push(['add_event',{
                  "key": "account_signup",
                  "count": 1,
                  "segmentation": {
                     "action": "auto_download",
                     "label": "mac"
                  }
                }]);
              } else if (navigator.userAgent.match(/Windows/i) !== null) { //windows
                $reg_timeout = 10000;
                window.location = 'https://www.yinxiang.com/download/get.php?file=Win';
                ga('send','event','account_signup','auto_download', 'windows');
                Countly.q.push(['add_event',{
                  "key": "account_signup",
                  "count": 1,
                  "segmentation": {
                     "action": "auto_download",
                     "label": "windows"
                  }
                }]);
              }
              setTimeout(() => {
                $jq(superGattoID + " form").submit();
              }, $reg_timeout);
            });
            <?php } ?>
        });
        </script>
        <div id="form-yx-super-gatto-for-<?php echo $settings['formid']; ?>"></div>
        <?php

    }

}
