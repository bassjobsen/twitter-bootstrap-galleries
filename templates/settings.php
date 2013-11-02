<div class="wrap"> 

<h2>Twitter Bootstrap Galleries <?php echo __('Settings','tbgal');?></h2> 

<form method="post" action="options.php"> 
<?php @settings_fields('twitterbootstrap-galleries-group'); ?> 
<?php @do_settings_fields('twitterbootstrap-galleries-group'); ?> 
<table class="form-table"> 
<tr valign="top"> 
<th scope="row">
<label for="setting_a"><?php echo __('Number of columns per row','tbgal');?></label></th> 
<td>
	<select name="number_of_columns" id="number_of_columns">
	
	<?php
	
	$numberofcolumns = (get_option('number_of_columns'))?get_option('number_of_columns'):4;
	
	foreach(array(1,2,3,4,6) as $number)
	{
		?><option value="<?php echo $number ?>" <?php echo ($numberofcolumns==$number)?' selected="selected"':''?>><?php echo $number ?></option><?php
	}	
	?>
	</select>
</td> 
</tr> 
</table> 
<?php @submit_button(); ?> </form> 
</div>
