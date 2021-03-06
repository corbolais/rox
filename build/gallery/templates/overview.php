<?php

$words = new MOD_words();
$layoutbits = new MOD_layoutbits();
$thumbsize = $this->thumbsize;
if ($statement) {
    $request = PRequest::get()->request;
    $requestStr = implode('/', $request);
    $matches = array();
    if (preg_match('%/=page(\d+)%', $requestStr, $matches)) {
        $page = $matches[1];
        $requestStr = preg_replace('%/=page(\d+)%', '', $requestStr);
    } else {
        $page = 1;
    }
    if (!isset($itemsPerPage)) $itemsPerPage = 12;
    $p = PFunctions::paginate($statement, $page, $itemsPerPage);
    $statement = $p[0];

    $pages = $p[1];
    $maxPage = $p[2];
    $currentPage = $page;
    $request = $requestStr.'/=page%d';
        require 'pages.php';

    echo '<div class="row">';
    foreach ($statement as $d) {
    	echo '<div class="col-12 col-md-6 col-lg-4">';
    $title_short = ((strlen($d->title) >= 26) ? substr($d->title,0,20).'...' : $d->title);
    echo '<a href="gallery/img?id='.$d->id.'" data-toggle="lightbox" data-type="image"><img class="framed w-100" src="gallery/thumbimg?id='.$d->id.($thumbsize ? '&t='.$thumbsize : '').'" alt="image"></a>';

    echo '<div class="w-100 bg-white h6 px-2">';
    $loggedmember = isset($this->model) ? $this->model->getLoggedInMember : $this->loggedInMember;
    if ($loggedmember && $loggedmember->Username == $d->user_handle) {
        echo '<input type="checkbox" class="input_check mr-2" name="imageId[]" value="'.$d->id.'">';
        echo '<a href="gallery/img?id='. $d->id .'" title="'. $d->title .'" data-toggle="lightbox" data-type="image">'. $title_short . '</a>';
        echo '<a href="gallery/show/image/'.$d->id.'"><i class="fa fa-edit float-right pt-1"></i></a>';
        echo '<div class="d-inline"><p class="small float-left pt-2">'.$layoutbits->ago(strtotime($d->created)).'</p>';
        echo '<a href="gallery/show/image/'. $d->id .'/delete" title="DELETE '. $d->title .'" class="btn btn-sm btn-danger float-right"><i class="fa fa-trash"></i></a></div>';
    }
        echo '</div>';
        echo '</div>';
    }
    echo '</div>';

    require 'pages.php';
}
?>
<script type="text/javascript">
// late_loader.queueObjectMethod('common', 'highlightMe');
// late_loader.queueObjectMethod('common', 'checkAll');
// late_loader.queueObjectMethod('common', 'selectAll');
// late_loader.queueObjectMethod('common', 'checkEmpty');

function toggle(source) {
    checkboxes = document.getElementsByName('imageId[]');
    for(var i=0, n=checkboxes.length;i<n;i++) {
        checkboxes[i].checked = source.checked;
    }
}
</script>