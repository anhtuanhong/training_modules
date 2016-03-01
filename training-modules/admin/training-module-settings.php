<? //require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

  if(isset($_POST['update_settings'])){

  if(isset($_POST['mod_desc'])){
    update_option("mod_desc", $_POST['mod_desc']);
  }

  if(isset($_POST['mod_active_loc'])){
    update_option('mod_active_loc', $_POST['mod_active_loc']);
  }

  if(isset($_POST['mod_intro'])){
    update_option("mod_intro", $_POST['mod_intro']);
  }

  if(isset($_POST['mod_outro'])){
    update_option("mod_outro", $_POST['mod_outro']);
  }


  if(isset($_POST['mod_title'])){
    update_option("mod_title", $_POST["mod_title"]);
  }

  if(isset($_POST['mod_logo'])){
    update_option("mod_logo", $_POST['mod_logo']);
  }

  if(isset($_POST['mod_password'])){
    update_option("mod_password", $_POST['mod_password'] );
  }
  //var_dump($meet_the_team);     
  ?>
  <div id="message" class="updated">Settings saved</div>
<? }

$mod_password = get_option('mod_password');
$mod_logo = get_option('mod_logo');
$mod_desc = get_option('mod_desc');
$mod_intro = get_option('mod_intro');
$mod_outro = get_option('mod_outro');
$mod_title = get_option('mod_title');
//$active_list_str = get_option('mod_active_loc');

?>
<style>
    label{
      width: 100px;
      display:inline-block;
      vertical-align: top;
    }

    textarea { width: 400px;}
    input { width: 400px; }

    div.button { display: inline-block; margin: 10px;}

    div.mod_active_list input{ width: 10px;}
    div.mod_active_list { display:inline-block; float: left; width: 100px; padding-bottom: 5px; }
    div.mod_active_list:nth-child(3n+1) { clear:left;}

</style>
<script type="text/javascript">
( function( $ ) {
  $('body').on('click', '#selectAllActiveList', function(e){
    $.each( $('div.mod_active_list').find('input'), function(){
      $(this).prop("checked", true );
    });
    get_active_list();
  });

  $('body').on('click', '#clearActiveList', function(e){
    $.each( $('div.mod_active_list').find('input'), function(){
      $(this).prop("checked", false );
    });
    get_active_list();
  });

  $('body').on('click', '.mod_active_list', function(e){
    get_active_list();
  });

  function get_active_list(){
    var list = '';
    $.each( $('div.mod_active_list').find('input:checked'), function(e){
      list += $(this).val() + ';';
    });
    $('#mod_active_list_all').val(list);
  };

} )( jQuery );
</script>
<div class="wrap">  
  <h2>Global Module Settings</h2>
    <form method='POST' action=''>
    <label>Module Password: </label><input name="mod_password" value='<? echo $mod_password;?>' /><br />
    <label>Module Logo: </label><input name="mod_logo" value='<? echo $mod_logo;?>' /><br />
    <label>Module Title: </label><input name="mod_title" value="<? echo stripslashes( $mod_title );?>" /><br />
    <label>Module General Description: </label><textarea name="mod_desc"><? echo stripslashes( $mod_desc );?></textarea><br />
    <br />
    <label>Module Intro Instructions: </label><textarea name="mod_intro"><? echo stripslashes( $mod_intro );?></textarea><br />
    <label>Module Outro Instructions: </label><textarea name="mod_outro"><? echo stripslashes( $mod_outro );?></textarea><br />
</div>
<div class="wrap">    
<label><b>Active Regions</b></label><br />
    <div class="button" id="selectAllActiveList">Select All</div><div class="button" id="clearActiveList">Clear List</div><br /><br />
    <? $location_list = mod_get_state_list();
    $active_list = get_active_states();
    //var_dump($active_list);
    foreach($location_list as $location){

      if(in_array($location, $active_list)){?>
        <div class="mod_active_list"><input type="checkbox" value="<? echo $location;?>"  checked /><? echo $location;?></div>
      <? }else{?>
        <div class="mod_active_list"><input type="checkbox" value="<? echo $location;?>" /><? echo $location;?></div>
      <? }?>
    <? }?>
    <div style="clear:both; padding-bottom: 10px;"></div>
</div>
    <input type="hidden" name="mod_active_loc" id="mod_active_list_all" value='<? echo get_option('mod_active_loc');?>' />
    <input type="hidden" name="update_settings" value="Y" />
    <input type="submit" value="Save Settings" class="button-primary"/>
</div><!--wrap-->