<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

//Adding menus.
add_action('admin_menu', 'vidyen_gatekeeper_menu');

//Sub menu. Adding it to the VYPS system.

function vidyen_gatekeeper_menu()
{
	$parent_page_title = "VidYen Gatekeeper";
	$parent_menu_title = 'VY Gatekeeper';
	$capability = 'manage_options';
	$parent_menu_slug = 'vidyen_gatekeeper';
	$parent_function = 'vidyen_gatekeeper_menu_page';
	$icon_url = plugin_dir_url( __FILE__ ) . 'includes/images/coin.png';
	add_menu_page($parent_page_title, $parent_menu_title, $capability, $parent_menu_slug, $parent_function, $icon_url);

}

//The actual menu
function vidyen_gatekeeper_menu_page()
{
	global $wpdb;

	if (isset($_POST['crypto_wallet']))
	{
		//As the post is the only thing that edits data, I suppose this is the best place to the noce
		$vyps_nonce_check = $_POST['vypsnoncepost'];
		if ( ! wp_verify_nonce( $vyps_nonce_check, 'vidyen-gatekeeper-nonce' ) )
    {
				// This nonce is not valid.
				die( 'Security check' );
		}

		$gatekeeper_parsed_array = vidyen_gatekeeper_settings();
		$index = 1; //Lazy coding but easier to copy and paste stuff.

		//Text for button
		if (isset($_POST['button_text']))
		{
			$button_text = sanitize_text_field($_POST['button_text']);
		}
		else
		{
			$button_text  = $gatekeeper_parsed_array[$index]['button_text']; //make this the sql call to pull it
		}

		//The disclaimer text
		if (isset($_POST['disclaimer_text']))
		{
			$disclaimer_text = sanitize_textarea_field($_POST['disclaimer_text']);
		}
		else
		{
			$disclaimer_text  = $gatekeeper_parsed_array[$index]['disclaimer_text'];
		}

		//The EULA text. The text below the button if they claim to have read it.
		if (isset($_POST['eula_text']))
		{
			$eula_text = sanitize_textarea_field($_POST['eula_text']);
		}
		else
		{
			$eula_text  = $gatekeeper_parsed_array[$index]['eula_text'];
		}

		//The current WPM server (NOTE Not the pool but the proxy)
		if (isset($_POST['current_wmp']))
		{
			$current_wmp = sanitize_text_field($_POST['current_wmp']);
		}
		else
		{
			$current_wmp  = $gatekeeper_parsed_array[$index]['current_wmp'];
		}

		//The current pool
		if (isset($_POST['current_pool']))
		{
			$current_pool = sanitize_text_field($_POST['current_pool']);
		}
		else
		{
			$current_pool  = $gatekeeper_parsed_array[$index]['current_pool'];
		}

		//The current pool password if supplied
		if (isset($_POST['pool_password']))
		{
			$pool_password = sanitize_text_field($_POST['pool_password']);
		}
		else
		{
			$pool_password  = $gatekeeper_parsed_array[$index]['pool_password'];
		}

		//The crypto wallet. This field should have been required but *shrugs*
		if (isset($_POST['crypto_wallet']))
		{
			$crypto_wallet = sanitize_text_field($_POST['crypto_wallet']);
		}
		else
		{
			$crypto_wallet  = $gatekeeper_parsed_array[$index]['crypto_wallet'];
		}

		//Whether or not gatekeeper is active. If GK is not active the WM will never be
		if (isset($_POST['gatekeeper_active']))
		{
			$gatekeeper_active = intval($_POST['gatekeeper_active']); //should be 1 or 0
		}
		else
		{
			$gatekeeper_active  = 0;
		}

		//But is it possible to have the WM off while the GK is active.
		if (isset($_POST['wm_active']))
		{
			$wm_active = intval($_POST['wm_active']); //should be 1 or 0
		}
		else
		{
			$wm_active  = 0;
		}



    $table_name_gatekeeper = $wpdb->prefix . 'vidyen_gatekeeper';

		//Default data
	  $data = [
	      'button_text' => $button_text,
	      'disclaimer_text' => $disclaimer_text,
	      'eula_text' => $eula_text,
	      'current_wmp' => $current_wmp,
	      'current_pool' => $current_pool,
	      'pool_password' => $pool_password,
	      'crypto_wallet' => $crypto_wallet,
	      'gatekeeper_active' => $gatekeeper_active,
	      'wm_active' => $wm_active,
	  ];

			$wpdb->update($table_name_gatekeeper, $data, ['id' => 1]);
	    //$data_id = $wpdb->update($table_name_gatekeeper , $data);

	    //I forget thow this works
	    $message = "Added successfully.";
	}

	$gatekeeper_parsed_array = vidyen_gatekeeper_settings();
	$index = 1; //Lazy coding but easier to copy and paste stuff.
  //Repulls from SQL
	$button_text  = $gatekeeper_parsed_array[$index]['button_text'];
	$disclaimer_text  = $gatekeeper_parsed_array[$index]['disclaimer_text'];
	$eula_text  = $gatekeeper_parsed_array[$index]['eula_text'];
	$current_wmp  = $gatekeeper_parsed_array[$index]['current_wmp'];
	$current_pool  = $gatekeeper_parsed_array[$index]['current_pool'];
	$pool_password  = $gatekeeper_parsed_array[$index]['pool_password'];
	$crypto_wallet  = $gatekeeper_parsed_array[$index]['crypto_wallet'];
	$gatekeeper_active = $gatekeeper_parsed_array[$index]['gatekeeper_active'];
	$wm_active  = $gatekeeper_parsed_array[$index]['wm_active'];

	//It dawned on me that these need to go only oce after the SQL parse has been redone.
	if ($gatekeeper_active == 1)
	{
		$gate_checked = 'checked';
	}
	else
	{
		$gate_checked = '';
	}

	//It dawned on me that these need to go only oce after the SQL parse has been redone.
	if ($wm_active == 1)
	{
		$wm_checked = 'checked';
	}
	else
	{
		$wm_checked = '';
	}

	//It's possible we don't use the VYPS logo since no points.
  //$vyps_logo_url = plugins_url( 'includes/images/logo.png', __FILE__ );
	//$vidyen_gatekeeper_logo_url = plugins_url( 'includes/images/vyvp-logo.png', __FILE__ );

	//Adding a nonce to the post
	$vyps_nonce_check = wp_create_nonce( 'vidyen-gatekeeper-nonce' );

  $VYPS_worker_url = plugins_url( 'includes/images/stat_vyworker_001.gif',  __FILE__ );
	$VYPS_worker_img = '<div><img src="'.$VYPS_worker_url.'"></div>';
	//Static text for the base plugin
	$vidyen_gatekeeper_menu_html_ouput ='
	<br>'.$VYPS_worker_img.'
	<h1>VidYen Gatekeeper</h1>
	<p>Settings:</p>
	<table width=100%>
		<form method="post">
			<tr>
				<td><b>Button Text:</b></td>
				<td><input type="text" name="button_text" id="button_text" value="'.$button_text.'" size="128" required="true">
				<input type="hidden" name="vypsnoncepost" id="vypsnoncepost" value="'.$vyps_nonce_check.'"/></td>
			</tr>
			<tr>
				<td valign="top"><b>Disclaimer Text Above The Button:</b><br>HTML mark up<br><i>[img]image url[/img]<br>[b]bold[/b]<br>[br] for line breaks</i></td>
				<td><textarea name="disclaimer_text" id="disclaimer_text" rows="10" cols="130" required="true">'.$disclaimer_text.'</textarea></td>
			</tr>
			<tr>
				<td valign="top"><b>EULA Text Below The Button:</b><br>HTML mark up<br><i>[img]image url[/img]<br>[b]bold[/b]<br>[br] for line breaks</i></td>
				<td><textarea name="eula_text" id="eula_text" rows="10" cols="130">'.$eula_text.'</textarea></td>
			</tr>
			<tr>
				<td><b>Current WMP Proxy Server:</b></td>
				<td><input type="text" name="current_wmp" id="current_wmp" value="'.$current_wmp.'" size="128" required="true"></td>
			</tr>
			<tr>
				<td><b>Current Mining Pool:</b></td>
				<td><input type="text" name="current_pool" id="current_pool" value="'.$current_pool.'" size="128" required="true"></td>
			</tr>
			<tr>
				<td><b>Pool Password:</b></td>
				<td><input type="text" name="pool_password" id="pool_password" value="'.$pool_password.'" size="128"></td>
			</tr>
			<tr>
				<td><b>Your XMR Based Crypto Wallet:</b></td>
				<td><input type="text" name="crypto_wallet" id="crypto_wallet" value="'.$crypto_wallet.'" size="128" required="true"></td>
			</tr>
			<tr>
				<td><input type="checkbox" name="gatekeeper_active" id="gatekeeper_active" value="1" '.$gate_checked.'>Activate Gatekeeper</td>
				<td>Needs to be enabled for Webminer to work.</td>
			</tr>
			<tr>
				<td><input type="checkbox" name="wm_active" id="wm_active" value="1" '.$wm_checked.'>Activate WebMiner</td>
				<td><b>NOTE: For every 10 minutes an end user mines, 15 seconds will be given as a fee to VidYen for development funding!</b></td>
			</tr>
			<tr>
				<td><input type="submit" value="Save Settings"></td>
				<td></td>
			</tr>
		</form>
	</table>
	<h2>Disclaimer</h2>
	<p><b>NOTE: For every 10 minutes an end user mines, 15 seconds will be given as a fee to VidYen for development funding!</b></p>
	<p>If you do not like it, it would do you good to learn how to code or go back to just giving away your own efforts for free.</p>
	<p>You are responsible for creating your own disclaimer that legally handles your current situation (consent to mining, cookies, and/or above 18 years or older)</p>
	<p>Disclaimer cookies last for 24 hours. This is intentional to remind users what they are doing.</p>
	<p>This monetization is quite advanced compared to receiving checks from Adsense, but as advertising revenue dwindles for independant site ownswers, it is recommended you educate yourself on how to use Monero and Monero Alt coins.</p>
	<p>Using this plugin will connect you to 3rd parties whose privacy policies are on their respective webpages.</p>
	<h2>Instructions:</h2>
	<p>Fill out the fields where asked using your own Monero wallet to whichever pool of your choice. By default, it is set to the VidYen WMP and MoneroOcean pools as it tends to be AV and Adblock friendly as possible.</p>
	<h2>Video Tutorial on how to create a Monero Wallet</h2>
	<iframe width="560" height="315" src="https://www.youtube.com/embed/x7yq-5PWWPo" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
	<h2>Video Tutorial on how Monero Pools Work</h2>
	<iframe width="560" height="315" src="https://www.youtube.com/embed/_HS4HQvqOUY" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
	<h2>Spport</h2>
	<p>Feel free to open a post on the WordPress forums or reach out to us on the <a href="https://discord.gg/6svN5sS" target="_blank">VidYen Discord</a></p>
	<p>It is my personal intention to make WebMining the default way of monetization in the future. If you do have problems, I will be happy to look into regardless of the issue. -Felty</p>
	<h2>VidYen WMP Proxy Servers</h2>
	<p>savona.vy256.com:8183</p>
	<p>vesalius.vy256.com:8443</p>
	<p>daidem.vidhash.com:8443</p>
	<p>clarion.vidhash.com:8286</p>
	<p>clarion.vidhash.com:8186</p>
	<h2>VidYen WMP TRTL Only Proxy Servers</h2>
	<p>igori.vy256.com:8256</p>
	<h2>Alternate 3rd party WMP Proxy Servers</h2>
	<p>Note: These may not be as adblock friendly as VidYen servers.</p>
	<p>webminer.moneroocean.stream:443</p>
	<h2>List of known valid Pools (Payouts handled through pools!)</h2>
	<p><a href="https://moneroocean.stream/" target="_blank">moneroocean.stream</a> - Algo Switching Pool Pay Out In XMR</p>
	<p><a href="http://turtlepool.space/" target="_blank">turtlepool.space</a> - Turtle Coin Pool</p>
	<p><a href="https://masari.miner.rocks/" target="_blank">masari.miner.rocks</a> - Masari Coin Pool</p>
	<p>xmrpool.eu</p>
	<p>moneropool.com</p>
	<p>monero.crypto-pool.fr</p>
	<p>monerohash.com</p>
	<p>minexmr.com</p>
	<p>usxmrpool.com</p>
	<p>supportxmr.com</p>
	<p>moneroocean.stream:100</p>
	<p>moneroocean.stream</p>
	<p>poolmining.org</p>
	<p>minemonero.pro</p>
	<p>xmr.prohash.net</p>
	<p>minercircle.com</p>
	<p>xmr.nanopool.org</p>
	<p>xmrminerpro.com</p>
	<p>clawde.xyz</p>
	<p>dwarfpool.com</p>
	<p>xmrpool.net</p>
	<p>monero.hashvault.pro</p>
	<p>osiamining.com</p>
	<p>killallasics</p>
	<p>arhash.xyz</p>
	<p>aeon-pool.com</p>
	<p>minereasy.com</p>
	<p>aeon.sumominer.com</p>
	<p>aeon.rupool.tk</p>
	<p>aeon.hashvault.pro</p>
	<p>aeon.n-engine.com</p>
	<p>aeonpool.xyz</p>
	<p>aeonpool.dreamitsystems.com</p>
	<p>aeonminingpool.com</p>
	<p>aeonhash.com</p>
	<p>durinsmine.com</p>
	<p>aeon.uax.io</p>
	<p>aeon-pool.sytes.net</p>
	<p>aeonpool.net</p>
	<p>supportaeon.com</p>
	<p>pooltupi.com</p>
	<p>aeon.semipool.com</p>
	<p>etn.spacepools.org</p>
	<p>etn.nanopool.org</p>
	<p>etn.hashvault.pro</p>
	<p> </p>
	<p>If you use a 3rd party WMP server, they may support different ones!</p>
	';

	//NOTE: I keep forgetting but wss://webminer.moneroocean.stream:443 is the webmining address for MO

  echo $vidyen_gatekeeper_menu_html_ouput;
}
