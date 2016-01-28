<?php
/**
 * Copyright 2005-2011 MERETHIS
 * Centreon is developped by : Julien Mathis and Romain Le Merlus under
 * GPL Licence 2.0.
 *
 * This program is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License as published by the Free Software
 * Foundation ; either version 2 of the License.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY
 * WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A
 * PARTICULAR PURPOSE. See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with
 * this program; if not, see <http://www.gnu.org/licenses>.
 *
 * Linking this program statically or dynamically with other modules is making a
 * combined work based on this program. Thus, the terms and conditions of the GNU
 * General Public License cover the whole combination.
 *
 * As a special exception, the copyright holders of this program give MERETHIS
 * permission to link this program with independent modules to produce an executable,
 * regardless of the license terms of these independent modules, and to copy and
 * distribute the resulting executable under terms of MERETHIS choice, provided that
 * MERETHIS also meet, for each linked independent module, the terms  and conditions
 * of the license of that module. An independent module is a module which is not
 * derived from this program. If you modify this program, you may extend this
 * exception to your version of the program, but you are not obliged to do so. If you
 * do not wish to do so, delete this exception statement from your version.
 *
 * For more information : contact@centreon.com
 *
 */

require_once "../../require.php";
require_once $centreon_path . 'www/class/centreon.class.php';
require_once $centreon_path . 'www/class/centreonSession.class.php';
require_once $centreon_path . 'www/class/centreonDB.class.php';
require_once $centreon_path . 'www/class/centreonWidget.class.php';
require_once $centreon_path . 'www/class/centreonDuration.class.php';
require_once $centreon_path . 'www/class/centreonUtils.class.php';
require_once $centreon_path . 'www/class/centreonACL.class.php';

session_start();
if (!isset($_SESSION['centreon']) || !isset($_POST['widgetId'])) {
    exit;
}

require_once $centreon_path ."GPL_LIB/Smarty/libs/Smarty.class.php";

$path = $centreon_path . "www/widgets/open-tickets/src/";
$template = new Smarty();
$template = initSmartyTplForPopup($path . 'templates/', $template, "./", $centreon_path);

$centreon = $_SESSION['centreon'];
$widgetId = $_POST['widgetId'];
$db = new CentreonDB();
$widgetObj = new CentreonWidget($centreon, $db);
$preferences = $widgetObj->getWidgetPreferences($widgetId);

$pearDB = new CentreonDB();
$admin = $centreon->user->admin;
$canDoAction = true;
//if ($admin) {
//    $canDoAction = true;
//}
$actions  = "<option value='0'>-- "._("More actions")." -- </option>";
if ($canDoAction) { // || $centreon->user->access->checkAction("service_schedule_check")) {
    $actions .= "<option value='3'>"._("Service: Open ticket")."</option>";
}

if ($canDoAction) { // || $centreon->user->access->checkAction("host_acknowledgement")) {
    $actions .= "<option value='4'>"._("Host: Open ticket")."</option>";
}

$template->assign("widgetId", $widgetId);
$template->display('toolbar.ihtml');

?>
<link href="../../include/common/javascript/jquery/plugins/colorbox/colorbox.css" rel="stylesheet" type="text/css"/>
<script type="text/javascript" src="../../include/common/javascript/jquery/plugins/colorbox/jquery.colorbox-min.js"></script>
<script type='text/javascript'>
var tab = new Array();
var sid = '<?php echo session_id();?>';
var actions = "<?php echo $actions;?>";
var widget_id = "<?php echo $widgetId; ?>";

$(function() {
	$(".toolbar").html(actions);
	$(".toolbar").change(function() {
		if (jQuery(this).val() != 0) {
    		var checkValues = $("input:checked").map(function() {
    			var tmp = $(this).attr('id').split("_");
    			return tmp[1];
    		}).get().join(",");
    		parent.jQuery.colorbox({
    							href		:	"./widgets/open-tickets/src/action.php?widgetId="+widget_id+"&sid="+sid+"&selection="+checkValues+"&cmd="+jQuery(this).val(),
    							width		:	"50%",
    							height		:	"40%",
    							opacity		:	0.7,
    							overlayClose:	false,
    							iframe		: 	true
    						});

    		$(".toolbar").val(0);
		}
	});
});
</script>