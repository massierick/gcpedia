<?php
/**
 * Disclaimer - use with EmailUpdate extension
 * 
 * 
 */

if ( !defined('MEDIAWIKI') ) {
        echo <<<EOT
To install this extension, put the following line in LocalSettings.php:
require_once("$IP/extensions/GC_Disclaimer/disclaimer.php");
EOT;
        exit( 1 );
}

$wgHooks['BeforePageDisplay'][] = 'showDisclaimer';

$dir = dirname( __FILE__ ) . '/';

// Lang file
$wgExtensionMessagesFiles['Disclaimer'] = $dir . 'disclaimer.i18n.php';

require_once("$IP/extensions/GC_Disclaimer/Addaccepted.php");

// Load the ajax responder and register it
$wgAutoloadClasses['AddAccepted']	= __DIR__  . '/Addaccepted.php';
$wgAPIModules['addaccepted'] = 'AddAccepted';

///Function to compare current date and date last accepted.
/**
 *
 * @param $lastdate date string
 *
 * @return bool
 * 
 * @author Ilia Salem
 */
function lastaccept( $lastdate ) {
	
	$ltime = explode( '.', $lastdate );
	
	if(count($ltime)>1)
	{
		$lasttime = $ltime[2] * 365 + $ltime[1] * 30 + $ltime[0];
		$now = explode( '.', date("d.m.y"));
		$nowtime = $now[2] * 365 + $now[1] * 30 + $now[0];
	
		global $wgDisclaimReset;
	
		$lasttime += $wgDisclaimReset;
	
		return ( $nowtime >= $lasttime );
	}
	else
	{
		return true;
	}	
}

///Create the disclaimer output page
/**
 * 
 * @return bool returns true on completion
 * 
 * @author Ilia Salem
 */
