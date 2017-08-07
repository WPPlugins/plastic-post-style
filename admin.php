<?php

function pps_options(){

$settings = pps_get_settings(); ?>

<div class="wrap">
<h2 style="font-size: 1.4em; border-bottom: 1px solid #464646; margin-bottom: 5px">PPS OPTIONS</h2>
<b><small>by <a href="mailto:mio@wembley.jp">MIO</a> of <a href="http://www.wembley.jp">wembley</a>.&nbsp;&nbsp;
Visit <a href="http://www.wembley.jp/wp-plugins/plastic-post-style">my plugin page</a></b></small>.
<form method="post" action="options.php" name="PPS">

<?php
	wp_nonce_field('update-options');
	settings_fields('pps_options');
?>

<div id="poststuff" class="metabox-holder">
<div>
<div class='postbox'>
	<h3 class='hndle'><span><?php _e('General Settings','pps_lang');?></span></h3>
<div class='inside'>
	<p><?php _e('Change general settings for all posts.','pps_lang');?></p>

	<table class="form-table" style="margin-top: 0; padding: 0">
		<tbody>
		
    <tr valign="top"><td><b><?php _e('XML Parser','pps_lang');?></b></td>
    <td>
      <input type="checkbox" name="pps_xmlparser_content" value="enable" <?php if($settings['xmlparser_content'] == 'enable') echo 'checked'; ?> /><?php _e('Content','pps_lang');?>&nbsp;&nbsp;&nbsp;
      <input type="checkbox" name="pps_xmlparser_excerpt" value="enable" <?php if($settings['xmlparser_excerpt'] == 'enable') echo 'checked'; ?> /><?php _e('Excerpt','pps_lang');?>&nbsp;
    </td><td><?php _e('Enables XML Parser to complement html tags. <br />Setting "excerpt" will  prevent tags from being broken when you split the content automatically by settings bellow.','pps_lang');?></td></tr>

    <tr valign="top"><td><b><?php _e('Auto  &lt;br&gt;','pps_lang');?></b></td>
    <td>
      <input type="checkbox" name="pps_br_content" value="enable" <?php if($settings['br_content'] == 'enable') echo 'checked'; ?> /><?php _e('Content','pps_lang');?>&nbsp;&nbsp;&nbsp;
      <input type="checkbox" name="pps_br_excerpt" value="enable" <?php if($settings['br_excerpt'] == 'enable') echo 'checked'; ?> /><?php _e('Excerpt','pps_lang');?>
    </td><td><?php _e('Remove &lt;p&gt; filter and add &lt;br /&gt; for paragraphs.','pps_lang');?>
    </td></tr>
    
    <tr valign="top"><td><b><?php _e('Header Tags','pps_lang');?></b></td>
    <td><input type="text" name="pps_header" value="<?php echo $settings['header']; ?>"></td><td><?php _e('You can add any tags or texts you want above & bellow the content.','pps_lang');?></td></tr>

    <tr valign="top"><td><b><?php _e('Footer Tags','pps_lang');?></b></td>
    <td><input type="text" name="pps_footer" value="<?php echo $settings['footer']; ?>"></td></tr>
  
		</tbody>
	</table>
	</div>
</div>

</div></div> <!--closing metabox containers-->

<div id="poststuff" class="metabox-holder">
<div>

<div class='postbox'>
	<h3 class='hndle'><span><?php _e('Excerpt Settings','pps_lang');?></span></h3>
<div class='inside'>
	<p><?php _e('Change excerpt settings on your toppage.','pps_lang');?></p>

	<table class="form-table" style="margin-top: 0; padding: 0">
		<tbody>

    <tr valign='top'>
  		<th scope="row" valign="middle"><strong><?php _e('Excerpt Length','pps_lang');?></strong></th>
      <td><input type="text" name="pps_length" value="<?php echo $settings['length']; ?>"></td>
      <td><?php _e('The length, in letters, of excerpts. (multibyte count)','pps_lang');?></td>
    </tr>

    <tr valign='top'>
  		<th scope="row" valign="middle"><strong><?php _e('Excerpt Text','pps_lang');?></strong></th>
      <td><input type="text" name="pps_moretext" value="<?php echo $settings['moretext']; ?>"></td>
      <td><?php _e('"Read More" link will be generated to the post using this text.(HTML tags available)','pps_lang');?></td>
    </tr>

    <tr valign='top'>
  		<th scope="row" valign="middle"><strong><?php _e('Make Link Nofollow','pps_lang');?></strong></th>
      <td>
      <?php
  	if( $settings['nofollow'] === 'No'){
  		$No = ' SELECTED';
  	} else {
  		$Yes = ' SELECTED';
  	}
  ?>
        <select name="pps_nofollow">
          <option value="Yes"<?php echo $Yes; ?>>Nofollow</option>
          <option value="No"<?php echo $No; ?>>Dofollow</option>
        </select>
      </td>
      <td><?php _e('Set "Read More" link nofollow or dofollow','pps_lang');?></td>
    </tr>

    <tr valign='top'>
  		<th scope="row" valign="middle"><strong><?php _e('Separater','pps_lang');?></strong></th>
      <td>
      <?php
      	if( $settings['separater'] === "Period"){
      		$separater_period=' SELECTED';
      	} elseif ( $settings['separater'] === "Touten"){
          $separater_touten=' SELECTED';
      	} elseif ( $settings['separater'] === "Space"){
          $separater_space=' SELECTED';
      	} elseif ( $settings['separater'] === "Paragraph"){
          $separater_paragraph=' SELECTED';
      	} else {
      		$separater_none=' SELECTED';
      	}
      ?>
      <select name="pps_separater">
        <option value="None"<?php echo $separater_none; ?>>None</option>
        <option value="Period"<?php echo $separater_period; ?>>Period</option>
        <option value="Touten"<?php echo $separater_touten; ?>>Touten</option>
        <option value="Space"<?php echo $separater_space; ?>>Space</option>
        <option value="Paragraph"<?php echo $separater_paragraph; ?>>Paragraph</option>
      </select>
      </td>
      <td><?php _e('Select the element you use as a separater of the content.','pps_lang');?></td>
    </tr>

    <tr valign="top"><td><b><?php _e('Allowable HTML Tags','pps_lang');?></b></td>
    <td><input type="text" name="pps_allowedtags" value="<?php echo $settings['allowedtags']; ?>"></td><td><?php _e('This plugin removes tags from the excerption except specified tags written in this box. (with comma like <strong>a,br,img</strong>)','pps_lang');?><br /><strong><?php _e('If you want to allow all tags, please write "all".','pps_lang');?></strong></td></tr>

		</tbody>
	</table>
	</div>
</div>

</div></div> <!--closing metabox containers-->

<input type="hidden" name="action" value="update" /><input type="submit" name="Submit" value="<?php _e('Save Changes','pps_lang'); ?>" />

<input type="hidden" name="page_options" value="pps_xmlparser_content,pps_xmlparser_excerpt,pps_br_content,pps_br_excerpt,pps_header,pps_footer,pps_length,pps_moretext,pps_nofollow,pps_separater,pps_allowedtags" />

</form>
</div>

<?php } ?>