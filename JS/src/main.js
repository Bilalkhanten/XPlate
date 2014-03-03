var config = {
  'Init': function Init() {
    $('#configsubmit').click(function(e) { // Handle the Configuration forms "Submit" event
      e.preventDefault();
      $.ajax({
        url: 'Function/config.php',
        data: $('#formconfig').serialize(),
        type: 'POST',
        dataType: 'json',
        success: function(x) {
          if (x.status == 'success') {
            location.href = './';
          }
        },
        error: function() {}
      });
    });
    $('#configcancel').click(function(e) { // Handle the Configuration forms "Cancel" event
      e.preventDefault();
      location.href = './';
    });
  }
};
var generate = {
  'i': 0, // Counter for the second character
  'j': 0, // Counter for the third character (only when necessary)
  'n': 0, // Counter for the increment function
  'progress': 0, // The progress returned by the generate script
  'timer': 0,
  'Init': function Init() {
    if ($('.generate').val() == 1 || parseInt($('.total').html()) === 0) {
      setInterval(generate.IncrementProgress, 100);
      generate.Generate();
    }
  },
  'Generate': function Generate() {
    var postdata = (generate.j === null) ? 'i=' + generate.i : 'i=' + generate.i + '&j=' + generate.j;
    $.ajax({
      url: 'Function/generate.php',
      data: postdata,
      type: 'POST',
      dataType: 'json',
      success: function(x) {
        if (x.status == 'success') {
          generate.i = parseInt(x.i);
          generate.j = parseInt(x.j);
          generate.progress += 26000;
          generate.Generate();
        } else {
          generate.progress += 26000;
          location.href = './';
        }
      },
      error: function() {}
    });
  },
  'IncrementProgress': function IncrementProgress() {
    // Calculate the percentage
    var percent = generate.n / 17576000 * 100;
    $('.progress').width(percent + '%');
    $('.progress-percent').html(Math.round(percent) + '%');
    $('.total').html(generate.InsertComma(generate.n));
    if (generate.progress < 17576000) {
      $('.timer').html(generate.ConvertTime(generate.timer));
      generate.timer += 100;
    } else {
      $('.timer').html('Done!');
    }
    if (generate.n < generate.progress && generate.progress > 0) {
      generate.n = (generate.n + 22750 <= 17576000) ? generate.n + 22750 : 17576000;
    }
  },
  'InsertComma': function InsertComma(num) {
    num += '';
    x = num.split('.');
    x1 = x[0];
    x2 = x.length > 1 ? '.' + x[1] : '';
    var regx = /(\d+)(\d{3})/;
    while (regx.test(x1)) {
      x1 = x1.replace(regx, '$1' + ',' + '$2');
    }
    return x1 + x2;
  },
  'ConvertTime': function ConvertTime(ms) {
    var min = 0,
      sec = 0;
    if (ms >= 60000) {
      min = (ms - ms % 60000) / 60000;
      ms = ms % 60000;
    }
    if (ms >= 1000) {
      sec = (ms - ms % 1000) / 1000;
      ms = ms % 1000;
    }
    if (min < 10) min = "0" + min;
    if (sec < 10) sec = "0" + sec;
    if (!ms) ms = "000";
    return min + ":" + sec + "." + ms;
  }
};
var rebuild = {
  'post': null,
  'Init': function Init() {
    $(".dialog-rebuild").dialog({
      modal: true,
      width: 540,
      autoOpen: false,
      open: function() {
        $.get('Function/secure.php?ms=25', function(txt) {
          rebuild.post = {
            hts: txt
          };
        });
      },
      buttons: [{
        text: "Yes, I know what I'm doing...",
        click: function() {
          $.ajax({
            url: 'Function/rebuild.php',
            data: rebuild.post,
            type: 'POST',
            dataType: 'json',
            success: function(x) {
              if (x.status == 'success') {
                location.href = './';
              }
            },
            error: function() {}
          });
        }
      }, {
        text: "On second thought",
        click: function() {
          $(this).dialog("close");
        }
      }]
    });
    $('.masthead-rebuild').click(function(e) {
      e.preventDefault();
      $(".dialog-rebuild").dialog("open");
    });
  }
};
$(function() {
  // Document is ready
  config.Init();
  generate.Init();
  rebuild.Init();
});