function showDisclaimer( &$out ) {
	global $wgUser, $wgTitle, $wgLang, $wgOut, $wgScriptPath;
	//$out->setHTMLTitle('GCpedia Disclaimer');
	require_once('dbdisclaimer.php');
	$userName = $wgUser->getName();
	$timeout = true;
	$dbw = wfGetDB( DB_MASTER );
	
	# select user from table
	$queryString = "SELECT * FROM `accepted` WHERE username = \"$userName\" OR username = \"" . str_ireplace( "'", "-*-", $userName ) .'"';
	$result = $dbw->query($queryString);
	
	 try{
		$row = $dbw->fetchRow( $result );
		if(count($row)>1)
		{
			$timeout = lastaccept( $row[1] );
		}
	} catch( Exception $e ) {
		$row = array(0);
		# create table
		require_once('dbdisclaimer.php');
	}

if( $wgUser->isLoggedIn() ) {

	if ($timeout and strpos( $wgTitle, 'Special:ConfirmEmail' ) === false and  strcasecmp( $wgTitle, 'Special:UserLogin' ) != 0 and strcasecmp( $wgTitle, 'GCPEDIA:Terms and conditions of use' ) != 0 and strcasecmp( $wgTitle, 'GCPEDIA:Conditions d\'utilisation' ) != 0 and strcasecmp( $wgTitle, 'Help:Code of Conduct') != 0 and strcasecmp( $wgTitle, 'GCPEDIA:Privacy notice' ) != 0 and strcasecmp( $wgTitle, 'Avis de confidentialité' ) != 0 ) {
	
	# get current page URL before we change it in disclaimerHead
	$fullURL = $wgTitle->getFullURL();
	
	# create <head>, load extension-specific script(s)
	$dhead = new disclaimerHead;
	$dhead->execute();

	# load head scripts
	echo $out->getHeadLinks() . "\n";
	echo $out->getHeadScripts() . "\n";
	echo '<link href="' . $wgScriptPath . '/skins/Vector/GCWeb/assets/favicon.ico" rel="icon" type="image/x-icon">';
	
	?>

		<link rel="stylesheet" href="<?php echo $wgScriptPath; ?>/skins/monobook/main.css?270" media="screen" />
		<style>
		.terms_button {
			margin: 5px 3px 17px 3px;
			height: 38px;
			font-size: 2em;
			/*background-color: #22FF22;*/
		}
		.terms_button_close {
			/*background-color: #FF2222;*/
		}
		
		.checkboxRequired {
			color: #000000;
			font-weight: bold;
			font-size: 1.06em;
		}
		
		.proceedenabled {
			background-color: #88ccff;
			height:2.5em;
			width:7.5em;
		}
		.proceeddisabled {
			/*background-color: #ff5555;*/
			height:2.5em;
			width:7.5em;
		}
		</style>

	</head>
	<body style ="background-color:white;">
			
		<div id="globalWrapper">
				<div style="margin:auto;
					border:1px dashed #000;
					min-width:512px;
					max-width:1024px;
					width:70%;
					margin-top:40px;
					background-color:white;
					padding: 5px 20px 8px 20px;
					/* for IE */
					filter:alpha(opacity=90);
					/* CSS3 standard */
					opacity:0.9;
					color:#000;
					clear:both;
					">
					<?php ?>
					<h2 nstyle="font-size:133%; background-color: #ac1a2f; color: white; padding-left:7px;"><?php if ( $wgLang->getCode() == 'en' ) echo "Welcome"; else echo "Bienvenue";?>, <?php if ( $wgUser->getRealName() != '' ) echo $wgUser->getRealName(); else echo $wgUser->getName(); ?></h1>
					<ul style="float: right;">
						<li> <a href='<?php echo $fullURL . "?setlang="
						. ( ( $wgLang->getCode() == 'en' ) ? fr : en ) . "'>"
						. wfMsg( 'emailupdate-language' ); ?></a> </li>
					</ul>
					<br />
					<!-- Instructions -->
					<p style="padding-left:7px;"> <?php echo wfMsg( 'disclaimer-instruc' ); ?> </p>
					
					<br />
				</div>
			<!-- Step 1 -->
				<div style="
					width:92%;
					padding: 17px 2px 5px 9%;
				">
					<div style="float:left;
						width:40%;
					">
					
					<p>
						<b><?php echo wfMsg ( 'disclaimer-step-1' ); ?></b> 
						<br /> <br />
						<?php echo wfMsg ( 'disclaimer-step-2-desc' ); ?>
					</p>
					
					<ul>
						<li> <a onclick="document.getElementById('terms').style.display='block';" href = '#'><?php if ( $wgLang->getCode() == 'en' ) echo "Terms and conditions of use"; else echo "Conditions d'utilisation"; ?> </a> </li>
					</ul>
					
					<input name='accept' id='accept' type='checkbox' onclick="getChecked(); if (document.getElementById('accept').checked) document.getElementById('lblaccept').className=''; else document.getElementById('lblaccept').className='checkboxRequired';
					" /> <label class="checkboxRequired" id='lblaccept' for='accept'> <?php if( $wgLang->getCode() == 'en' ) echo "I have read, understood, and agree to the terms and conditions of use."; else echo "J'ai lu, je comprends et j'accepte les conditions d'utilisation.";?> </label>
					
					<div style="clear:both;"></div>
					</div >
			<!-- Step 2 -->
					<div style="float:left;
						margin-left:10%;
						width:40%;
					">
					<form>
					<p> 
						<b> <?php echo wfMsg ( 'disclaimer-step-2' ); ?> </b>
						<br />
						<?php echo emailCheck(); ?>
					</p>
					</form>
					<br />
					</div>
				</div>
				<div style="clear:both;
					position:relative;
					top: 50px;
					left: 44%;
					width: 50%;
					">
					<!-- Submit -->
					<input class="proceeddisabled" style='margin: 4px 0 3px 9px;' name='proceed' type='button' id='proceed'
						value="<?php echo wfMsg('disclaimer-submit'); ?>"
						disabled='disabled' onclick="mw.loader.using( 'mediawiki.api', function () {
			( new mw.Api() ).get( {
				action: 'addaccepted',
				username: '<?php echo str_ireplace( "'", "-*-", $wgUser->getName() ); ?>',
			} ).done( function ( data ) {
					location.reload(true);
			} );
		} );"
					/>
					
					<p style="text-align: right;"> <?php echo wfMsg('emailupdate-help'); ?> </p>
					
				</div>
		</div>

		<div id="terms" style="display:none; width:90%; height:88%; position:fixed; left:5%; top:25px; border:solid 3px; border-top-left-radius:25px; border-bottom-left-radius:25px; padding: 3px 8px 12px 9px; overflow-y: scroll;
		background-color:white;
		/* for IE */
		filter:alpha(opacity=100);
		/* CSS3 standard */
		opacity:1.00;
		color:#000;">

		<?php /* Remove the X for now ?> 
		<div style="position:fixed; left:4.5%; top:3.15%; font-size:25px; font-weight:bold; font-family:'Comic Sans MS'; color:red; cursor:pointer;" onclick="
		document.getElementById('terms').style.display='none';">X</div>
		<?php */ ?>

		<center><h1><?php echo wfMsg("disclaimer-terms-head"); ?></h1></center>
		<br />
		
		<?php echo generateTermsHTML( $wgLang->getCode() );//wfMsg("disclaimer-terms-page"); ?>
		<?php // echo '<iframe src="http://192.168.0.100/gcwiki120-Mo/index.php/GCPEDIA:Terms_and_conditions_of_use" width=100% height=80%></iframe>'; ?>

		<br />

		<center>
		<input type="button" id="btnCloseTerms" value=<?php echo wfMsg("disclaimer-close-terms-txt"); ?>
		class="terms_button terms_button_close"
		onclick="
		document.getElementById('terms').style.display='none';">

		<input type="button" id="btnAcceptTerms" value=<?php echo wfMsg("disclaimer-accept-terms-txt"); ?>
		class="terms_button"
		onclick="
		document.getElementById('accept').checked = true;
		getChecked();
		document.getElementById('terms').style.display='none';
		if (document.getElementById('accept').checked) document.getElementById('lblaccept').className=''; else document.getElementById('lblaccept').className='checkboxRequired';
		">
		</center>

		</div>

	</body>
</html>
	<?php
	exit; //kill process from rendering the actual page
	}
}

	return true;
}

