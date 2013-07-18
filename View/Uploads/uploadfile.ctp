<h2>Files</h2>
<div id="files">Loading filelist...</div>

<!-- The fileinput-button span is used to style the file input field as button -->
<span class="btn btn-success fileinput-button">
    <i class="icon-plus icon-white"></i>
    <span>Upload files...</span>
    <!-- The file input field used as target for the file upload widget -->
    <input id="fileupload" type="file" name="files[]" multiple>
</span>
<br>
<br>
<!-- The global progress bar -->
<div id="progress" class="progress progress-success progress-striped">
    <div class="bar"></div>
</div>
<!-- The container for the uploaded files -->
<div id="files" class="files"></div>



<?php
//Determine max filesize before upload
$maxfilesize = min(rtrim(ini_get('post_max_size'),'M'),rtrim(ini_get('upload_max_filesize'),'M'));
$maxfilesize = ($maxfilesize)*1024*1024;

//Load CSS
$this->start('css');
	echo $this->Html->css('/torrent_tracker/css/jquery.fileupload-ui.css');
$this->end();

//Load script
$this->start('script');
	//The jQuery UI widget factory, can be omitted if jQuery UI is already included 
	echo $this->Html->script('/torrent_tracker/js/vendor/jquery.ui.widget.js');
	//The Iframe Transport is required for browsers without support for XHR file uploads
	echo $this->Html->script('/torrent_tracker/js/jquery.iframe-transport.js');
	//The basic File Upload plugin
	echo $this->Html->script('/torrent_tracker/js/jquery.fileupload.js');

	?>
	<script>
	function showfiles(){
		<?php
		echo $this->Js->request(
	        array('action' => 'ajaxlistfiles'),
	        array(
	        	'async' => true, 
	        	'update' => '#files'
	        	)
	    );
		?>
	}
	showfiles(); //Init

	//binds to onchange event of your input field
	if (typeof(FileReader) != "undefined") { //HTML 5 support check

		$('#fileupload').bind('change', function() {

		  //this.files[0].size gets the size of your file.
		  if(this.files[0].size > <?php echo $maxfilesize; ?>){	  	  	
		  	alert('File to big, max <?php echo round($maxfilesize/1024/1024,2)." Mb"; ?> allowed');
		  	$('#fileupload').die();
		  	return false;
		  }

		});

	}

	/*jslint unparam: true */
	/*global window, $ */
	$(function () {
	    'use strict';
	    // Change this to the location of your server-side upload handler:
	    var url = '<?php echo $this->Html->url(array("plugin"=>"torrent_tracker","controller"=>"uploads","action"=>"ajaxupload"));?>';
	    $('#fileupload').fileupload({
	        url: url,
	        dataType: 'json',
	        maxFileSize: 5000000, // 5 MB
	        done: function (e, data) {
	            showfiles(); //Refresh list
	        },
	        progressall: function (e, data) {
	            var progress = parseInt(data.loaded / data.total * 100, 10);
	            $('#progress .bar').css(
	                'width',
	                progress + '%'
	            );
	        }
	    }).prop('disabled', !$.support.fileInput)
	        .parent().addClass($.support.fileInput ? undefined : 'disabled');
	});
	
	</script>
<?php
$this->end();
?>