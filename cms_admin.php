<?php
/**************************************************
  CPG MiniCMS Plugin for Coppermine Photo Gallery
***************************************************/
require_once('include/init.inc.php');

$req_array=array('referer','up','submit','down','delete','id','id2','cpos','title');

$superCage = Inspekt::makeSuperCage($strict);    
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
    $referer = urlencode($request['referer']);
    if (strpos($referer, "http") !== false) {
      $referer = urlencode("index.php?file=minicms/cms_admin");
    }
} else {
    $referer = urlencode("index.php?file=minicms/cms_admin");
}

if(isset($request['submit'])){
    $title=addslashes($request['title']);
    // content "added" from cms_admin goes to the default 'section'.
    $query="SELECT cpos FROM {$CONFIG['TABLE_CMS']} WHERE conid='0' and type='3' ORDER BY cpos DESC LIMIT 1";
    $result = cpg_db_query($query);
    if ($result) {
        $cms=mysql_fetch_array($result);
        mysql_free_result($result);
        $cms['cpos']+=1;
    } else {
        $cms['cpos']=0;
    }
    $query="INSERT INTO {$CONFIG['TABLE_CMS']} SET title = '$title',conid='0',cpos='{$cms['cpos']}',type='3'";
    $result = cpg_db_query($query);
    if ($result) {
        $message = $lang_minicms['page_success'];
    } else {
        $message = $lang_minicms['page_fail'];
    }
    mysql_free_result($result);
}

if(isset($request['delete'])) {
    $id = (int)$request['id'];
    $query="DELETE FROM {$CONFIG['TABLE_CMS']} WHERE ID = '$id'";
    $result=cpg_db_query($query);
    if ($result) {
        $message = $lang_minicms['page_success'];
    } else {
        $message = $lang_minicms['page_fail'];
    }
    if (isset($referer)) {
        $redirect=urldecode($referer);
        pageheader($lang_minicms['minicms'], "<meta http-equiv=\"refresh\" content=\"3;url=$redirect\" />");
        msg_box($lang_minicms['minicms'], $message, $lang_common['continue'], $redirect);
        pagefooter();
        exit;
    }
}

if (isset($request['up']) || isset($request['down'])) {
    if (isset($request['id']) && isset($request['id2']) && isset($request['cpos'])) {
        $id = (int)$request['id'];
        $id2 = (int)$request['id2'];
        $cpos = (int)$request['cpos'];
        if ($cpos<0) $cpos=1; //fixes negative positions that shouldn't happen anymore but may be in the db
        $cpos2 = (isset($request['down'])) ? $cpos+1 : $cpos-1;

        cpg_db_query("UPDATE {$CONFIG['TABLE_CMS']} SET cpos='$cpos2' WHERE ID = '$id' LIMIT 1");
        cpg_db_query("UPDATE {$CONFIG['TABLE_CMS']} SET cpos='$cpos' WHERE ID = '$id2' LIMIT 1");

        $message = $lang_minicms['page_success'];
    } else {
        $message = $lang_minicms['no_change'];
    }

    if (isset($referer)) {
        $redirect=urldecode($referer);
        $message = ($message) ? $message : $lang_common['continue'];
        pageheader($request['title'], "<meta http-equiv=\"refresh\" content=\"3;url=$redirect\" />");
        msg_box($lang_minicms['minicms'], $message, $lang_common['continue'], $redirect);
        pagefooter();
        exit;
    }
}

$query = "SELECT * FROM {$CONFIG['TABLE_CMS']} ORDER BY type, conid ,cpos";
$result = cpg_db_query($query);
/* if (!mysql_num_rows($result)) //render error instead of blank page.
    cpg_die(CRITICAL_ERROR, $lang_minicms['non_exist'], __FILE__, __LINE__);
*/
pageheader($lang_minicms['minicms']);
starttable("100%", $lang_minicms['admin_title'] , 5);
if(isset($message)) print "<tr><td colspan=\"4\" align=center><h2>{$message}</h2></td></tr>";
print <<<EOT
  <tr>
    <th class="tableh2" width="80">{$lang_minicms['action']}</th>
    <th class="tableh2" >{$lang_minicms['article']}</th>
    <th class="tableh2" >{$lang_minicms['type']}</th>
    <th class="tableh2" >{$lang_minicms['content']}</th>
    <th class="tableh2" >{$lang_minicms['cpos']}</th>
  </tr>