/**
*
* @return string containing HTML of the current terms page in the appropreate language
*
* @author Ilia Salem
*/
function generateTermsHTML( $lang ){
	global $wgParser;		// global parser object, this will be needed to get the page parsed and converted into HTML format from the stored wikitext

	( $lang == 'en' ) ? $pageID = 8735 : $pageID = 24559;		// get the appropriate-language page's ID

	$termsHTML = '';
	$termstext = '';
	
	// set uo options for parsing the page
	$opts = new ParserOptions();
	$opts->setEditSection( false );				// no edit section links
	$opts->setMaxTemplateDepth( 0 );			// no templates (since they are mostly navigation aides and hence couterproductive in this case)
	$opts->setExternalLinkTarget("_blank");		// target external links to open in new a tab / window

	// get page wikitext - latest version is the only one we're interested in, store for parsing
	$page = WikiPage::newFromId( $pageID );
	$termstext = $page->getText();

	// remove manually added links to other language versions of the terms page
	$termstext = str_ireplace("''(In [[GCPEDIA:Terms and conditions of use|English]])''", "", $termstext);
	$termstext = str_ireplace("''(En [[GCPEDIA:Conditions d'utilisation|Français]])''", "", $termstext);

	// remove language and category links
	$pattern[0] = "/\[\[(en|fr):(.*)\]\]/i";
	$pattern[1] = "/\[\[(Category):(.*)\]\]/i";
	$replacement = "";
	$termstext = preg_replace( $pattern, $replacement, $termstext );

	// un-link internal links, leaveing only links' text
	$pattern1 = "/\[\[([^\|]*)\]\]/";
	$pattern2 = "/\[\[([^\|]*)(\|+)(.*)\]\]/";
	$replacement1 = "$1";
	$replacement2 = "$3";
	$termstext = preg_replace( $pattern1, $replacement1, $termstext );
	$termstext = preg_replace( $pattern2, $replacement2, $termstext );

	// parse the page to get the HTML, using the previously defined options
	$termsHTML = $wgParser->parse( $termstext, $page->getTitle(), $opts );

	return str_ireplace( "Template recursion depth limit exceeded (0)", "", $termsHTML->getText() );	// from the HTML we remove the messages that are generated by removal of all template parsing
}

///Function to validate and update the users email address + create the HTML form
/**
 *
 * @return string - HTML form
 * 
 * @author Matthew April
 */
