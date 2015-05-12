<?php

function title($title) {
	return '<div class="title">'.$title.'</div>';
}
function content($content, $style=false) {
	return '<div class="content'.($style ? ' '.$style : '').'">'.$content.'</div>';
}

?>