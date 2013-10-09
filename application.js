function updateProgress(progress){
  var $progressBar = $('progress#p');
  $progressBar.val(progress);

  if (progress >= 50) {
    $('.progress-item.second').css({
      opacity:    1,
      transition: 'opacity 1s ease-in-out'
    });
    $('.progress-dot.second').attr('src', 'orcircle.png');
  }
};

function populateDownloadTable() {
	
  var downloadTable = document.getElementById("downloadTable");
  var storedFiles = localStorage.getItem('storedFiles');
	if (storedFiles == null) {
		storedFiles = [];
	} else {
		storedFiles = JSON.parse(storedFiles);
  }

	for(var i=0;i<storedFiles.length;i++) {
    var filename = storedFiles[i];
		var row=downloadTable.insertRow(1);
		var cell1=row.insertCell(0);
		var cell2=row.insertCell(1);

		row.className = "tableRow";
		cell1.innerHTML= "&nbsp;" + filename;
		cell1.className = "tableEntry";
		cell2.innerHTML='<a href="#" data-url="'+filename+'">Select</a>';
		cell2.className = "tableEntry tableEntrySelect js-download-file";
	}
}

// 1. Click on Upload
$('.js-show-uploader').click(function(e){
  e.preventDefault();
  $(this).hide();
  $('.js-file-uploader').show();
});

// 2. File Selected
$('.upload-file').change(function(){
  var filename = $(this).val().replace(/^.*[\\\/]/, '');

  $('.js-submit-filename').text(filename);
  $('.js-file-uploader').hide();
  $('.js-upload-submit').show();
});

// 3. Click Upload
$('.js-submit-file').click(function(){
  $('.actions').slideUp(500, function(){
    $('.js-upload-progress').fadeIn();
    // interval = setInterval(updateProgress, 50);
  });
});

var fakeinterval = null;
function fakeprogress() {
  var $progressBar = $('progress#p');
  if ($progressBar.val() == 100) {
    clearInterval(fakeinterval);
  } else {
	  $progressBar.val(97 - (97 - $progressBar.val()) / 1.03);
	}
}

$('input.upload-file').fileupload({
  dataType: 'text',
  add: function (e, data) {
    console.log("Attaching click handler to button");

    data.context = $('.js-submit-file').click(function() {
      data.submit();
    });
  },
  progress: function (e, data) {
    var progress = parseInt(data.loaded / data.total * 50, 10);
    console.log(progress);
	 var $progressBar = $('progress#p');
	 if ($progressBar.val() <= 50) { 
    	updateProgress(progress);
	 }

    if ((data.loaded / data.total) > 0.99 && fakeinterval == null) {
      fakeinterval = setInterval(fakeprogress, 130);
    }
  },
  done: function (e, data) {
    console.log("Done!");
    console.log(data);
    var $progressBar = $('progress#p');
    $progressBar.val(100);

    $('.progress-item.third').css({
      opacity:    1,
      transition: 'opacity 1s ease-in-out'
    });
    $('.progress-dot.third').attr('src', 'orcircle.png');
  
    $('.js-upload-progress').delay(3600).fadeOut(250);
	 $('.upload-complete').delay(3900).slideDown(500, function(){ 
  	 }).delay(800).fadeOut(300, function() { window.location.reload(); }).hide();

	 var filename = $('.js-submit-filename').text();
	 var storedFiles = localStorage.getItem('storedFiles');
	 if (storedFiles == null) {
		storedFiles = [];
	 } else {
		storedFiles = JSON.parse(storedFiles);
    }
   
	 if (jQuery.inArray(filename, storedFiles) == -1) {
		 storedFiles.push(filename);
	    localStorage.setItem('storedFiles', JSON.stringify(storedFiles));
	 }

    // interval = setInterval(updateProgress, 50);
  },
  fail: function (e, data) {
    console.log("FAIL");
    console.log(e);
    console.log(data);
  }
});

$('form#upload-form').submit(function(e){
  e.preventDefault();
});

