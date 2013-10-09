var fakeDownloadInterval = null;
function fakeDownloadProgress() {
  var $progressBar = $('progress#download-progress-bar');

  if ($progressBar.val() >= 50) {
    $('.progress-item.second').css({
      opacity:    1,
      transition: 'opacity 1s ease-in-out'
    });
    $('.progress-dot.second').attr('src', 'orcircle.png');
  }

  if ($progressBar.val() == 100) {
    clearInterval(fakeDownloadInterval);
  } else {
    console.log($progressBar.val());
    $progressBar.val(97 - (97 - $progressBar.val()) / 1.03);
  }
}

function updateProgress(progress){
  var $progressBar = $('progress#download-progress-bar');
  $progressBar.val(progress);

  if (progress >= 50) {
    $('.progress-item.second').css({
      opacity:    1,
      transition: 'opacity 1s ease-in-out'
    });
    $('.progress-dot.second').attr('src', 'orcircle.png');
  }
};

// 1. Click on download
$('.download-action').click(function(e){
  e.preventDefault();
  populateDownloadTable();
  $(this).hide();
  $('.downloadTableDiv').css("visibility", "visible");
});

// 2. Select a file to download
$(document.body).on ( "click" , '.js-download-file', function(filename){
 
  var filename = $(this).find('a').data('url');
  $('.actions').slideUp(500, function(){
    $('.js-download-progress').fadeIn();

    $.ajax({
      url: "download.php?filename="+filename,
		type: "GET",
        beforeSend: function(xhr) {
          console.log("Start the progress bar");
          fakeDownloadInterval = setInterval(fakeDownloadProgress, 130);
        }
    }).done(function ( data ) {
      $('.progress-item.third').css({
        opacity:    1,
      transition: 'opacity 1s ease-in-out'
      });
      $('.progress-dot.third').attr('src', 'orcircle.png');

      var $progressBar = $('progress#download-progress-bar');
      $progressBar.val(100);
      window.setTimeout(function(){ window.open(data); }, 1000);
    });
  });
});
