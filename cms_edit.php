<?php
/**************************************************
  CPG MiniCMS Plugin for Coppermine Photo Gallery
***************************************************/

require_once('include/init.inc.php');
$superCage = Inspekt::makeSuperCage($strict);

$req_array=array('referer','id','submit','conid','type','title','minicms_content');

foreach ($req_array as $cnf_item) {
    if ($superCage->get->keyExists($cnf_item)) {
        $request[$cnf_item] = $superCage->get->getRaw($cnf_item);
    }
    if ($superCage->post->keyExists($cnf_item)) {
        $request[$cnf_item] = $superCage->post->getRaw($cnf_item);
    }
}


if (!(GALLERY_ADMIN_MODE))
    cpg_die(ERROR, $lang_errors['access_denied'], __FILE__, __LINE__);

if (isset($request['referer'])) {
    $referer = urlencode(html_entity_decode($request['referer']));
    if (strpos($referer, "http") !== false) {
      $referer = urlencode("index.php?file=minicms/cms_edit");
    }
} else {
    $referer = urlencode("index.php?file=minicms/cms_edit");
}

if (isset($request['id'])) {
    $id = (int)$request['id'];
} else {
    $id = -1;
}

if(isset($request['submit']) && $request['submit']==$lang_minicms['submit'] && $request['id'] > -1){
    $MINICMS['conid']=(int)$request['conid'];
    $MINICMS['type']=(int)$request['type'];
    $title=mysql_real_escape_string($request['title']);
    $content=mysql_real_escape_string($request['minicms_content']);
    $query = "UPDATE {$CONFIG['TABLE_CMS']} SET title = '$title', content = '$content', type = '{$MINICMS['type']}' WHERE ID = '$id'";
    $result = cpg_db_query($query);
    if ($result) {
        $redirect=urldecode($referer);
        pageheader($superCage->post->getRaw('title'), "<meta http-equiv=\"refresh\" content=\"3;url=$redirect\" />");
        msg_box($lang_minicms['minicms'], $lang_minicms['page_success'], $lang_common['continue'], $redirect);
        pagefooter();
        exit;
    }
}

if(isset($request['conid']) && isset($request['id']) && $request['id']=='-1' && $request['submit']==$lang_minicms['submit']) {
    $MINICMS['conid']=(int)$request['conid'];
    $MINICMS['type']=(int)$request['type'];
    $title = (isset($request['title'])) ? mysql_real_escape_string($request['title']) : $lang_minicms['article'];
    $content=mysql_real_escape_string($request['minicms_content']);
    $query="SELECT cpos FROM {$CONFIG['TABLE_CMS']} WHERE conid='{$MINICMS['conid']}' ORDER BY cpos DESC LIMIT 1";
    $result = cpg_db_query($query);
    if ($result) {
        $cms=mysql_fetch_array($result);
        mysql_free_result($result);
        $cms['cpos']+=1;
    } else {
        $cms['cpos']=0;
    }
    $query="INSERT INTO {$CONFIG['TABLE_CMS']} SET title = '$title',conid='{$MINICMS['conid']}',type='{$MINICMS['type']}',cpos='{$cms['cpos']}', content = '$content';";
    $result = cpg_db_query($query);
    if ($result) {
        $message = $lang_minicms['page_success'];
    } else {
        $message = $lang_minicms['page_fail'];
    }
    $id=mysql_insert_id();
    mysql_free_result($result);
}

if(isset($request['submit']) && $request['submit'] == $lang_minicms['preview']){
    $cms['ID'] = $request['id'];
    $cms['conid'] = $request['conid'];
    $cms['title'] = $request['title'];
    $cms['content'] = $request['minicms_content'];
    $cms['type'] = $request['type'];
    //$message = $lang_minicms['preview'];
} elseif ($request['id']=='new') {
    $cms['ID'] = -1;
    $cms['conid'] = $request['conid'];
    $cms['title'] = $lang_minicms['new_content'];
    $cms['content'] = '';
    $cms['type'] = $request['type'];
    //$message = $lang_minicms['new_content'];
}else {
    $query = "SELECT * FROM {$CONFIG['TABLE_CMS']} WHERE ID=$id";
    $result = cpg_db_query($query);
    if (!mysql_num_rows($result))
        cpg_die(CRITICAL_ERROR, $lang_minicms['non_exist'], __FILE__, __LINE__);
    $cms = mysql_fetch_array($result);
    mysql_free_result($result);
}

$cms['content'] = html_entity_decode(stripslashes($cms['content']));
$cms['message'] = (isset($message)) ? $message : '';

theme_minicms_edit($cms);

?>
