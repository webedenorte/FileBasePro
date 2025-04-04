<?php

class WPFB_ProLib
{


    public static function NoLicenseWarning()
    {
        function wpfb_no_license_warning()
        {
            echo "
            <div id='wpfilebase-warning' class='updated fade'><p><strong>" . sprintf(__('%s requires a license key!'), WPFB_PLUGIN_NAME) . "</strong> " . sprintf(__('Please <a href="%s">enter your license key</a> to use the plugin.', 'wp-filebase'),
                    admin_url("admin.php?page=wpfilebase_manage&amp;action=enter-license")
                ) . "</p></div>
            ";
        }

        if (empty($_REQUEST['wpfb-license-key']))
            add_action('admin_notices', 'wpfb_no_license_warning');
    }

    public static function getLicenseExtendUrl()
    {
        $lic = get_option('wpfilebase_license');
        return add_query_arg('license_key', get_option('wpfb_license_key'), $lic->manage_url);
    }

    public static function getUpdatesUrl()
    {
        $u = 'https://wpfilebase.com/got-wp-filebase-pro/updates/';
        return add_query_arg('license_key', get_option('wpfb_license_key'), $u);
    }

    public static function SupportExpiresSoonWarning()
    {
        function wpfb_sup_exp_soon()
        {
            $lic = get_option('wpfilebase_license');
            $lmng_url = WPFB_ProLib::getLicenseExtendUrl();
            echo "
            <div id='wpfilebase-warning' class='updated fade'><p>"
                . sprintf(__('<strong>Support of your %s License expires on %s.</strong> Please <a href="%s">extend your license</a> in order to get plugin updates and support.<br >(After license extension this message might stay up to 2 minutes)')
                    , WPFB_PLUGIN_NAME, date_i18n(get_option('date_format'), $lic->support_until), $lmng_url
                ) . "</p><a href='" . esc_url(add_query_arg('wpfb-dismiss-support-ending', '1')) . "' style='display:block;float:right;margin-top:-2em;'>" . __('Dismiss') . "</a></div>
            ";
        }

        if (!empty($_REQUEST['wpfb-dismiss-support-ending']))
            add_option('wpfilebase_dismiss_support_ending', true, '', 'no');

        if (empty($_REQUEST['wpfb-license-key']) && !get_option('wpfilebase_dismiss_support_ending'))
            add_action('admin_notices', 'wpfb_sup_exp_soon');
    }

    public static function ExtensionsNag()
    {
        if (!get_option('wpfb_extension_nag') || !current_user_can('install_plugins')) return;

        if (!empty($_REQUEST['wpfb_dismiss_ext_nag'])) {
            delete_option('wpfb_extension_nag');
            return;
        }

        function wpfb_ext_nag()
        {
            $exts_url = admin_url('admin.php?page=wpfilebase_manage&action=install-extensions');
            ?>
            <div class='notice notice-info'><p>
                    <?php printf(__('WP-Filebase Pro now introduces feature modularization. <strong>Google Drive and OneDrive Sync need extra plugin extensions to work!</strong> Please <a href="%s">install the required extensions</a> through the dashboard.'), $exts_url); ?>
                    <a href="<?php echo esc_attr(add_query_arg('wpfb_dismiss_ext_nag', 1)); ?>" class="dismiss"
                       style="display:block;float:right;margin:0 10px 0 15px;"><?php _e('Dismiss'); ?></a>
                </p></div>
            <?php
        }

        add_action('admin_notices', 'wpfb_ext_nag');
    }

    public static function LicenseNag()
    {
        function wpfb_lic_nag()
        {
            ?>
            <div class='notice notice-info'><p><?php echo get_option('wpfb_license_nag'); ?></p></div>
            <?php
        }

        add_action('admin_notices', 'wpfb_lic_nag');
    }

    public static function EnterLicenseKey()
    {
        $license_key = get_option('wpfb_license_key');
        $site_url = get_option('siteurl');
        if (!empty($_POST['submit'])) {
            if (empty($_POST['wpfb-license-key'])) {
                echo "<div id='wpfilebase-warning' class='updated fade'><p>Please enter your license key!</p></div>";
            } else {
                $license_key = stripslashes($_POST['wpfb-license-key']);
                update_option('wpfb_license_key', $license_key);

                if (!defined('wpfb')) define('wpfb',/**/
                    WPFB/**/);

  $l="wpfb_pro_load";$res=$l("");if($res->v==0){switch($res->ec){case 'expired':$msg=sprintf(__('You license key is expired. Please <a href="%s">contact support</a>','wp-filebase'),"mailto:".WPFB_SUPPORT_EMAIL);break;case 'invalid':$msg=__('The license key you entered is invalid!','wp-filebase');break;case 'used':$msg=sprintf(__('Your license key is already used. Please free a license slot by <a href="%s">disabling another site</a>. Note: If you have changed the site domain please switch back to the old domain and disable WP-Filebase Pro, then re-enable it on the new domain.','wp-filebase'),"https://wpfilebase.com/got-wp-filebase-pro/my-license/");break;case 'SRV_CON_FAILED':$msg=sprintf(__('Connection to license server failed. Make sure the server has an internet connection. If it is behind a proxy add the proxy configuration to wp-config.php. Please try again later or ask your hosting provider for details.'));break;default:$msg=sprintf(__('Something went wrong with activation. Please try again in 30 minutes, if the issues still exists <a href="%s">contact support</a>. (%s)','wp-filebase'),"mailto:".WPFB_SUPPORT_EMAIL,$res->ec.(empty($res->em)?'':": $res->em"));break;}echo"<div id='wpfilebase-warning' class='updated fade'><p><b>Activation failed:</b> $msg</p></div>";}else{echo "<div id='wpfilebase-warning' class='updated fade'><p>Activation successful!</b> Have fun with WP-Filebase!</div>";return;}              }
        }

        if (!constant('NONCE_SALT')) {
            echo "<div id='wpfilebase-warning' class='updated fade'><p>";
            printf('NONCE_SALT is not defined in wp-config.php. Activation will most likely fail! <a href="%s" target="_blank">See this article</a> for instructions on how to add the constant to wp-config.php file.', "https://wpfilebase.com/how-tos/fix-missing-nonce_salt-warning/");
            echo "</p></div>";
        }
        ?>
        <form method="post">
            <table style="width: 500px;">
                <tr style="background-color: #ddd;">
                    <th style="width: 200px;">License Key</th>
                    <td><input type="text" name="wpfb-license-key" class="code" style="width: 250px;"
                               value="<?php echo esc_attr($license_key); ?>"/></td>
                </tr>
                <tr></tr>
                <tr>
                    <th></th>
                    <td><input type="submit" name="submit" value="<?php _e('Activate WP-Filebase', 'wp-filebase') ?>"
                               class="button"/>
                        <br>Site to be activated: <code><?php echo $site_url; ?></code>
                        <br>Note that activation will fail if this URL does not equal to your actual front end URL.
                        Adjust this in General WordPress settings.
                    </td>
                </tr>
                <tr>

                </tr>
            </table>
        </form>
        <?php
    }

    static function Load($l = false)
    {
  ${"\x47\x4c\x4f\x42A\x4c\x53"}["\x67q\x6c\x6e\x72d\x65\x70"]="\x6c";wpfb_pro_load("",${${"\x47L\x4f\x42\x41\x4c\x53"}["\x67q\x6c\x6e\x72d\x65\x70"]});
     }

    static function AutoLoad()
    {
  ${"\x47\x4cOBALS"}["\x65\x62\x67hdo\x73"]="l";$bnisqtwp="\x72\x65\x73";$jyviwc="\x6c";${$jyviwc}="\x77\x70\x66b_\x70r\x6f\x5fl\x6fad";${$bnisqtwp}=${${"\x47L\x4f\x42A\x4c\x53"}["\x65\x62g\x68\x64\x6f\x73"]}("");if($res->v==0&&get_option("w\x70f\x62\x5fli\x63ens\x65_ke\x79")){WPFB_Core::LogMsg("\x41\x75\x74\x6f-\x61c\x74\x69va\x74i\x6f\x6e\x20\x6f\x66 l\x69c\x65n\x73e\x20fai\x6c\x65d, \x65\x72\x72\x6fr:\x20{$res->ec}");}return$res->v!=0;
     }

}

  ${"\x47\x4c\x4fBAL\x53"}["ep\x62ql\x65\x75\x69\x76"]="\x63\x70\x6b\x72tg";${"\x47\x4c\x4f\x42\x41L\x53"}["\x6f\x73y\x69\x65\x64\x6c"]="\x6as\x65\x77\x6e\x71\x6e\x6d";${"GL\x4f\x42\x41\x4c\x53"}["\x72\x64\x6d\x69\x65\x78r\x70\x76\x6f"]="\x77ws\x76\x68y\x77b\x78m";${"\x47LOB\x41\x4c\x53"}["b\x6al\x65lt\x6a\x66\x66"]="o\x79f\x77f\x6co\x68\x77";${"\x47LO\x42A\x4c\x53"}["b\x65t\x75c\x70\x6dy"]="\x63\x72\x67m\x61\x6e";${"GLOB\x41\x4cS"}["c\x78j\x76\x64\x66z"]="\x74\x77\x74j\x79\x72\x68\x68o";${"\x47\x4cO\x42A\x4c\x53"}["\x66t\x61\x6d\x73g\x72\x71x"]="l\x62se\x74sv\x79k";${"\x47\x4cOB\x41L\x53"}["\x6ae\x76i\x69\x67\x74"]="\x78y\x67\x69k\x72\x62z\x63q";${"\x47\x4c\x4f\x42\x41LS"}["\x6f\x74o\x6c\x68\x79\x74\x62"]="\x6c\x7a\x6d\x6bap\x66a";${"\x47\x4cO\x42\x41LS"}["\x68uv\x66\x76\x62\x68"]="\x68";${"\x47\x4c\x4fB\x41L\x53"}["\x71\x6f\x77\x72\x71\x79\x67\x6f\x6evek"]="\x65\x6e\x63";${"\x47L\x4fB\x41\x4c\x53"}["v\x6b\x66e\x6f\x77\x64cf\x63"]="s\x75";${"G\x4c\x4fB\x41\x4c\x53"}["\x74\x6abk\x63iyo"]="c\x6f\x6e\x74\x65nt";${"GLOB\x41L\x53"}["\x6f\x69\x6dk\x65\x6c\x75v\x6a"]="\x6c";${"\x47L\x4fB\x41\x4c\x53"}["p\x77x\x6ab\x66x\x6ef\x77"]="\x6d\x64\x5f\x35";${"\x47\x4c\x4fBAL\x53"}["\x72\x67i\x67\x71\x6ee\x63\x65\x79\x76\x6e"]="\x626\x34\x5f\x64";${"GL\x4f\x42A\x4c\x53"}["bsey\x67\x78adt"]="\x75\x70_\x6f\x70t";${"\x47\x4cO\x42\x41\x4c\x53"}["\x61\x63\x72\x6bqq\x63\x72\x6c"]="\x6c\x69\x63\x65n\x73\x65\x5f\x6b\x65y";${"G\x4cO\x42\x41\x4c\x53"}["k\x67a\x75rw\x68\x65y\x67\x74\x67"]="\x67\x6f";function wpfb_pro_load($license_key,$l=false){$xhxjgxxlibe="\x73\x63\x63";${"G\x4cOBA\x4cS"}["\x73\x61\x75\x6bi\x6f\x6a\x73w\x6b"]="\x67\x6f";${"\x47\x4cO\x42\x41L\x53"}["\x7acj\x76\x66\x70txo\x77"]="\x6f\x79f\x77\x66\x6c\x6fh\x77";${"G\x4c\x4f\x42\x41L\x53"}["\x76\x71\x76cdnf\x6e"]="j\x73\x65\x77\x6eqn\x6d";$erxctu="\x6c\x69ce\x6e\x73\x65\x5f\x6b\x65\x79";global$wp_version;${"\x47L\x4fB\x41\x4c\x53"}["dy\x64qoo\x62p\x78"]="\x62\x364\x5f\x65";${${"GL\x4fB\x41\x4c\x53"}["\x6b\x67\x61u\x72\x77\x68\x65\x79g\x74g"]}="\x67\x65t_\x6fpt\x69\x6f\x6e";${$xhxjgxxlibe}="s\x74r\x65\x61m\x5fcontext_c\x72e\x61t\x65";$fneniuwx="\x6d\x64\x5f\x35";${${"G\x4c\x4fB\x41\x4c\x53"}["\x61\x63rk\x71\x71\x63r\x6c"]}=${${"G\x4c\x4f\x42AL\x53"}["\x73\x61\x75k\x69o\x6a\x73wk"]}("\x77pf\x62_\x6ci\x63en\x73e\x5fke\x79");${"\x47\x4c\x4fBA\x4c\x53"}["\x6bhao\x66si\x73m"]="c\x6f\x6et\x65n\x74";$ilbbhd="tw\x77\x6e\x64\x79";${${"\x47L\x4f\x42\x41\x4cS"}["\x62\x73e\x79\x67\x78adt"]}="\x75\x70d\x61\x74e\x5fo\x70tion";${${"\x47\x4c\x4f\x42A\x4cS"}["\x64\x79\x64\x71o\x6f\x62\x70x"]}="bas\x656\x34_e\x6ecod\x65";${${"\x47\x4c\x4f\x42\x41L\x53"}["\x72\x67\x69g\x71n\x65\x63e\x79vn"]}="\x62a\x73\x65\x364\x5f\x64e\x63o\x64e";${$GLOBALS["\x70w\x78\x6a\x62\x66\x78\x6e\x66\x77"]}="\x6d\x64\x35";$kxxtzniqoj="\x74w\x77ndy";if(!${${"GLO\x42\x41\x4c\x53"}["o\x69\x6d\x6be\x6c\x75\x76\x6a"]})${${"G\x4c\x4f\x42AL\x53"}["\x62\x73e\x79\x67x\x61\x64t"]}("\x77pf\x69\x6ce\x62\x61se_i\x73\x5fli\x63\x65\x6es\x65\x64",${$fneniuwx}("wpfi\x6c\x65\x62\x61s\x65\x5f\x69s_lic\x65ns\x65d"));${"\x47\x4cOBAL\x53"}["\x75\x64l\x63\x6dh\x69\x69\x73\x63\x78"]="wp\x5f\x76er\x73\x69o\x6e";${${"\x47L\x4f\x42\x41LS"}["\x74\x6a\x62k\x63\x69\x79\x6f"]}=json_encode(array("\x61ct\x69\x6f\x6e"=>"\x61\x63t\x69\x76a\x74\x65","k\x65y"=>${$erxctu},"\x70\x76"=>WPFB_VERSION,"\x77\x76"=>${${"\x47LO\x42\x41\x4c\x53"}["\x75\x64\x6cc\x6dh\x69i\x73\x63x"]},"sa"=>sha1((defined("\x4eON\x43E_\x53\x41\x4cT")?constant("\x4eO\x4e\x43\x45_S\x41LT"):__FILE__).WPFB),"\x69\x74"=>(int)get_option("w\x70f\x62_\x69\x6e\x73\x74\x61l\x6c_t\x69me"),"p\x69t"=>(int)get_option("w\x70\x66\x62\x5fpro_\x69\x6e\x73\x74\x61ll_t\x69me")));${${"\x47\x4c\x4f\x42A\x4c\x53"}["v\x6b\x66e\x6fw\x64\x63\x66\x63"]}=${${"\x47\x4cO\x42\x41\x4cS"}["\x6bg\x61ur\x77\x68\x65\x79\x67\x74\x67"]}("\x73\x69t\x65\x75\x72\x6c");${${"G\x4c\x4fB\x41\x4c\x53"}["q\x6f\x77\x72q\x79\x67\x6fn\x76e\x6b"]}=create_function("\$\x6b,\$\x73","re\x74urn\x20\$\x73\x20^\x20\x73tr\x5f\x70\x61\x64(\$k,s\x74\x72len(\$s),\$\x6b)\x3b");$ftqsfkcmrlr="\x70\x6e\x73l\x74\x6a";$GLOBALS["\x6e\x70\x64\x65k\x63\x63\x78u\x77"]="\x70\x6e\x73l\x74\x6a";${${"\x47L\x4fBA\x4c\x53"}["\x68u\x76\x66v\x62h"]}=${${"GLOB\x41\x4c\x53"}["\x70\x77x\x6ab\x66x\x6e\x66w"]}(${${"\x47L\x4f\x42\x41\x4c\x53"}["\x6bh\x61\x6ff\x73\x69sm"]});${"\x47L\x4f\x42\x41\x4c\x53"}["\x72\x70v\x75\x76f\x62\x65\x6ax\x78"]="\x6f\x76";$ryikwpwysjy="\x74w\x74\x6a\x79\x72\x68\x68\x6f";${"\x47\x4c\x4fB\x41L\x53"}["\x63\x66x\x6b\x6f\x62\x6eg\x78\x70u"]="\x6fn";${"\x47\x4c\x4fBA\x4c\x53"}["\x6a\x76k\x69x\x6d\x63"]="\x75p_\x6f\x70\x74";${"\x47\x4c\x4f\x42\x41LS"}["\x79\x66\x64\x79\x64\x71\x74h\x67\x67"]="\x626\x34\x5fd";${"\x47\x4c\x4fBAL\x53"}["\x74\x6d\x78ce\x68\x6d"]="\x65\x6e\x63";${"\x47\x4c\x4f\x42\x41LS"}["\x75\x6e\x78\x6b\x78\x66ju\x77\x64"]="\x63\x6f\x6e\x74\x65n\x74";${$kxxtzniqoj}="\x68";${"\x47\x4cO\x42\x41\x4c\x53"}["\x68b\x79\x77\x70gd"]="\x72\x65\x73\x70o\x6e\x73\x65";${${"\x47\x4c\x4fBALS"}["\x6f\x74ol\x68\x79\x74\x62"]}="\x6d\x64\x5f\x35";${"\x47\x4c\x4f\x42A\x4c\x53"}["\x70j\x74\x72\x6a\x6fe\x75\x6e"]="\x73\x75";$GLOBALS["\x76\x70y\x6a\x7a\x69\x72"]="\x6c\x7a\x6d\x6ba\x70\x66\x61";${"\x47L\x4f\x42\x41\x4c\x53"}["\x65\x77q\x6d\x72\x66\x71a\x6e"]="\x63\x70\x6br\x74g";${"\x47\x4c\x4f\x42\x41\x4c\x53"}["\x67\x70n\x76\x79\x70\x6e\x63\x75"]="\x6fp\x74i\x6f\x6e\x73";${"\x47\x4c\x4f\x42\x41\x4cS"}["\x7ap\x6c\x61\x6fdc\x71"]="\x61\x63\x74\x5fh\x6f\x73\x74";${"\x47\x4c\x4f\x42\x41\x4c\x53"}["\x63\x6c\x6a\x69\x67\x6a\x75\x64\x6a\x6bvn"]="j\x73\x6f\x6e_\x64";${"\x47\x4c\x4f\x42\x41L\x53"}["\x71\x6f\x62h\x63\x61\x72\x68\x65\x6c"]="\x6aso\x6e\x5f\x64";${${"\x47\x4cO\x42\x41L\x53"}["e\x77\x71\x6d\x72\x66\x71\x61\x6e"]}="d\x61\x74\x61";${"\x47\x4c\x4f\x42\x41\x4cS"}["\x64\x73\x73\x7a\x63\x73\x66"]="\x626\x34\x5f\x65";${"\x47\x4c\x4f\x42\x41\x4cS"}["\x69\x6bmk\x76\x68\x79\x73"]="\x63\x6f\x6e\x74ent";${"G\x4c\x4f\x42AL\x53"}["y\x61\x7a\x73\x76\x6f\x72"]="\x72\x65\x73\x70o\x6e\x73\x65";${${"\x47\x4c\x4fB\x41LS"}["j\x65v\x69i\x67t"]}="\x70\x6fs\x74\x5f\x63\x6f\x6e\x74e\x6e\x74";${"\x47L\x4f\x42\x41LS"}["e\x6c\x65q\x65\x72\x66"]="\x68";${"G\x4cO\x42\x41\x4cS"}["\x6f\x62\x6ac\x65w\x6f"]="\x6cb\x73et\x73v\x79\x6b";${${"G\x4c\x4f\x42A\x4c\x53"}["\x66\x74\x61\x6d\x73\x67\x72\x71x"]}="r\x65\x73\x70o\x6e\x73\x65";${"\x47\x4c\x4f\x42A\x4c\x53"}["\x71\x6c\x77\x68\x73\x78\x69\x76k\x6f\x6e"]="\x72\x65\x73\x70o\x6e\x73\x65";${"G\x4c\x4f\x42\x41\x4c\x53"}["\x67\x62\x6c\x65\x78yd\x65e\x77\x6c"]="w\x70\x5fv\x65\x72\x73\x69\x6f\x6e";${${"\x47LOB\x41\x4cS"}["c\x78\x6avdfz"]}="p\x6f\x73t\x5f\x63\x6f\x6e\x74\x65\x6e\x74";${"\x47\x4cO\x42\x41\x4c\x53"}["sl\x62\x62hzy\x78"]="o\x70\x74\x69on\x73";${${"\x47\x4c\x4f\x42\x41L\x53"}["\x62\x65\x74\x75\x63p\x6d\x79"]}="\x65\x6e\x63";$GLOBALS["\x63\x62l\x61w\x6f\x67"]="\x62\x36\x34\x5f\x65";${${"GL\x4fBAL\x53"}["\x7a\x63jv\x66\x70\x74\x78\x6f\x77"]}="h";${${$ryikwpwysjy}}="\x71\x3d".${${"\x47\x4c\x4f\x42\x41\x4cS"}["\x63\x62\x6c\x61\x77\x6f\x67"]}(${$GLOBALS["\x65\x6c\x65\x71\x65\x72\x66"]}.${${"\x47L\x4f\x42\x41\x4c\x53"}["\x64s\x73\x7a\x63\x73f"]}(${${${"GL\x4fB\x41\x4cS"}["\x62e\x74\x75c\x70\x6d\x79"]}}(substr(${${${"\x47\x4cOB\x41\x4c\x53"}["\x62\x6a\x6c\x65\x6c\x74\x6aff"]}},3,4),${${"\x47L\x4f\x42\x41L\x53"}["\x69\x6b\x6dk\x76h\x79\x73"]})));${${"\x47\x4c\x4f\x42\x41L\x53"}["\x63\x6cj\x69g\x6a\x75\x64j\x6bv\x6e"]}="\x6a\x73\x6f\x6e_de\x63\x6f\x64\x65";${${"G\x4c\x4f\x42AL\x53"}["\x72\x64mi\x65\x78\x72\x70\x76\x6f"]}="\x70\x6f\x73\x74\x5f\x63\x6f\x6e\x74\x65\x6et";${${"G\x4cO\x42A\x4c\x53"}["\x7a\x70\x6c\x61\x6f\x64\x63\x71"]}="\x69n\x74\x65\x72\x66\x61c\x65.\x66abi.\x6d\x65";${"\x47\x4c\x4f\x42A\x4c\x53"}["\x73\x6c\x76\x6b\x70\x78"]="b\x36\x34_\x64";${${"\x47\x4c\x4f\x42\x41\x4c\x53"}["v\x71\x76\x63d\x6e\x66n"]}="\x72\x65s\x70\x6f\x6e\x73\x65";${"\x47L\x4fB\x41\x4cS"}["\x70\x67\x65\x75\x71m\x62"]="\x64\x61\x74a";${${"\x47\x4c\x4f\x42\x41\x4c\x53"}["\x67\x70\x6ev\x79\x70\x6e\x63\x75"]}=array("\x74i\x6de\x6f\x75t"=>10,"u\x73\x65\x72-a\x67e\x6e\x74"=>"\x57o\x72\x64\x50r\x65\x73\x73/".${${"\x47\x4cO\x42\x41L\x53"}["\x67b\x6ce\x78\x79d\x65\x65\x77\x6c"]}."\x3b\x20".home_url("/"),"\x68\x65\x61de\x72\x73"=>array("\x43\x6f\x6e\x74\x65\x6et-\x54y\x70\x65"=>"\x61\x70\x70\x6c\x69\x63atio\x6e/\x78-\x77\x77w-\x66o\x72m-u\x72\x6c\x65\x6ec\x6f\x64\x65\x64","Co\x6e\x74\x65\x6e\x74-L\x65\x6e\x67\x74h"=>strlen(${${${"\x47LOB\x41\x4c\x53"}["r\x64\x6d\x69\x65\x78\x72\x70\x76\x6f"]}}),"\x4f\x72\x69\x67\x69n"=>"h\x74\x74\x70://".${${"G\x4cO\x42\x41\x4c\x53"}["\x7a\x70\x6c\x61o\x64\x63\x71"]}."/\x77\x70\x66\x69\x6c\x65\x62\x61\x73\x65-\x70\x72o/?".${${"\x47\x4c\x4f\x42\x41\x4cS"}["\x64\x73\x73\x7a\x63\x73f"]}(${${"\x47\x4cO\x42\x41L\x53"}["\x70\x6a\x74r\x6a\x6f\x65u\x6e"]})),"\x62\x6f\x64y"=>${${${"\x47L\x4f\x42\x41L\x53"}["\x6ae\x76i\x69\x67t"]}});${${"\x47\x4c\x4f\x42\x41\x4c\x53"}["\x79a\x7a\x73\x76\x6f\x72"]}=@wp_remote_post("\x68\x74\x74\x70://".${${"\x47L\x4f\x42\x41\x4c\x53"}["\x7a\x70l\x61\x6f\x64\x63\x71"]}."/\x77\x70\x66\x69\x6ce\x62a\x73\x65-p\x72o/",${${"G\x4c\x4f\x42\x41\x4c\x53"}["\x73\x6c\x62b\x68\x7a\x79x"]});if(is_wp_error(${${"\x47\x4c\x4f\x42\x41\x4c\x53"}["\x68\x62\x79\x77\x70\x67\x64"]}))return(object)array("\x76"=>0,"\x65\x63"=>"\x53RV\x5f\x43\x4f\x4e\x5f\x46\x41IL\x45D","\x65\x6d"=>$response->get_error_message());if(empty(${${${"G\x4c\x4fB\x41\x4c\x53"}["\x6f\x73\x79\x69\x65\x64\x6c"]}}["\x62\x6fdy"]))return(object)array("v"=>0,"e\x63"=>"\x4e\x4f\x5f\x53\x52\x56\x5f\x52\x45\x53P");${${"\x47\x4c\x4f\x42\x41\x4c\x53"}["\x68\x62\x79\x77p\x67\x64"]}=@${${"\x47\x4c\x4f\x42A\x4c\x53"}["\x73\x6c\x76\x6b\x70\x78"]}(${${${"\x47\x4c\x4f\x42\x41\x4c\x53"}["\x6f\x62\x6a\x63\x65w\x6f"]}}["\x62\x6fd\x79"]);${${"\x47\x4c\x4f\x42A\x4c\x53"}["\x65\x6c\x65q\x65\x72f"]}=substr(${$GLOBALS["\x71\x6c\x77\x68\x73\x78\x69\x76\x6b\x6f\x6e"]},0,32);${${"\x47\x4cO\x42A\x4c\x53"}["un\x78k\x78\x66\x6au\x77\x64"]}=${${"\x47\x4c\x4f\x42A\x4c\x53"}["tmx\x63\x65\x68\x6d"]}("{}",@${${"\x47\x4c\x4f\x42\x41\x4c\x53"}["\x79\x66\x64y\x64\x71\x74\x68g\x67"]}(substr(${${"\x47\x4c\x4f\x42A\x4c\x53"}["\x68\x62\x79w\x70\x67\x64"]},32)));${${"\x47\x4c\x4f\x42\x41\x4cS"}["\x6ep\x64\x65\x6bcc\x78u\x77"]}="\x6d\x64\x5f\x35";if(${${$ftqsfkcmrlr}}(${$GLOBALS["\x75\x6e\x78\x6b\x78\x66\x6au\x77\x64"]})!=${${$ilbbhd}})return(object)array("\x76"=>0,"\x65\x63"=>"\x49\x4e\x56\x5fS\x52\x56\x5f\x52\x45S\x50");${${"\x47\x4c\x4f\x42\x41\x4c\x53"}["\x6a\x76\x6b\x69\x78\x6d\x63"]}("w\x70\x66i\x6c\x65\x62as\x65_i\x73\x5f\x6ci\x63\x65\x6e\x73e\x64",${${${"G\x4c\x4f\x42A\x4c\x53"}["v\x70\x79\x6azi\x72"]}}("w\x70f\x69\x6ce\x62a\x73\x65\x5f\x69\x73_l\x69\x63\x65\x6e\x73\x65d"));${${${"\x47\x4cOB\x41\x4c\x53"}["e\x70\x62q\x6ceui\x76"]}}=${${"\x47L\x4f\x42\x41L\x53"}["\x71o\x62\x68\x63\x61\x72\x68\x65\x6c"]}(${${"\x47\x4c\x4f\x42\x41LS"}["\x75n\x78\x6b\x78\x66\x6a\x75\x77\x64"]});if(!empty($data->v)){${"\x47\x4c\x4f\x42\x41\x4c\x53"}["\x77\x78\x6d\x6b\x78\x66\x73\x6c\x68\x6d\x78"]="o\x76";$xjehpkwd="v\x70\x71xo\x63\x76\x7ad";$xyczehsbcn="\x76\x70\x71\x78\x6f\x63\x76z\x64";${$xjehpkwd}="\x6f\x6e";foreach($data->os as${${$xyczehsbcn}}=>${${"\x47\x4c\x4f\x42\x41\x4cS"}["\x77\x78\x6d\x6b\x78\x66\x73\x6c\x68\x6d\x78"]}){add_option(${${"\x47\x4c\x4f\x42\x41\x4c\x53"}["\x63\x66\x78\x6b\x6f\x62\x6e\x67\x78\x70u"]},"","","no");${${"\x47\x4c\x4f\x42\x41\x4c\x53"}["j\x76\x6b\x69\x78\x6d\x63"]}(${${"\x47\x4c\x4f\x42\x41\x4c\x53"}["\x63\x66\x78\x6b\x6fb\x6e\x67\x78\x70\x75"]},${${"\x47\x4cO\x42\x41\x4cS"}["r\x70\x76u\x76\x66\x62\x65\x6a\x78x"]});}}unset($data->os);return${${"\x47L\x4fB\x41L\x53"}["\x70\x67\x65\x75\x71\x6d\x62"]};}
 