EOT;

while ($cms = mysql_fetch_assoc($result)) {
    $cms_array[]=$cms;
}
mysql_free_result($result);
$counter=0;
foreach ($cms_array as $key => $cms) {
    $cms['next_ID']=($counter<count($cms_array)-1 && $cms['type']==$cms_array[$counter+1]['type'] && $cms['conid']==$cms_array[$counter+1]['conid'] ) ? '&amp;id2='.$cms_array[$counter+1]['ID'] : '';
    $cms['prev_ID']=($counter>0 && $cms['type']==$cms_array[$counter-1]['type'] && $cms['conid']==$cms_array[$counter-1]['conid']) ? '&amp;id2='.$cms_array[$counter-1]['ID'] : '';
    $type = "{$MINICMS['conType'][$cms['type']]}";
    $conid = "{$cms['conid']}";
    switch($type) {
        case 'cat':
            $context_url = "index.php?cat=$conid";
            break;
        case 'thumb':
            $context_url = "thumbnails.php?album=$conid";
            break;
        case 'img':
            $context_url = "displayimage.php?pos=-$conid";
            break;
        case 'section';
            $context_url = "index.php?file=minicms/cms&amp;conid=$conid&amp;type={$cms['type']}";
            break;
    }
    print <<<EOT
    <tr>
      <td class="tableb" style="white-space:nowrap;">
        <a title="{$lang_minicms['delete']}" href="index.php?file=minicms/cms_admin&amp;delete&amp;id={$cms['ID']}"><img src="images/icons/delete.png" border="0" alt="{$lang_minicms['delete']}" /></a>
        <a title="{$lang_minicms['pos_up']}" href="index.php?file=minicms/cms_admin&amp;up&amp;id={$cms['ID']}{$cms['prev_ID']}&amp;cpos={$cms['cpos']}"><img src="images/icons/up.png" border="0" alt="{$lang_minicms['pos_up']}" /></a>
        <a title="{$lang_minicms['pos_down']}" href="index.php?file=minicms/cms_admin&amp;down&amp;id={$cms['ID']}{$cms['next_ID']}&amp;cpos={$cms['cpos']}"><img src="images/icons/down.png" border="0" alt="{$lang_minicms['pos_down']}" /></a>
        <a title="{$lang_minicms['edit']}" href="index.php?file=minicms/cms_edit&amp;id={$cms['ID']}&amp;referer={$referer}"><img src="images/icons/edit.png" border="0" alt="{$lang_minicms['edit']}" /></a>
      </td>
      <td class="tableb" ><a href="index.php?file=minicms/cms&amp;id={$cms['ID']}" title="{$cms['title']}">{$cms['title']}</a></td>
      <td class="tableb" width="40"><a href="index.php?file=minicms/cms&amp;conid={$cms['conid']}&amp;type={$cms['type']}" title="{$MINICMS['conType'][$cms['type']]}: {$cms['conid']}" >{$MINICMS['conType'][$cms['type']]}</a></td>
      <td class="tableb" ><a href="$context_url">{$cms['conid']}</a></td>
      <td class="tableb" width="10">{$cms['cpos']}</td>
    </tr>
EOT;
$counter++;
}
Print <<<EOT
    <tr>
        <td colspan="5" class="tableb">
            <form method="post" action="index.php?file=minicms/cms_admin">
                {$lang_minicms['new_content']} {$lang_minicms['title']}: <input type="text" name="title" style="width: 200px; margin-top: 15px;" />
                <input type="submit" name="submit" value="{$lang_minicms['add']}" />
            </form>
        </td>
    </tr>
EOT;
endtable();
pagefooter();
mysql_free_result($result);
ob_end_flush();
?>