function emailCheck() {
	global $wgRequest, $wgUser, $wgTitle, $wgEnableEmail, $wgEmailAuthentication;
	
	# init vars
	$currentEmail = $wgUser->getEmail();
	$action = $wgTitle->getLocalUrl();
	$emailOption = $wgRequest->getVal( 'EmailUpdate' );
	$err = "";
	$out = "";
	$confmessage = "";	//confirmation email notification message (blank if not sent)
	
	# radio option to update email
	if( $emailOption == "outdated" ) {
		
		if( $wgEnableEmail ) {
			
			$newEmail = $wgRequest->getText( 'newEmail' );
			$newEmail = trim($newEmail);
			$oldEmail = $currentEmail;
			$oldEmail = trim($oldEmail);
			
			# returns true on success
			$validEmail = Sanitizer::validateEmail( $newEmail );
			
			if( $validEmail === true && $newEmail != $oldEmail ) {
				
				# set new email + invalidate it
				$wgUser->setEmail( $newEmail );
				
				# update var for form output
				$currentEmail = $newEmail;
				
				if( $wgEmailAuthentication ) {
					
					$wgUser->invalidateEmail();
					# send confirmation email
					$result = $wgUser->sendConfirmationMail();
					
					if( $result->isOK() ) { //test me
						$err = wfMsg( 'mailerror', htmlspecialchars( $result->getMessage() ) );
					} else {
						$confmessage = wfMsg( 'emailupdate-confirm' );
					}
				}
				
			} else {
				# get error
				if( $newEmail == $oldEmail ) {
					$err = wfMsg('emailupdate-duplicate-error');
				} else {
					$err = wfMsg('invalidemailaddress');
				}
			}
			
			if( $oldEmail != $newEmail ) {
				wfRunHooks( 'PrefsEmailAudit', array( $wgUser, $oldEmail, $newEmail ) );
			}
		}
	}
	
	# form output
	$out .= "<div style='float: left; width: 40%; border-right: 1px solid black; clear:none; margin-right: 7px; padding: 3px 7px 5px;'>". wfMsg('emailupdate-current') . " <b>$currentEmail</b> <br/><br />
			<input type='checkbox' name='emailcheck' id='emailcheck' onclick=\"getChecked(); if (document.getElementById('emailcheck').checked){ document.getElementById('lblemail').className=''; document.getElementById('emailupdateform').style.display = 'none'; } else{ document.getElementById('lblemail').className='checkboxRequired'; document.getElementById('emailupdateform').style.display = 'block'; }
					\" /> <label class=\"checkboxRequired\" id='lblemail' for='emailcheck'>" . wfMsg('emailupdate-confirm-address') . "</label><br /></div>";
	
	$out .= "<div id='emailupdateform' style='float: left; width:55%; margin-bottom:1px;><form method='POST' action='$action'>
				<input type='hidden' id='outdateEmail' name='EmailUpdate' value='outdated' CHECKED>
				<label for='newEmail'>" . wfMsg('emailupdate-update') . "
				<input type='text' name='newEmail' size='30' value='' /></label>
					<br/>
					<br/>
				<input type='submit' name='submit' value='". wfMsg('emailupdate-submit') ."' /> <div style='color:green;'>$confmessage</div> <div class='error'> $err </div>
				
			</form> </div>";
	
	return $out;
}


///diclaimerHead class
/**
 * creates the head element for the page.
 *
 *
 * @author Ilia Salem
 */

class disclaimerHead extends QuickTemplate {
	var $skin;
	
	function execute() {
		global $wgRequest, $wgLang, $wgTitle, $wgScriptPath;
		
		$title = Title::newFromText('GCpedia Disclaimer');
		$wgTitle = $title;
		
		//$this->skin = $skin = $this->data['skin'];
		$action = $wgRequest->getText( 'action' );

		//suppress warnings to prevent notices about missing indexes in $this->data
		wfSuppressWarnings();
		?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="<?php $this->text('xhtmldefaultnamespace') ?>" <?php 
	foreach($this->data['xhtmlnamespaces'] as $tag => $ns) {
		?>xmlns:<?php echo "{$tag}=\"{$ns}\" ";
	} ?>xml:lang="<?php $this->text('lang') ?>" lang="<?php $this->text('lang') ?>" dir="<?php $this->text('dir') ?>">
	<head>
	

		<title>GCpedia Disclaimer</title>

		<script type="text/javascript">
	function getChecked() {
			if( document.getElementById('accept').checked == true && document.getElementById('emailcheck').checked == true ) { 
				document.getElementById('proceed').disabled=0;
				document.getElementById('proceed').className = 'proceedenabled';
			} else { 
				document.getElementById('proceed').disabled=1;
				
				document.getElementById('proceed').className = 'proceeddisabled';
			}
		}
	</script>
	<META HTTP-EQUIV="CACHE-CONTROL" CONTENT="NO-CACHE">
		<?php
	}
	
}

?>